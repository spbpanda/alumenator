<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhonePe\common\PhonePeClient;
use PhonePe\Env;
use PhonePe\payments\v1\models\request\builders\InstrumentBuilder;
use PhonePe\payments\v1\models\request\builders\PgPayRequestBuilder;
use PhonePe\payments\v1\PhonePePaymentClient;

class PhonepeController extends Controller
{
    public static function create($cart, $payment, $currency): ?array
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'PhonePe')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        if ($currency !== "INR") {
            $system_currency = Currency::query()->where("name", Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where("name", "INR")->first();
            $payment->price = round(Controller::toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => round($payment->price),
                'currency' => "INR"
            ]);
        }

        $cart->update([
            'is_active' => 0
        ]);

        $phonePePaymentsClient = new PhonePePaymentClient($config['merchant_id'], $config['salt_key'], $config['salt_index'], Env::PRODUCTION, true);

        $merchantTransactionId = 'PHONEPE-' . \Str::random(10) . '-' . $payment->id;
        $payment->update([
            'transaction' => $merchantTransactionId
        ]);

        $details = [
            'amount' => (int)round(($payment->price * 100), 2), // Amount in paise
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => $payment->user_id,
            'redirectUrl' => 'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
            'redirectMode' => 'REDIRECT',
            'callbackUrl' => 'https://'. request()->getHost() .'/api/payments/phonepe/handle',
        ];

        $request = PgPayRequestBuilder::builder()
            ->callbackUrl($details['callbackUrl'])
            ->merchantId($config['merchant_id'])
            ->merchantUserId($details['merchantUserId'])
            ->amount($details['amount'])
            ->merchantTransactionId($details['merchantTransactionId'])
            ->redirectUrl($details['redirectUrl'])
            ->redirectMode($details['redirectMode'])
            ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
            ->build();

        try {
            $response = $phonePePaymentsClient->pay($request);
        } catch (\Exception $e) {
            Log::error('PhonePe payment error: ' . $e->getMessage());
            return [
                'type' => 'error',
                'error' => $e->getMessage()
            ];
        }

        $url = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();

        return [
            'type' => 'url',
            'url' => $url
        ];
    }

    public function handle(Request $request)
    {
        Log::error('PhonePe Handle Request: ' . json_encode($request->getContent(), true));
        $paymentMethod = PaymentMethod::query()->where('name', 'PhonePe')->first();
        if (!$paymentMethod->enable) {
            return response()->json(['error' => 'Payment method is not enabled.'], 403);
        }
        $config = json_decode($paymentMethod->config, true);

        $data = $request->post();

        // Get x_verify header
        $x_verify = $request->header('x_verify');

        // Check if Authorization header is provided
        if (!$x_verify) {
            return response()->json(['error' => 'Not passed security check.'], 401);
        }

        // Check if response is provided
        if (!$request->response) {
            return response()->json(['error' => 'No response.'], 403);
        }

        // Initialize callback validation
        $phonePeClient = new PhonePeClient($config['merchant_id'], $config['salt_key'], $config['salt_index'], Env::PRODUCTION);
        $validation = $phonePeClient->verifyCallback($request->response, $x_verify);

        // Check if the validation is passed
        if (!$validation) {
            return response()->json(['error' => 'Failed security check.'], 403);
        }

        if ($data['state'] == 'FAILED') {
            $payment_id = $data['merchantTransactionId'];
            $payment = Payment::where('id', $payment_id)->first();

            if (!$payment) {
                return response()->json(['error' => 'Transaction not found.'], 403);
            }

            $payment->update([
                'status' => Payment::ERROR,
                'transaction' => $data['transactionId']
            ]);
        }

        if ($data['state'] !== 'COMPLETED') {
            return response()->json(['error' => 'Transaction not completed.'], 403);
        }

        $payment_id = $data['merchantTransactionId'];
        $payment = Payment::where('transaction', $payment_id)->first();

        // Check if the payment exists
        if (!$payment) {
            return response()->json(['error' => 'Transaction not found.'], 403);
        }

        // Check if the payment amount is correct
        $transferAmount = (int)$data['amount'];
        $paymentPrice = (int)round(($payment->price * 100), 2);

        if ($transferAmount - $paymentPrice > 0.0001) {
            return response()->json(['error' => 'Amount mismatch.'], 403);
        }

        $paymentController = new PaymentController();
        $paymentController->FinalHandler($payment->id);

        abort(200, 'OK');
    }
}
