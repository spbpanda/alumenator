<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App\Helpers\ChargeHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Models\CartItem;
use App\Models\Chargeback;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Subscription;
use Carbon\Carbon;
use Fahim\PaypalIPN\PaypalIPNListener;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayPalIPNController extends Controller
{
    protected PaymentController $paymentController;

    public function __construct()
    {
        $this->paymentController = new PaymentController();
    }

    public static function create($cart, $payment, $currency, $isSubs): ?array
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'PayPalIPN')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
        if ($system_currency->name != $config['paypal_currency_code'] || $currency != $config['paypal_currency_code']) {
            $currencyRate = Currency::query()->where('name', $config['paypal_currency_code'])->first();
            $payment->price = round(Controller::toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => $config['paypal_currency_code'],
            ]);
        }

        if ($isSubs) {
            $itemData = Item::where('id', CartItem::where('cart_id', $cart->id)->first()->item_id)->select('id', 'name', 'chargePeriodUnit', 'chargePeriodValue')->first();
            $period = ChargeHelper::GetChargeDays($itemData->chargePeriodUnit, $itemData->chargePeriodValue);

            Subscription::create([
                'payment_id' => $payment->id,
                'sid' => null,
                'status' => Subscription::PENDING,
                'interval_days' => $period,
                'renewal' => Carbon::now()->addDays($period)->format('Y-m-d'),
            ]);

            return [
                'type' => 'html',
                'html' => '<form id=pay name=pay action="https://'.($config['test'] == '1' ? 'sandbox' : 'www').'.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_xclick-subscriptions">
                    <input type="hidden" name="business" value="'.$config['paypal_business'].'">
                    <input type="hidden" name="item_name" value="'.$itemData->name.' ('. request()->getHost() .')">
                    <input type="hidden" name="item_number" value="'.$itemData->id.'">
                    <input type="hidden" name="no_note" value="1">
                    <input type="hidden" name="src" value="1">
                    <input type="hidden" name="a3" value="'.$payment->price.'">
                    <input type="hidden" name="p3" value="'.$period.'">
                    <input type="hidden" name="t3" value="D">
                    <input type="hidden" name="sra" value="1">
                    <input type="hidden" name="custom" value="'.$payment->id.'">
                    <input type="hidden" name="currency_code" value="'.$config['paypal_currency_code'].'">
                    <input type="hidden" name="return" value="https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id .'">
                    <input type="hidden" name="cancel_return" value="https://'. request()->getHost() .'/error">
                    <input type="hidden" name="notify_url" value="https://'. request()->getHost() .'/api/payments/handle/paypalIPN">
                    <input type="submit" value="PayPal">
                </form>',
            ];
        } else {
            $cartItem = CartItem::where('cart_id', $payment->cart->id)->get();
            $itemData = Item::where('id', CartItem::where('cart_id', $cart->id)->first()->item_id)->select('name')->first();
            $manyItemsText = count($cartItem) > 1 ? ' and others' : '';

            return [
                'type' => 'html',
                'html' => '<form id=pay name=pay action="https://'.($config['test'] == '1' ? 'sandbox' : 'www').'.paypal.com/cgi-bin/webscr" method="post">
                  <input type="hidden" name="cmd" value="_xclick">
                  <input type="hidden" name="no_shipping" value="1">
                  <input type="hidden" name="item_name" value="Purchasing at '. request()->getHost() .': '. $itemData->name . $manyItemsText.'">
                  <input type="hidden" name="item_number" value="'.$payment->id.'">
                  <input type="hidden" name="currency_code" value="'.$config['paypal_currency_code'].'">
                  <input type="hidden" name="amount" value="'.$payment->price.'">
                  <input type="hidden" name="business" value="'.$config['paypal_business'].'">
                  <input type="hidden" name="custom" value="'.$payment->id.'">
                  <input type="hidden" name="return" value="https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id .'">
                  <input type="hidden" name="cancel_return" value="https://'. request()->getHost() .'/error">
                  <input type="hidden" name="notify_url" value="https://'. request()->getHost() .'/api/payments/handle/paypalIPN">
                  <input type="submit" value="PayPal">
                </form>',
            ];
        }
    }

    public function handle(Request $r)
    {
        try {
            Log::info('PayPal IPN received', [
                'txn_type' => $r->input('txn_type'),
                'payment_status' => $r->input('payment_status'),
                'txn_id' => $r->input('txn_id'),
                'custom' => $r->input('custom')
            ]);

            $paymentMethod = PaymentMethod::query()->where('name', 'PayPalIPN')->first();
            if (!$paymentMethod->enable) {
                Log::error('PayPalIPN Error: Payment method disabled');
                abort(403, 'Payment method disabled');
            }

            $config = json_decode($paymentMethod->config, true);

            $raw_post_data = file_get_contents('php://input');
            $raw_post_array = explode('&', $raw_post_data);
            $myPost = [];
            foreach ($raw_post_array as $keyval) {
                $keyval = explode('=', $keyval);
                if (count($keyval) == 2) {
                    $myPost[$keyval[0]] = urldecode($keyval[1]);
                }
            }

            if (!isset($myPost['receiver_email']) ||
                mb_strtolower($myPost['receiver_email']) !== mb_strtolower($config['paypal_business'])) {
                Log::error('PayPalIPN Error: Emails not matching', [
                    'received' => mb_strtolower($myPost['receiver_email'] ?? ''),
                    'expected' => mb_strtolower($config['paypal_business'])
                ]);
                abort(403, 'Unauthorized access');
            }

            if (strstr($myPost['txn_type'] ?? '', 'subscr') !== false) {
                return $this->handleSubscription($myPost, $config);
            }

            $ipn = new PaypalIPNListener();
            $ipn->use_sandbox = ($config['test'] == '1');

            if (!$ipn->processIpn()) {
                Log::error('PayPal IPN verification failed');
                abort(400, 'IPN verification failed');
            }

            return $this->handleRegularPayment($myPost);

        } catch (\Exception $e) {
            Log::error('PayPal IPN Handler error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Internal server error');
        }
    }

    private function handleSubscription(array $myPost, array $config)
    {
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }

        foreach ($myPost as $key => $value) {
            $value = urlencode($value);
            $req .= "&$key=$value";
        }

        $ch = curl_init('https://' . ($config['test'] == '1' ? 'sandbox' : 'www') . '.paypal.com/cgi-bin/webscr');
        if (!$ch) {
            Log::error('Failed to initialize CURL');
            abort(500, 'Failed to initialize CURL');
        }

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Connection: Close', 'User-Agent: minestorecms']);

        $res = curl_exec($ch);
        if (!$res) {
            Log::error('Failed to execute CURL request', ['error' => curl_error($ch)]);
            abort(500, 'Failed to execute CURL request');
        }

        $tokens = explode("\r\n\r\n", trim($res));
        $res = trim(end($tokens));

        if (strcmp($res, 'VERIFIED') == 0 || strcasecmp($res, 'VERIFIED') == 0) {
            switch ($myPost['txn_type']) {
                case 'subscr_signup':
                    Subscription::where('payment_id', $myPost['custom'])->update([
                        'sid' => $myPost['subscr_id'],
                        'payment_method' => 'paypalipn',
                        'status' => Subscription::ACTIVE,
                    ]);
                    break;

                case 'subscr_payment':
                    return $this->handleSubscriptionPayment($myPost);

                case 'subscr_modify':
                    break;

                case 'subscr_eot':
                    break;

                case 'recurring_payment_profile_cancel':
                case 'recurring_payment_suspended':
                case 'mp_cancel':
                case 'subscr_failed':
                case 'subscr_cancel':
                    return $this->handleSubscriptionCancel($myPost);

                default:
                    Log::error('paypalIPNHandle subs default: ' . json_encode($myPost));
            }
        } else {
            abort(400, 'Subscription verification failed');
        }

        return http_response_code(200);
    }

    private function handleSubscriptionPayment(array $myPost)
    {
        $subscr = Subscription::where('sid', $myPost['subscr_id'])->first();
        if (!$subscr) {
            Log::error('Subscription not found', ['sid' => $myPost['subscr_id']]);
            abort(404, 'Subscription not found');
        }

        $item_amount = (float) $myPost['mc_gross'];
        $paymentData = Payment::where('id', $subscr->payment_id)
            ->select('currency', 'price')
            ->first();

        if (!$paymentData) {
            Log::error('Payment not found', ['payment_id' => $subscr->payment_id]);
            abort(404, 'Payment not found');
        }

        if (strtoupper($myPost['mc_currency']) != strtoupper($paymentData->currency)
            || $item_amount < $paymentData->price) {
            abort(400, 'Invalid payment amount or currency');
        }

        DB::transaction(function() use ($subscr, $myPost) {
            Payment::where('id', $subscr->payment_id)->update([
                'transaction' => $myPost['subscr_id'],
            ]);

            $subscr->update([
                'status' => Subscription::ACTIVE,
                'count' => $subscr->count + 1,
                'renewal' => Carbon::now()->addDays($subscr->interval_days)->format('Y-m-d'),
            ]);

            if ($subscr->count > 1) {
                $this->paymentController->RenewHandler($subscr->payment_id);
            } else {
                $this->paymentController->FinalHandler($subscr->payment_id);
            }
        });

        return http_response_code(200);
    }

    private function handleSubscriptionCancel(array $myPost)
    {
        DB::transaction(function() use ($myPost) {
            Subscription::where('payment_id', $myPost['custom'])->update([
                'status' => Subscription::CANCELLED,
            ]);

            Payment::where('id', $myPost['custom'])->update([
                'status' => Payment::ERROR
            ]);

            $this->paymentController->ExpireHandler($myPost['custom']);
        });

        return http_response_code(200);
    }

    private function handleRegularPayment(array $myPost)
    {
        return DB::transaction(function() use ($myPost) {
            switch ($myPost['txn_type']) {
                case 'new_case':
                case 'Refunded':
                    return $this->handleChargeback($myPost);

                default:
                    switch ($myPost['payment_status']) {
                        case 'Completed':
                            return $this->handleCompletedPayment($myPost);
                        case 'Reversed':
                            return $this->handleChargeback($myPost);
                        case 'Refunded':
                            return $this->handleRefund($myPost);
                        case 'Canceled_Reversal':
                            return $this->handleCanceledReversal($myPost);
                        default:
                            Log::error('Unknown payment status', ['status' => $myPost['payment_status']]);
                            return http_response_code(200);
                    }
            }
        });
    }

    private function handleChargeback(array $myPost)
    {
        $existingChargeback = null;
        if (isset($myPost['txn_id'], $myPost['custom'])) {
            $existingChargeback = Chargeback::where('payment_id', $myPost['custom'])
                ->where('sid', $myPost['txn_id'])
                ->first();
        }

        if (!$existingChargeback) {
            $caseData = array_intersect_key($myPost, array_flip([
                'case_type',
                'case_id',
                'case_creation_date',
                'reason_code'
            ]));

            if (isset($caseData['reason_code'])) {
                $caseData['reason'] = $caseData['reason_code'];
            }

            $payment = Payment::where('id', $myPost['custom'])->first();
            if (!$payment) {
                Log::error('Payment not found', ['payment_id' => $myPost['custom']]);
                abort(404, 'Payment not found');
            }

            $this->paymentController->PaymentHandler($payment);
            $this->paymentController->ExpireHandler($myPost['custom']);
            $this->paymentController->ChargebackHandler($myPost['custom']);

            Payment::where('id', $myPost['custom'])->update([
                'status' => Payment::CHARGEBACK,
                'transaction' => $myPost['txn_id']
            ]);

            Subscription::where('payment_id', $myPost['custom'])->update([
                'status' => Subscription::CANCELLED
            ]);

            Chargeback::create([
                'payment_id' => $myPost['custom'],
                'sid' => $myPost['txn_id'],
                'status' => Chargeback::PENDING,
                'details' => json_encode($caseData)
            ]);
        }

        return http_response_code(200);
    }

    private function handleRefund(array $myPost)
    {
        $chargeback = Chargeback::where('payment_id', $myPost['custom'])->first();
        if ($chargeback) {
            $chargeback->update(['status' => Chargeback::CHARGEBACK]);
        }

        DB::transaction(function() use ($myPost) {
            Payment::where('id', $myPost['custom'])->update([
                'status' => Payment::CHARGEBACK
            ]);

            Subscription::where('payment_id', $myPost['custom'])->update([
                'status' => Subscription::CANCELLED
            ]);

            $this->paymentController->ExpireHandler($myPost['custom']);
            $this->paymentController->ChargebackHandler($myPost['custom']);

            $payment = Payment::where('id', $myPost['custom'])->first();
            if ($payment) {
                $this->paymentController->PaymentHandler($payment);
            }
        });

        return http_response_code(200);
    }

    private function handleCanceledReversal(array $myPost)
    {
        $chargeback = Chargeback::where('payment_id', $myPost['custom'])->first();
        if ($chargeback) {
            $chargeback->update(['status' => Chargeback::COMPLETED]);
        }

        return http_response_code(200);
    }

    private function handleCompletedPayment(array $myPost)
    {
        if ($myPost['txn_type'] !== 'web_accept') {
            return response('', 200);
        }

        $item_amount = (float) $myPost['mc_gross'];
        $paymentData = Payment::where('id', $myPost['custom'])
            ->select('currency', 'price')
            ->first();

        if (!$paymentData) {
            abort(404, 'Payment not found');
        }

        if (strtoupper($myPost['mc_currency']) != strtoupper($paymentData->currency)
            || $item_amount < $paymentData->price) {
            abort(400, 'Invalid payment amount or currency');
        }

        DB::transaction(function() use ($myPost) {
            if (isset($myPost['txn_id'])) {
                Payment::where('id', $myPost['custom'])->update([
                    'transaction' => $myPost['txn_id']
                ]);
            }

            $this->paymentController->FinalHandler($myPost['custom']);
        });

        return http_response_code(200);
    }
}
