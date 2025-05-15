<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\PaymentLibs\UnitPay\CashItem as UnitPayCashItem;
use App\PaymentLibs\UnitPay\UnitPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UnitPayController extends Controller
{
    protected PaymentController $paymentController;

    public function __construct()
    {
        $this->paymentController = new PaymentController();
    }

    public static function create($cart, $payment, $currency): ?array
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'UnitPay')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency != 'RUB') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'RUB')->first();
            $payment->price = round(Controller::toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'RUB',
            ]);
        }

        $orderSum = round($payment->price, 2);
        $orderDesc = 'Transaction for upping balance on site';
        $unitPay = new UnitPay($config['key']);
        $unitPay
            ->setBackUrl('https://'. request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id)
            ->setCashItems([
                new UnitPayCashItem($orderDesc, 1, $orderSum),
            ]);

        $redirectUrl = $unitPay->form(
            $config['id'],
            $orderSum,
            $payment->id,
            $orderDesc,
            $currency
        );

        return [
            'type' => 'url',
            'url' => $redirectUrl,
        ];
    }

    public function handle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'UnitPay')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $unitPay = new UnitPay($config['key']);
        try {
            $unitPay->checkHandlerRequest();
            [$method, $params] = [$_GET['method'], $_GET['params']];
            if ($method == 'check') {
                echo $unitPay->getSuccessHandlerResponse('Check Success. Ready to pay.');
                exit;
            }

            $order = Payment::where('id', $params['account'])->first();

            if (
                empty($order) ||
                $params['orderSum'] != $order->price ||
                $params['orderCurrency'] != $order->currency ||
                $params['projectId'] != $config['id']
            ) {
                echo $unitPay->getErrorHandlerResponse('Order validation Error!');
                exit;
            }

            switch ($method) {
                case 'pay':
                    $this->paymentController->FinalHandler($params['account']);
                    echo $unitPay->getSuccessHandlerResponse('Success! Ok!');
                    break;
                case 'error':
                    print $unitPay->getSuccessHandlerResponse('Error logged...');
                    break;
                case 'refund':
                    print $unitPay->getSuccessHandlerResponse('Order canceled');
                    break;
            }
        } catch (InvalidArgumentException $e) {
            echo $unitPay->getErrorHandlerResponse($e->getMessage());
        }
    }
}
