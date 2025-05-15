<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class SepayController extends Controller
{
    public static function create($cart, $payment, $currency): ?array
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'SePay')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        if ($currency != "VND") {
            $system_currency = Currency::query()->where("name", Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where("name", "VND")->first();
            $payment->price = round(Controller::toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => round($payment->price),
                'currency' => "VND"
            ]);
        }

        $cart->update([
            'is_active' => 0
        ]);

        $qrcode = "https://qr.sepay.vn/img?acc={$config['bank_account']}&template=compact&bank={$config['bank']}&amount={$payment->price}&des={$config['paycode_prefix']}{$payment->id}";
        $details = [
            'payment_method' => 'SePay',
            'sepay_bank' => $config['bank'],
            'sepay_bank_owner' => $config['bank_owner'],
            'sepay_bank_account' => $config['bank_account'],
            'sepay_paycode_prefix' => $config['paycode_prefix'] . $payment->id,
            'payment_price' => round($payment->price) . ' ' . $payment->currency,
            'qrcode' => $qrcode,
            'payment_id' => $payment->id
        ];

        return [
            'type' => 'qrcode',
            'details' => $details
        ];
    }

    public function handle(Request $request)
    {
        Log::error('Sepay handle request: ' . json_encode($request->getContent(), true));
        $paymentMethod = PaymentMethod::query()->where('name', 'SePay')->first();
        if (!$paymentMethod->enable) {
            return response()->json(['error' => 'Phương thức thanh toán này không được kích hoạt.'], 403);
        }
        $config = json_decode($paymentMethod->config, true);

        $data = $request->post();

        // Get Authorization header
        $apiKeyHeader = $request->header('Authorization');

        // Check if Authorization header is provided
        if (!$apiKeyHeader) {
            return response()->json(['error' => 'Không có tiêu đề Authorization được cung cấp.'], 401);
        }

        // Check if Authorization header is correct
        if ($apiKeyHeader !== 'Apikey ' . $config['webhook_apikey']) {
            return response()->json(['error' => 'Sai mã webhook apikey.'], 403);
        }

        // Get the corresponding transaction
        $payment_id = str_replace($config['paycode_prefix'], '', $data['code']);
        $payment = Payment::where('id', $payment_id)->first();
//        Log::error('Sepay paycode prefix: ' . $config['paycode_prefix']);
//        Log::error('Sepay payment id: ' . $payment_id);
//        Log::error('Sepay payment: ' . $payment);

        // Check if the payment exists
        if (!$payment) {
            return response()->json(['error' => 'Giao dịch không tồn tại.'], 403);
        }

        // Check if the payment amount is correct
        $transferAmount = (int) $data['transferAmount'];
        if (abs($payment->price - $transferAmount) > 0.0001) {
            return response()->json(['error' => 'Số tiền không khớp.'], 403);
        }

        $paymentController = new PaymentController();
        $paymentController->FinalHandler($payment_id);

        return response()->json(
            [
                'success' => true,
                'message' => 'Giao dịch thành công id: '. $payment_id
            ], 201
        );
    }

    public function check(Request $request)
    {
        $data = $request->post();

        // Check if pmid is transmitted
        if(empty($data['pmid'])) return response()->json([
            'success' => 'false',
            'error' => 'Không có gì để check.'
        ], 403);

        $payment_id = $data['pmid'];
        $payment = Payment::where('id', $payment_id)->first();

        // Check if transaction exists
        if (!$payment) {
            return response()->json([
                'success' => 'false',
                'error' => 'Giao dịch không tồn tại.'
            ], 403);
        }

        if ($payment->status !== 1) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Giao dịch chờ thanh toán id: '.$payment_id
                ], 201
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Giao dịch thành công id: '.$payment_id
            ], 201
        );
    }
}
