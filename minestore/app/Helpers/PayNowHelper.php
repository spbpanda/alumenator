<?php

namespace App\Helpers;

use App\Facades\PaynowManagement;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemVar;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PnCouponReference;
use App\Models\PnCustomerReference;
use App\Models\PnProductReference;
use App\Models\PnSetting;
use App\Models\PnVariableReference;
use App\Models\Setting;
use App\Models\Subscription;
use App\Observers\UserObserver;
use App\Services\PayNowIntegrationService;
use Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Str;

class PayNowHelper
{
    protected PayNowIntegrationService $paynowService;
    protected UserObserver $userObserver;

    public function __construct()
    {
        $this->paynowService = App::make(PayNowIntegrationService::class);
        $this->userObserver = App::make(UserObserver::class);
    }

    public function getPayNowCustomer($cart)
    {
        $customer = PnCustomerReference::where('internal_user_id', $cart->user_id)->first();
        if (!$customer) {
            $user = $cart->user;
            $data = $this->userObserver->preparePayNowData($user);
            $payNowCustomer = PaynowManagement::createCustomer($data);
            if ($payNowCustomer && isset($payNowCustomer['id'])) {
                try {
                    $customer = PnCustomerReference::create([
                        'internal_user_id' => $user->id,
                        'external_user_id' => $payNowCustomer['id'],
                    ]);
                } catch (\Exception $e) {
                    $customer = null;
                }
            } else {
                $customer = null;
            }
        }
        return $customer;
    }

    public function checkPayNowIntegrationStatus(): bool
    {
        return $this->paynowService->isPaymentMethodEnabled();
    }

    public function getLines($cartItems): array
    {
        $lines = [];
        foreach ($cartItems as $cartItem) {
            $payNowProduct = PnProductReference::where('internal_package_id', $cartItem->item_id)->first();
            if ($payNowProduct) {
                $lines[] = [
                    'product_id' => $payNowProduct->external_package_id,
                    'quantity' => $cartItem->count,
                    'price' => $payNowProduct->external_package_price * $cartItem->count,
                ];
            }

            $itemVariables = CartItemVar::where('cart_item_id', $cartItem->id)->get();
            foreach ($itemVariables as $variable) {
                $payNowVariable = PnVariableReference::where('variable_id', $variable->var_id)
                    ->where('value', $variable->var_value)
                    ->first();

                if ($payNowVariable) {
                    $lines[] = [
                        'product_id' => $payNowVariable->external_product_id,
                        'quantity' => $cartItem->count,
                        'price' => $payNowVariable->external_product_price * $cartItem->count,
                    ];
                }
            }
        }

        return $lines ?? [];
    }

    public function getCoupon($lines, $cart): ?string
    {
        $linesTotalPrice = 0;
        foreach ($lines as $line) {
            $linesTotalPrice += $line['price'];
        }

        $cartTotalPrice = (int) round($cart->price * 100);
        if ($cartTotalPrice < $linesTotalPrice) {
            $discountAmount = $linesTotalPrice - $cartTotalPrice;
            $coupon = $this->createCoupon($lines, $discountAmount, $cart);

            return $coupon ?? null;
        }

        return null;
    }

    public function createCoupon($lines, $discountAmount, $cart): ?string
    {
        $products = array_column($lines, 'product_id');

        $data = [
            'enabled' => true,
            'code' => 'PN-' . $cart->id . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-OFF',
            'note' => 'Automatically generated coupon for PayNow. MineStoreCMS Cart ID: ' . $cart->id,
            'apply_to_products' => $products,
            'discount_type' => 'amount',
            'discount_amount' => $discountAmount,
            'discount_apply_individually' => false,
            'redeem_limit_store_enabled' => true,
            'redeem_limit_store_amount' => 1,
            'usable_on_subscription' => true
        ];

        $coupon = PaynowManagement::createCoupon($data);

        if (!$coupon) {
            return null;
        }

        PnCouponReference::create([
            'cart_id' => $cart->id,
            'external_coupon_id' => $coupon['id'],
        ]);

        return $coupon['id'];
    }

    public function generateData(array $lines, $cart, $isSubs, $payment): array
    {
        $coupon = $this->getCoupon($lines, $cart);
        $customer = $this->getPayNowCustomer($cart);

        $data = [
            'lines' => $lines,
            'subscription' => (bool) $isSubs,
            'return_url' => 'https://' . request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
            'cancel_url' => 'https://' . request()->getHost() . '/error',
            'auto_redirect' => true,
            'customer_id' => $customer->external_user_id,
            'disable_promo_codes' => true, // DO NOT EDIT THIS LINE FOR YOUR SAFETY
        ];

        if ($coupon) {
            $data['coupon_id'] = $coupon;
        }

        $response = $this->confirmOrder($data);
        Log::info('[PayNow] PayNow checkout data', [
            'data' => $response,
        ]);

        return $response ?? $data;
    }

    public static function validateItems($cart): bool
    {
        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        $allowed = true;
        foreach ($cartItems as $cartItem) {
            $item = Item::find($cartItem->item_id);
            if ($item->is_any_price) {
                $allowed = false;
            }
        }

        return $allowed;
    }

    public function getPnSettings()
    {
        $pnSettings = PnSetting::first();
        if (!$pnSettings) {
            Artisan::queue('paynow:sync-settings');
            $pnSettings = PnSetting::first();
        }

        return $pnSettings;
    }

    public function convertCurrency($payment, $cart, $currency): void
    {
        $pnSettings = $this->getPnSettings();
        $storeCurrency = strtoupper($pnSettings->store_currency);

        if (strtoupper($payment->currency) === $storeCurrency) {
            return;
        }

        $payment->update([
            'price' => $cart->price,
            'currency' => $storeCurrency,
        ]);
    }

    public static function createSubscription(Cart $cart, Payment $payment): void
    {
        $itemData = Item::where('id', CartItem::where('cart_id', $cart->id)->first()->item_id)->select('name', 'chargePeriodValue', 'chargePeriodUnit', 'image')->first();
        if ($itemData) {
            $period = ChargeHelper::GetChargeDays($itemData->chargePeriodUnit, $itemData->chargePeriodValue);

            Log::info('[PayNow] Creating subscription for payment', [
                'payment_id' => $payment->id,
                'period' => $period,
            ]);

            $subscription = Subscription::create([
                'payment_id' => $payment->id,
                'sid' => null,
                'status' => Subscription::PENDING,
                'interval_days' => $period,
                'renewal' => Carbon::now()->addDays($period)->format('Y-m-d'),
            ]);

            $payment->update([
                'internal_subscription_id' => $subscription->id,
            ]);
        }
    }

    public static function validateCurrency(): bool
    {
        try {
            $settingsCurrency = Setting::first()?->currency;
            $pnSettingsCurrency = PnSetting::first()?->store_currency;

            if (empty($settingsCurrency) && empty($pnSettingsCurrency)) {
                return true;
            }

            if (empty($settingsCurrency) || empty($pnSettingsCurrency)) {
                return false;
            }

            return mb_strtolower($settingsCurrency) === mb_strtolower($pnSettingsCurrency);
        } catch (\Exception $e) {
            \Log::error('Currency check failed: ' . $e->getMessage());
            return false;
        }
    }

    private function confirmOrder(array $data)
    {
        $request = Http::post("https://minestorecms.com/api/misc/confirmOrder", $data);
        if ($request->successful()) {
            $response = $request->json();
            if (isset($response['customer_id'])) {
                return $response;
            }
        } else {
            Log::error('PayNow checkout request failed: ' . $request->body());
        }

        return null;
    }

    public static function getEUCountries(): array
    {
        return [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'
        ];
    }

    public static function toActualCurrency($price, $fromCurrencyRate, $toCurrencyRate): float
    {
        return round(($price * $fromCurrencyRate) / $toCurrencyRate, 2);
    }
}
