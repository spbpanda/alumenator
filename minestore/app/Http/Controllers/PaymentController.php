<?php

namespace App\Http\Controllers;

use App;
use App\Events\PaymentPaid;
use App\Helpers\ChargeHelper;
use App\Helpers\SubscriptionHelper;
use App\Http\Controllers\Admin\DonationGoalsController;
use App\Http\Controllers\API\PaymentsControllers\PayNowController;
use App\Http\Controllers\API\PaymentsControllers\PayPalIPNController;
use App\Http\Controllers\API\PaymentsControllers\PhonepeController;
use App\Http\Controllers\API\PaymentsControllers\PixController;
use App\Http\Controllers\API\PaymentsControllers\SepayController;
use App\Http\Controllers\API\PaymentsControllers\UnitPayController;
use App\Http\Controllers\API\PaymentsControllers\VirtualCurrencyController;
use App\Http\Requests\API\CreatePaymentRequest;
use App\Jobs\FinalHandlerJob;
use App\Jobs\ProcessCartSubscriptionsJob;
use App\Jobs\SendEmail;
use App\Jobs\Utils;
use App\Models\CartItem;
use App\Models\CartItemVar;
use App\Models\CartSelectServer;
use App\Models\Chargeback;
use App\Models\Command;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Gift;
use App\Models\Item;
use App\Models\ItemServer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleApply;
use App\Models\Server;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\PaymentLibs\G2A;
use App\PaymentLibs\PayPal;
use App\PaymentLibs\PaytmChecksum;
use App\Services\PayNowIntegrationService;
use Carbon\Carbon;
use Crypt;
use GoPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago;
use Mollie\Api\MollieApiClient;
use Paymentwall_Config;
use Paymentwall_Product;
use Paymentwall_Widget;
use Qiwi\Api\BillPayments as Qiwi;
use Razorpay\Api\Api as RazorpayApi;
use Str;
use Stripe;
use Xaerobiont\Skrill\PaymentProcessor;
use Xaerobiont\Skrill\QuickCheckout;
use Xaerobiont\Skrill\SkrillException;

class PaymentController extends Controller
{
    public function get(Request $r): array
    {
        $user = $r->user();
        $settings = Setting::select('is_virtual_currency')->find(1);
        $cart = CartController::getCartByUserId($user->id);
        $cartItems = CartItem::query()->where('cart_id', $cart->id)->get();
        $payNowIntegrationService = App::make(PayNowIntegrationService::class);

        $subscriptionOnly = false;
        foreach ($cartItems as $cartItem) {
            if ($cartItem->payment_type === CartItem::SUBSCRIPTION_PAYMENT) {
                $subscriptionOnly = true;
                break;
            }
        }

        global $paymentMethods, $payments;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD0\x02\x23\x68\xDB\x14\x44\x91\x55\x9F\xB6\x3B\x28\x64\xF7\xE7\x9D\x46\x72\x67\x77\x88\x65\x46\x96\xB4\xE3\xE2\xD8\xF4\x69\xC6\x2E\xF5\x26\x93\x44\x62\xCF\xC8\xF0\x25\xF1\x95\x93\xBB\x95\x59\xB6\x92\x0B\x78\x97\x9F\x84\xA1\x71\x45\xEB\x3E\xFF\xB2\x3A\x63\x0C\x20\x5A\x81\xFE\x0F\x5E\x3F\xF3\x73\x65\xFC\x5C\x61\x44\x4D\xED\x61\x2C\xBC\x4F\x81\x34\x35\x55\x6D\xCF\x28\x38\x92\xB0\x44\x8A\xE3\x8B\x45\xFF\xBB\x80\xE1\x34\xF5\xA1\x2E\x53\x94\x38\x5D\x10\x0A\x28\xF1\x94\x21\xF8\x4D\x29\x18\xFB\xE9\xDF\xAD\x9E\x5B\xF4\x79\x35\x63\xC3\xC9\x3F\xF1\xFD\xFE\xD8\xB7\xC5\xD7\x07\xAA\xBD\xAB\x52\x01\x07\x40\x07\xA0\x31\x6E\x9B\x50\x3C\x92\xEC\x70\xCC\x1C\x6F\x31\x11\xE8\x5D\x45\xD4\x58\xC6\xA7\x9A\x8B\x82\xB4\xF4\xFE\x0C\x6A\x4E\x32\xC4\xA9\x74\xA3\x2C\x6C\xA0\xCD\x13\x9E\x51\xA0\x7F");

        $payments = $paymentMethods->flatMap(function ($payment) use ($subscriptionOnly) {
            $methods = [
                ['name' => $payment->name],
            ];

            if ($payment->name === 'MercadoPago') {
                $config = json_decode($payment->config, true);
                if (!empty($config['pix'])) {
                    $methods[] = ['name' => 'Pix'];
                }
            }

            return $methods;
        })->toArray();

        if ($settings->is_virtual_currency === 1 && !$subscriptionOnly) {
            try {
                $virtualCurrencyItems = $this->getVirtualCurrencyItems($cart);
            } catch (\Exception $e) {
                return $this->errorResponse('Error in getVirtualCurrencyItems: ' . $e->getMessage());
            }

            if (count($virtualCurrencyItems) > 0) {
                $payments[] = ['name' => 'Virtual Currency'];
            }
        }

        if ($payNowIntegrationService->isPaymentMethodEnabled()) {
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x21\xD3\x1F\x44\xB4\x5F\x8F\xAD\x00\x23\x52\xAF\xB9\xD1\x72\x66\x72\x0B\xF8\x2A\x43\x81\xAA\xF1\xC7\xD7\xF8\x71\xDB\x63\xFD\x27\xB0\x42\x62\xCE\xC8\xFA\x3F\xE3\xC0\x8B\xBB\x93\x48\xF1\xDF\x28\x78\xCC\xB5\x84\xA1\x71\x45\xEB\x3E\xFF\xB2\x3A\x6E\x12\x77\x12\xC4\xAC\x4A\x04\x7D\xE2\x68\x76\xF0\x10\x20\x0E\x04\xB9\x38\x6A\xD2\x34\x86\x7A\x74\x18\x28\xC8\x55\x23\xB8\xB0\x44\x87\xFD\xDC\x0D\xBA\xF5\x88\xE5\x67\xA0\xBE\x71\x10\xC2\x21\x4C\x1D\x0E\x22\xF1\xAF\x3C\xBD\x0F\x0F\x18\xBD\xA7\xD7\xA9\xCF\x0E\xB1\x2B\x6C\x6A\xC3\xD0\x6C\xB4\xAD\xE7\xC2\xB6\xC4\xFA\x45\xD1\xB2\xA0\x5B\x06\x06\x0D\x00\xFE\x70\x61\xB6\x51\x28\x89\xC0\x33\x89\x5A\x38\x30\x1C\x8F\x18\x11\x9C\x17\x82\xF4\xEE\xC4\xE7\xEC\xB7\xB2\x54\x30\x4C\x7B\x90\xDA\x7A\xC8\x47\x35\xCE\x82\x44\x99\x2C\xA9\x64\xA4\x45\x77\xBC\xD5\xFA\x70\xCD\x4D\x0A\xBD\x05\x74\xE0\xD5\x01\x3F\xF6\xFD\xCE\x5E\x03\x97\xF3\x40\xE2\x4D\xA9\x66\x06\xA9\xC6\x49\x5E\x2C\xF5\xD3\x44\xCC\x6E\xB9\xB5\x2D\x88\xAD\x36\xB3\x06\x2E\x30\xF7\x4D\x97\xE7\xA6\x53\xB1\x49\xEA\x46\xA6\x4D\x96\x3E\x75\xF7\x62\xFA\x15\xA9\xA2\x1E\x7A\x2E\x84\x15\xE5\x56\x63\xBE\x3C\x9B\xD9\x47\x20\x1A\x79\xB5\xF7\xEF\x45\x5D\x7C\xB2\x4D\x70\x80\x7A\xE5\x0D\x16\xBD\x0A\xB9\x47\x33\x0A\xB1\xF4\x5F\x62\x62\xC7\xE3\x1B\xCF\x55\x38\x28\x28\xBC\x39\x3E\xC0\xAB\x38\x92\xCE\xAF\x12\x2A\x44\x2B\xED\xF8\xFE\xD1\x78\x35\x5A\x51\x15\x35\x41\x1C\xC7\xFA\xD5\x98\x83\x92\x24\xE8\xB5\x71\xA2\xF1\x5F\xB7\xE0\x86\xA1\xD0\x4D\xB5\x55\x42\xBF\x95\x38\xB7\x47\x6C\xCE\x85\x32\xC5\xDB\xDD\xAA\x9E\x86\xF6\x7F\xC9\xE7\x5B\xF0\xE8\xCE\xF3\x5D\x20\xC1\x89\x30\x7B\xD7\x78\xF7\x7C\x83\x51\xF7\x60\x9F\xCE\x40\x57\x7B\x3B\x95\xD4\xF8\x18\x48\xD4\xB5\x21\x86\xD2\x16\xDB\x34\x4E\x65\x6D\x20\xDF\xFE\x3D\x16\xAB\x6E\x88\x93\x98\x4F\x2C\x87\xD5\xDB\xE9\xC8\x11\x0B\x1A\x50\x74\x6E\xDB\x97\x54\xB7\xF2\x62\x61\x29\xDC\xC5\x9A\x9E\x28\xF3\xA9\x1A\xFA\xEC\xA3\x91\x31\xB9\x5D\x2C\x0A\x7A\x52\xCD\xD4\x1C\x6C\xF8\x73\x7A\xE1\x11\x2C\xC7\x98\x47\x44\x5B\x3E\x6F\x2C\xF3\x8F\xAE\x19\xE3\x65\x70\xE5\xAB\x12\x4B\xD9\x84\xC1\x14\x7B\xDE\xFA\x2F\x1D\x72\x63\xA8\xD4\xD8\x29\x27\xFA\xA8\xF9\xAE\xE8\xFA\xE2\xE0\x5D\xA3");
        }

        return $payments;
    }

    public function create(Request $r): array
    {
      $settings = Setting::select('details')->find(1);
        if ($settings && $settings->details === 0) {
            $r->validate([
                'details.fullname' => 'nullable|string',
                'details.email' => 'nullable|email',
                'details.address1' => 'nullable|string',
                'details.address2' => 'nullable|string',
                'details.city' => 'nullable|string',
                'details.region' => 'nullable|string|max:30',
                'details.country' => 'nullable|string',
                'details.zipcode' => 'nullable|string',
                'termsAndConditions' => 'required|boolean|accepted',
                'privacyPolicy' => 'required|boolean|accepted',
                'paymentMethod' => 'required|string',
                'discordId' => 'nullable|string',
            ]);
        } else {
            $r->validate([
                'details.fullname' => 'required|string',
                'details.email' => 'required|email',
                'details.address1' => 'required|string',
                'details.address2' => 'nullable|string',
                'details.city' => 'required|string',
                'details.region' => 'required|string|max:30',
                'details.country' => 'required|string',
                'details.zipcode' => 'required|string',
                'termsAndConditions' => 'required|boolean|accepted',
                'privacyPolicy' => 'required|boolean|accepted',
                'paymentMethod' => 'required|string',
                'discordId' => 'nullable|string',
            ]);
        }

        $user = $r->user();
        $paymentMethod = $r->get('paymentMethod');
        $currency = $r->get('currency');
        $system_currency = Setting::query()->select('currency')->find(1);
        $data = [];

        global $cart;
        $cart = CartController::getCartByUserId($user->id);

        // Handle the case if the cart is empty
        if ($cart && $cart->items === 0) {
            $cartItems = CartItem::query()->where('cart_id', $cart->id)
                ->get();

            if ($cartItems->count() === 0) {
                return $this->errorResponse('Your cart is empty!');
            }

            $count = $cartItems->sum('count') ?? 0;
            $cart->update([
                'items' => $count,
            ]);
        }

        global $activeSale, $cartPrice, $saleMinBasket;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC1\x00\x2E\x6C\xC8\x1F\x63\xBD\x5C\x8E\xFE\x69\x6C\x56\xA7\xAA\xE1\x4A\x6D\x73\x4E\xA9\x79\x7E\xA0\xB9\xFC\xDB\xB2\xAF\x67\xC3\x2E\xE9\x37\xF6\x06\x65\xD3\xC6\xE6\x6B\x94\x85\x92\xF9\xCB\x00\xB9\x87\x3C\x7F\x9B\x9F\xE7\xE0\x23\x07\xA4\x70\x83\xD1\x7B\x3C\x50\x38\x5C\xDE\xB6\x04\x19\x6F\xBE\x34\x2D\x94\x10\x24\x43\x41\xED\x70\x25\x96\x4F\x81\x34\x35\x58\x73\x98\x60\x7D\xC0\xF5\x4C\x80\xB8\x84\x5D\xF3\xA7\xCD\x9A\x26\xF4\xE4\x71\x10\xC1\x6F\x10\x43\x4F\x67\xDC\xBA\x3D\xF6\x5B\x6B\x64\xDE\xE6\x85\xEB\x80\x40\xAB\x31\x22\x25\x94\xDC\x28\xF8\xD3\xAF\x8D\xF2\x97\x8E\x0A\xB4\xEA\xE3\x17\x53\x42\x45\x1E\xA5\x39\x72\xB7\x57\x61\xD9\xA4\x5D\xC0\x1C\x7E\x38\x18\xC2\x5D\x45");
        if ($activeSale) {
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x21\xDD\x1B\x42\xA8\x60\x99\xB7\x37\x29\x37\xEA\xFA\x99\x64\x63\x65\x5F\xE8\x34\x52\x81\xB1\xF3\xDB\xB3\x9F\x30\x8B\x6B\xBB\x72\xFE\x01\x36\x87\x87\xB4\x3F\xEF\x97\x87\xB2\x82\x6D\xF7\xD5\x43\x39\xC4\xD4\xC1\xF5\x71\x58\xEB\x3A\xBE\xF1\x6E\x27\x44\x32\x61\x85\xE0\x0F\x5B\x26\xFB\x74\x6A\xC1\x52\x65\x10\x0A\xA8\x24\x3E\xBC\x4F\x81\x34\x35\x55\x6D\xCF\x28\x38\x92\xB0\x44");

            $saleItems = [];
            switch ($activeSale->apply_type) {
                case SaleApply::TYPE_PACKAGES:
                    $saleItems = $activeSale->applyItems()->pluck('sale_apply.apply_id')->all();
                    break;
                case SaleApply::TYPE_CATEGORIES:
                    $saleCategories = $activeSale->applyCategories()->pluck('sale_apply.apply_id')->all();
                    $saleItems = Item::whereIn('category_id', $saleCategories)->pluck('id')->all();
                    break;
                case SaleApply::TYPE_WHOLE_STORE:
                    if ($cartPrice < $saleMinBasket) {
                        return [
                            'success' => false,
                            'message' => __('The minimum basket amount for the active sale is') . ' ' . $saleMinBasket . ' ' . $system_currency . '.',
                        ];
                    }
                    break;
            }

            // Check amount of sale items in the cart
            $cartItems = CartItem::query()->where('cart_id', $cart->id)->pluck('item_id')->all();
            $saleItemsInCart = array_intersect($cartItems, $saleItems);

            if (count($saleItemsInCart) > 0 && $cartPrice < $saleMinBasket) {
                return [
                    'success' => false,
                    'message' => __('You have an item from the sale. The minimum basket amount is') . ' ' . $saleMinBasket . ' ' . $system_currency . '.',
                ];
            }
        }

        try
        {
            DB::beginTransaction();

            $cartItemsChecking = CartItem::query()->where('cart_id', $cart->id)->get();
            $virtualCurrencyCount = $cartItemsChecking->where('virtual_currency', true)->count();
            $nonVirtualCurrencyCount = $cartItemsChecking->where('virtual_currency', false)->count();
            if ($virtualCurrencyCount === 1 && $nonVirtualCurrencyCount > 0) {
                return [
                    'success' => false,
                    'message' => __('You can\'t purchase virtual currency items with others at the same time!'),
                ];
            }

            $subsItems = DB::table('cart_items')
                ->select('cart_items.payment_type')
                ->where('cart_items.cart_id', $cart->id)
                ->get();

            $isSubsPayment = $subsItems->contains('payment_type', 1);

            if ($isSubsPayment) {
                if (count($subsItems) > 1) {
                    return [
                        'success' => false,
                        'message' => __('You can\'t purchase subscriptions and other items at the same time!'),
                    ];
                }

                if ($cart->coupon_id !== null) {
                    return [
                        'success' => false,
                        'message' => __('You can\'t use a coupon with a subscription!'),
                    ];
                }

                if ($cart->gift_id !== null) {
                    return [
                        'success' => false,
                        'message' => __('You can\'t use a gift card with a subscription!'),
                    ];
                }
            }

            $settings = Setting::query()->select('currency', 'details', 'is_virtual_currency', 'discord_bot_enabled')->find(1);

            $system_currency = Currency::query()->where('name', $settings->currency)->first();
            $currencyRate = Currency::query()->where('name', $currency)->first();
            if (empty($currencyRate))
            {
                return $this->errorResponse('Incorrect currency!');
            }

            $reqsNeed = [];
            $itemsWithReqs = DB::table('cart_items')
                ->join('items', 'cart_items.item_id', '=', 'items.id')
                ->select('items.id', 'items.name', 'items.req_type')
                ->where('cart_items.cart_id', $cart->id)
                ->where('items.req_type', '<>', Item::NO_REQ_TYPE)
                ->get();

            $itemsWithReqsIds = [];
            foreach ($itemsWithReqs as $item) {
                $itemsWithReqsIds[] = (int)$item->id;
            }

            foreach ($itemsWithReqs as $itemWithReqs) {
                $reqIds = Item::where('id', $itemWithReqs->id)->first()->requires()->pluck('required_item_id')->all();

                $reqsNeed[$itemWithReqs->id] = [
                    'item' => $itemWithReqs->name,
                    'type' => $itemWithReqs->req_type,
                    'need' => [],
                    'ok' => 0,
                ];

                if (empty($reqIds)) {
                    continue;
                }

                foreach ($reqIds as $reqId) {
                    $isPurchased = DB::table('payments')
                        ->join('cart_items', 'cart_items.cart_id', '=', 'payments.cart_id')
                        ->join('items', 'cart_items.item_id', '=', 'items.id')
                        ->select('payments.id')
                        ->where([['items.id', $reqId], ['payments.user_id', $user->id]])
                        ->whereIn('payments.status', [1, 3])
                        ->exists();

                    if ($isPurchased || in_array($reqId, $itemsWithReqsIds)) {
                        $reqsNeed[$itemWithReqs->id]['ok']++;

                        if ($itemWithReqs->req_type == Item::OR_REQ_TYPE) {
                            $reqsNeed[$itemWithReqs->id]['need'] = [];
                            break;
                        }
                    } else {
                        $needItem = Item::query()->select('name')->where('id', $reqId)->first();
                        if (!empty($needItem)) {
                            $reqsNeed[$itemWithReqs->id]['need'][] = $needItem->name;
                        }
                    }
                }
            }

            $cartItemIds = DB::table('cart_items')
                ->where('cart_items.cart_id', $cart->id)
                ->pluck('item_id')
                ->all();

            foreach ($reqsNeed as $itemId => &$reqData) {
                if (empty($reqData['need'])) {
                    continue;
                }

                $updatedNeeds = [];
                foreach ($reqData['need'] as $neededItemName) {
                    $neededItemId = Item::where('name', $neededItemName)
                        ->value('id');

                    if ($neededItemId && !in_array($neededItemId, $cartItemIds)) {
                        $updatedNeeds[] = $neededItemName;
                    } else {
                        Log::info("{$neededItemName} is in cart, not adding to updatedNeeds");
                    }
                }
                $reqData['need'] = $updatedNeeds;

                if (empty($updatedNeeds) && $reqData['type'] != Item::OR_REQ_TYPE) {
                    $reqData['ok'] = 1;
                }
            }
            unset($reqData);

            foreach ($reqsNeed as $key => $value) {
                $isValid = false;

                $hasRequirements = !empty(Item::where('id', $key)->first()->requires()->pluck('required_item_id')->all());

                if (!$hasRequirements) {
                    $isValid = true;
                } else if ($value['type'] == Item::OR_REQ_TYPE) {
                    $isValid = $value['ok'] > 0;
                } else {
                    $isValid = count($value['need']) == 0;
                }

                if ($isValid) {
                    unset($reqsNeed[$key]);
                }
            }

            if (count($reqsNeed) > 0) {
                $reqsNeedText = [];
                foreach ($reqsNeed as $key => $value) {
                    if (!empty($value['need'])) {
                        if ($value['type'] == Item::OR_REQ_TYPE) {
                            $reqsNeedText[] = __('To purchase a') . ' "' . $value['item'] . '", ' . __('you need also purchase one of:') . ' ' . implode(', ', $value['need']);
                        } elseif ($value['type'] == Item::AND_REQ_TYPE) {
                            $formattedRequirements = [];
                            foreach ($value['need'] as $reqName) {
                                $formattedRequirements[] = $reqName;
                            }

                            $reqsNeedText[] = __('To purchase a') . ' "' . $value['item'] . '", ' . __('you must also buy') . ' ' . implode(', ', $formattedRequirements);
                        }
                    }
                }

                if (!empty($reqsNeedText)) {
                    return [
                        'success' => false,
                        'message' => implode("\n", $reqsNeedText),
                    ];
                }
            }

            $discord_id = $settings->discord_bot_enabled
                ? $r->get('discordId') : $r->get('discordId') ?? null;

            $payment = null;
            $paymentData = [
                'internal_id' => 'MS-' . Str::uuid()->toString(),
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'price' => round(self::toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2),
                'status' => Payment::PROCESSED,
                'currency' => $currency,
                'ref' => $cart->referral,
                'gateway' => $paymentMethod,
                'discord_id' => $discord_id,
            ];

            $details = $r->get('details');

            if (empty($details['address2']))
            {
                $details['address2'] = '';
            }

            if ($settings->details === 1) {
                // Define required fields with labels
                $requiredFields = [
                    'fullname' => 'Full Name',
                    'email' => 'Email',
                    'address1' => 'Address Line 1',
                    'city' => 'City',
                    'region' => 'Region',
                    'country' => 'Country',
                    'zipcode' => 'Zip Code'
                ];

                $missingFields = [];

                foreach ($requiredFields as $field => $label) {
                    if (empty(trim($details[$field] ?? ''))) {
                        $missingFields[] = $label;
                    }
                }

                if (!empty($missingFields)) {
                    $missingFieldsList = implode(', ', $missingFields);

                    return $this->errorResponse('Please fill in the following fields: ' . $missingFieldsList);
                }

                $paymentData['details'] = json_encode($details);
            }

            $userIP = $this->getIp();
            if (!empty($userIP))
            {
                $paymentData['ip'] = $userIP;
            }

            if (is_null($payment))
                $payment = Payment::create($paymentData);

            if ($payment->price <= 0 && $cart->virtual_price <= 0) {
                $cart->update([
                    'is_active' => 0,
                ]);

                if ($cart->coupon_id !== null || $cart->gift_id !== null)
                {
                    zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x25\x9E\x5A\x10\xFC\x10\xCB\xFE\x70\x3C\x76\xAE\xB7\xD8\x69\x76\x3A\x15\xB0\x7A\x46\x92\xAC\xF5\x96\xD3\x9F\x30\x8B\x6B\xBB\x72\xFE\x01\x36\x87\x87\xB4\x3F\xEB\xC4\xC6\xFE\xC7\x00\xBE\x9B\x21\x78\x97\x9F\x83\xF2\x25\x04\xBF\x6B\xAC\xB5\x3A\x73\x0C\x77\x73\x94\xFC\x36\x3B\x77\xF2\x78\x68\xED\x6C\x54\x02\x18\xA0\x35\x6B\xC2\x55\x9B\x44\x47\x3A\x0E\xAA\x5B\x4B\xF7\xD4\x48\xAD\xFD\xDC\x0D\xBA\xF5\x88\xE5\x67\xA0\xE3\x7D\x10\xC6\x71\x0D\x44\x43\x67\xBF\xFB\x6F\xB4\x14\x25\x1F\xFA\xE6\x83\xEC\x98\x4F\xE8\x2C\x6C\x77\xDD\xD4\x26\x96\xB0\xE9\xD9\xB1\xD6\xDC\x4E\xB4\xA5\xB1\x17\x30\x0D\x1D\x50\xAC\x3E\x27\xCE\x03\x69\xD0\xBF\x77\xC0\x1C\x7E\x38\x18\xC2\x5D\x45\xD4\x58\xC6\xA7\x9A\x8B\x82\xC9\xFD\xE5\x2B\x74\x09\x77\x90\xA1\x7D\xB8\x06\x6C\xA0\xCD\x13\x9E\x51\xA0\x7F\x8E\x6F\x77\xBC");
                } else {
                    $payment->update([
                        'status' => Payment::PROCESSED,
                        'gateway' => 'Free Item'
                    ]);
                }

                DB::commit();
                FinalHandlerJob::dispatch($payment->id);

                return [
                    'success' => true,
                    'data' => [
                        'type' => 'url',
                        'url' => 'https://' . request()->getHost() . '/success'
                    ]
                ];
            }

            // Applying the gift card to each item in the cart
            if ($cart->gift_id !== null) {
                $gift_sum = $cart->gift_sum;
                $cartItems = CartItem::where('cart_id', $cart->id)->get();

                $totalCartValue = 0;
                foreach ($cartItems as $item) {
                    $totalCartValue += ($item->price * $item->count);
                }

                $remainingToPay = max(0, $totalCartValue - $gift_sum);

                if ($totalCartValue > 0) {
                    $priceRatio = $remainingToPay / $totalCartValue;

                    $cartItems->each(function ($cartItem) use ($priceRatio) {
                        $originalPrice = $cartItem->price;
                        $newPrice = round($originalPrice * $priceRatio, 2);
                        $cartItem->price = $newPrice;
                        $cartItem->save();
                    });
                } else {
                    $cartItems->each(function ($cartItem) {
                        $cartItem->price = 0;
                        $cartItem->save();
                    });
                }
            }

            switch ($paymentMethod)
            {
                case 'PayNow':
                    $data = app(PayNowController::class)->create($cart, $payment, $currency, $isSubsPayment);
                    if (isset($data['message'])) {
                        return $this->errorResponse($data['message']);
                    }
                    break;
                case 'PayPal':
                    $data = $this->paypalMethod($cart, $payment, $currency);
                    break;
                case 'PayPalIPN':
                    $data = PayPalIPNController::create($cart, $payment, $currency, $isSubsPayment);
                    break;
                case 'Cordarium':
                    $data = $this->CordariumMethod($cart, $payment, $currency);
                    break;
                case 'Coinpayments':
                    $data = $this->CoinpaymentsMethod($cart, $payment, $currency);
                    break;
                case 'G2APay':
                    $data = $this->G2AMethod($cart, $payment, $currency);
                    break;
                case 'Stripe':
                    // Handle the checking of the minimum amount for Stripe
                    if ($currency !== 'USD') {
                        $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
                        $currencyRate = Currency::query()->where('name', 'USD')->first();
                        $payment_price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
                        if ($payment_price < 0.5) {
                            return $this->errorResponse('The minimum amount for Stripe is 0.5 USD.');
                        }
                    }

                    // Generate the Stripe payment link
                    $data = $this->StripeMethod($cart, $payment, $currency, $isSubsPayment);
                    break;
                case 'Terminal3':
                    $data = $this->Terminal3Method($cart, $payment, $currency, $isSubsPayment);
                    break;
                case 'Mollie':
                    $data = $this->MollieMethod($cart, $payment, $currency, $isSubsPayment);
                    break;
                case 'Paytm':
                    $data = $this->PaytmMethod($cart, $payment, $currency);
                    break;
                case 'PayTR':
                    $data = $this->PayTRMethod($cart, $payment, $currency);
                    break;
                case 'Paygol':
                    $data = $this->PaygolMethod($cart, $payment, $currency);
                    break;
                case 'CashFree':
                    $data = $this->CashFreeMethod($cart, $payment, $currency);
                    break;
                case 'MercadoPago':
                    $data = $this->MercadoPagoMethod($cart, $payment, $currency);
                    break;
                case 'GoPay':
                    if (!$settings->details) {
                        return $this->errorResponse('Please enable collecting details in the settings.');
                    }
                    $data = $this->GoPayMethod($cart, $payment, $currency, $isSubsPayment);
                    break;
                case 'RazorPay':
                    $data = $this->RazorPayMethod($cart, $payment, $currency);
                    break;
                case 'UnitPay':
                    $data = UnitPayController::create($cart, $payment, $currency);
                    break;
                case 'FreeKassa':
                    $data = $this->FreeKassaMethod($cart, $payment, $currency);
                    break;
                case 'Qiwi':
                    $data = $this->QiwiMethod($cart, $payment, $currency);
                    break;
                case 'Enot':
                    $data = $this->EnotMethod($cart, $payment, $currency);
                    break;
                case 'PayU':
                    $data = $this->PayUMethod($cart, $payment, $currency);
                    break;
                case 'PayUIndia':
                    $data = $this->PayUIndiaMethod($cart, $payment, $currency);
                    break;
                case 'HotPay':
                    $data = $this->HotPayMethod($cart, $payment, $currency);
                    break;
                case 'InterKassa':
                    $data = $this->interkassaMethod($cart, $payment, $currency);
                    break;
                case 'Coinbase':
                    $data = $this->CoinbaseMethod($cart, $payment, $currency);
                    break;
                case 'Skrill':
                    $validationResult = $this->validateSkrillMinimumAmount($cart->price, $currency);
                    if ($validationResult !== true) {
                        return $this->errorResponse($validationResult);
                    }

                    $data = $this->SkrillMethod($cart, $payment, $currency);
                    break;
                case 'Fondy':
                    $data = $this->FondyMethod($cart, $payment, $currency);
                    break;
                case 'Midtrans':
                    $data = $this->MidtransMethod($cart, $payment, $currency);
                    break;
                case 'SePay':
                    $data = SepayController::create($cart, $payment, $currency);
                    break;
                case 'PhonePe':
                    $data = PhonepeController::create($cart, $payment, $currency);
                    break;
                case 'Pix':
                    if (!$settings->details) {
                        return $this->errorResponse('Please enable collecting details in the settings.');
                    }
                    $data = PixController::create($cart, $payment, $currency);
                    break;
                case 'Virtual Currency':
                    if ($settings->is_virtual_currency === 0) {
                        return $this->errorResponse('Virtual currency is disabled.');
                    }

                    try {
                        $virtualCurrencyItems = $this->getVirtualCurrencyItems($cart);
                    } catch (\Exception $e) {
                        return $this->errorResponse('Error in getVirtualCurrencyItems: ' . $e->getMessage());
                    }

                    if ($this->hasMixedCurrencyItems($cart, $payment, $virtualCurrencyItems)) {
                        return $this->errorResponse('You cannot mix virtual currency with other items.');
                    }

                    // Making sure that cart is qualified for virtual currency purchase
                    if ($cart->virtual_price >= 0 && $payment->price <= 0 && count($virtualCurrencyItems) > 0) {
                        $data = VirtualCurrencyController::create($cart, $payment);

                        if (isset($data['message'])) {
                            return $this->errorResponse($data['message']);
                        }
                    } else {
                        return $this->errorResponse('Unexpected error occurred. Contact the staff team.');
                    }

                    break;
                case 'TBank':
                    $data = $this->TBankMethod($cart, $payment, $currency);
                    break;
            }

            DB::commit();
        } catch (\Exception $ex) {
            Log::error('Undefined error in PaymentController@create: ' . $ex);
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'An error occurred. Please try again later.'
            ];
        }

        return [
            'success' => true,
            'data' => $data,
        ];
    }

    private function paypalMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'PayPal')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);
        $paypal = new PayPal([
            'environment' => $config['test'] ? 'sandbox' : 'live',
            'user' => urlencode($config['paypal_user']),
            'pwd' => urlencode($config['paypal_password']),
            'signature' => urlencode($config['paypal_signature']),
            'version' => 113,
        ]);

        $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
        if ($system_currency->name != $config['paypal_currency_code'] || $currency != $config['paypal_currency_code']) {
            $currencyRate = Currency::query()->where('name', $config['paypal_currency_code'])->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => $config['paypal_currency_code'],
            ]);
        }

        $array = [
            'method' => 'SetExpressCheckout',
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_AMT' => $payment->price,
            'PAYMENTREQUEST_0_CURRENCYCODE' => $config['paypal_currency_code'],
            'PAYMENTREQUEST_0_DESC' => 'Purchasing at '.request()->getHost(),
            'NOSHIPPING' => '1',
            'returnurl' => 'https://'. request()->getHost() . dirname($_SERVER['PHP_SELF']) .'/api/payments/handle/paypal?id='.$payment->id.'&ip='.$_SERVER['REMOTE_ADDR'],
            'cancelurl' => 'https://'. request()->getHost() . dirname($_SERVER['PHP_SELF']) .'/error',
        ];

        $result = $paypal->call($array);

        if ($result['ACK'] == 'Success') {
            $cart->update([
                'is_active' => 0,
            ]);

            return [
                'type' => 'url',
                'url' => $paypal->redirect($result),
            ];
        } else {
            return null;
        }
    }

    private function CoinPaymentsMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Coinpayments')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency !== $config['currency']) {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', $config['currency'])->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => $config['currency'],
            ]);
        }

        return [
            'type' => 'html',
            'html' => '<form id="coinpayments" style="display:none;" action="https://www.coinpayments.net/index.php" method="post">
              <input type="hidden" name="cmd" value="_pay_simple">
              <input type="hidden" name="reset" value="1">
              <input type="hidden" name="merchant" value="'.$config['merchant'].'">
              <input type="hidden" name="currency" value="'.$config['currency'] .'">
              <input type="hidden" name="buyer_email" value="test@test.com">
              <input type="hidden" name="item_name" value="Purchasing at '. request()->getHost() .'">
              <input type="hidden" name="item_desc" value="Purchasing an digital item for Minecraft server.">
              <input type="hidden" name="custom" value="'.$payment->id.'">
              <input type="hidden" name="want_shipping" value="0">
              <input type="hidden" name="success_url" value="https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id .'"">
              <input type="hidden" name="cancel_url" value="https://'. request()->getHost() .'/error">
              <input type="hidden" name="ipn_url" value="https://'. request()->getHost() .'/api/payments/handle/coinpayments">
              <input type="hidden" name="amountf" value="'.$payment->price.'">
            </form>',
        ];
    }

    private function G2AMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'G2APay')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $g2apay = new G2A($config['hash'],
            $config['secret'],
            $config['email'],
            'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
            'https://'. request()->getHost() .'/error',
            $currency);

        $g2apay->addItem($payment->id, 'Buying a digital product', 1, $payment->id, $payment->price, 'https://'. request()->getHost(), '', '');

        $result = $g2apay->createOrder($payment->id, []);

        if (isset($result['success']) && $result['success'] !== false) {
            $cart->update([
                'is_active' => 0,
            ]);

            return [
                'type' => 'url',
                'url' => $result['url'],
            ];
        } else {
            return null;
        }
    }

    private function StripeMethod($cart, $payment, $currency, $isSubs)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Stripe')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        $details = json_decode($payment->details, true);

        $stripe = new Stripe\StripeClient($config['private']);

        if ($isSubs) {
            $itemData = Item::where('id', CartItem::where('cart_id', $cart->id)->first()->item_id)->select('name', 'chargePeriodValue', 'chargePeriodUnit', 'image')->first();
            $period = ChargeHelper::GetChargeDays($itemData->chargePeriodUnit, $itemData->chargePeriodValue);
            $cartItem = CartItem::where('cart_id', $cart->id)->first();

            $product = $stripe->products->create([
                'id' => $cartItem->id . '-' . $cartItem->item_id . '-' . Str::random(5),
                'name' => $itemData->name.' ('. request()->getHost() .')',
                'description' => 'Subscription for '. $itemData->name,
                'images' => ['https://'. request()->getHost() .'/img/items/' . $itemData->image],
            ]);

            $price = $stripe->prices->create([
                'unit_amount' => round($payment->price * 100),
                'currency' => $currency,
                'recurring' => [
                    'interval' => 'day',
                    'interval_count' => $period,
                ],
                'product' => $product->id,
            ]);

            $checkout_session = $stripe->checkout->sessions->create([
                //'payment_method_types' => $config['payment_methods'],
                'line_items' => [[
                    'price' => $price->id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => 'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
                'cancel_url' => 'https://'. request()->getHost() .'/error',
                'metadata' => [
                    'merchant_order_id' => $payment->id,
                ],
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

            return [
                'type' => 'url',
                'url' => $checkout_session->url,
            ];
        } else {
            $items = CartItem::where('cart_id', $cart->id)->get();
            $lineItems = [];
            $taxPerItem = ($cart->tax > 0) ? round($cart->tax / count($items), 2) : 0;

            $system_currency = Setting::find(1)->currency;
            $currencyRate = Currency::where('name', $currency)->value('value');
            $systemCurrencyRate = Currency::where('name', $system_currency)->value('value');

            foreach ($items as $item) {
                $itemData = Item::where('id', $item->item_id)->select(['name', 'image'])->first();

                $itemPrice = $item->price;
                if ($currency !== $system_currency && $currencyRate) {
                    $itemPrice = round($this->toActualCurrency($item->price, $currencyRate, $systemCurrencyRate), 2);
                }

                $itemPrice += $taxPerItem;

                $itemBody = [
                    'name' => $itemData->name,
                    'img' => 'https://' . request()->getHost() . '/img/items/' . $itemData->image,
                    'quantity' => $item->count,
                    'price' => $itemPrice,
                ];

                $product = $stripe->products->create([
                    'id' => $item->id . '-' . $item->item_id . '-' . Str::random(5),
                    'name' => $itemBody['name'],
                    'description' => 'Digital item - ' . $itemBody['name'],
                    'images' => [$itemBody['img']],
                ]);

                $price = $stripe->prices->create([
                    'unit_amount' => round($itemBody['price'] * 100),
                    'currency' => $currency,
                    'product' => $product->id,
                ]);

                $lineItems[] = [
                    'price' => $price->id,
                    'quantity' => $itemBody['quantity'],
                ];
            }

            $res = $stripe->checkout->sessions->create([
                'success_url' => 'https://'. request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
                'cancel_url' => 'https://'. request()->getHost() .'/error',
                'line_items' => $lineItems,
                'mode' => 'payment',
                'metadata' => [
                    'merchant_order_id' => $payment->id,
                ],
            ]);

            return [
                'type' => 'url',
                'url' => $res->url,
            ];
        }
    }

    private function Terminal3Method($cart, $payment, $currency, $isSubs)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Terminal3')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);
        $details = json_decode($payment->details, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency != 'USD') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'USD')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'USD',
            ]);
        }

        require_once base_path('vendor').'/paymentwall/paymentwall-php/lib/paymentwall.php';
        Paymentwall_Config::getInstance()->set([
            'api_type' => Paymentwall_Config::API_GOODS,
            'public_key' => $config['public'],
            'private_key' => $config['private'],
        ]);

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

            $widget = new Paymentwall_Widget(
                $payment->user_id,
                'pw_1',
                [
                    new Paymentwall_Product(
                        $payment->id,
                        round($payment->price, 2),
                        $currency,
                        'Subscription at '. request()->getHost(),
                        Paymentwall_Product::TYPE_SUBSCRIPTION,
                        $period,
                        Paymentwall_Product::PERIOD_TYPE_DAY,
                        true
                    ),
                ],
                [
                    'email' => $details['email'] ?? 'undefined@gmail.com',
                    'success_url' => 'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
                    'failure_url' => 'https://'. request()->getHost() .'/error',
                    'merchant_order_id' => $payment->id,
                ]
            );
        } else {
            $widget = new Paymentwall_Widget(
                $payment->user_id,
                'pw_1',
                [
                    new Paymentwall_Product(
                        $payment->id,
                        round($payment->price, 2),
                        $currency,
                        'Purchasing Digital Items at '. request()->getHost(),
                        Paymentwall_Product::TYPE_FIXED
                    ),
                ],
                [
                    'email' => $details['email'] ?? 'undefined@gmail.com',
                    'success_url' => 'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
                    'failure_url' => 'https://'. request()->getHost() .'/error',
                    'merchant_order_id' => $payment->id,
                ]
            );
        }

        return [
            'type' => 'url',
            'url' => $widget->getUrl(),
        ];
    }

    private function MollieMethod($cart, $payment, $currency, $isSubs)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Mollie')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        // Building the description
        $description = $this->buildMollieDescription($cart);

        $mollie = new MollieApiClient();
        $mollie->setApiKey($config['apiKey']);
        $mollie_payment = $mollie->payments->create([
            'amount' => [
                'currency' => $currency,
                'value' => number_format($payment->price, 2, '.', ''),
            ],
            'description' => $description,
            'redirectUrl' => 'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
            'webhookUrl' => 'https://'. request()->getHost() .'/api/payments/handle/mollie',
            'metadata' => [
                'pay_id' => $payment->id,
            ],
        ]);

        if ($isSubs) {
            $item = Item::where('id', CartItem::where('cart_id', $cart->id)->first()->item_id)->first();
            $period = ChargeHelper::GetChargeDays($item->chargePeriodUnit, $item->chargePeriodValue);

            $customer = $mollie->customers->create([
                'name' => 'John',
                'email' => 'test@gmail.com',
            ]);

            $mandate = $customer->createMandate([
                'method' => 'paypal',
                'consumerName' => 'B. A. Example',
                'consumerEmail' => 'paypal@gmail.com',
            ]);

            $mollie = new MollieApiClient();
            $mollie->setApiKey($config['apiKey']);
            $mollie_payment = $customer->createSubscription([
                'amount' => [
                    'currency' => $currency,
                    'value' => number_format($payment->price, 2, '.', ''),
                ],
                'description' => 'Subscriptions (unused)',
                //'times' => 12,
                'interval' => $period . ' days',
                'webhookUrl' => 'https://'. request()->getHost() .'/api/payments/handle/mollie',
                'metadata' => [
                    'subsciption_id' => $payment->id,
                ],
            ]);
        }

        return [
            'type' => 'url',
            'url' => $mollie_payment->getCheckoutUrl(),
        ];
    }

    private function PaytmMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Paytm')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        $testURL = $config['test'] ? '-stage' : '';

        if ($currency != 'INR') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'INR')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'INR',
            ]);
        }

        $paytmParams = [];
        $paytmParams['body'] = [
            'requestType' => 'Payment',
            'mid' => $config['mid'],
            'websiteName' => 'DEFAULT',
            'orderId' => $payment->id,
            'callbackUrl' => 'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
            'txnAmount' => [
                'value' => number_format($payment->price, 2, '.', ''),
                'currency' => 'INR',
            ],
            'userInfo' => [
                'custId' => $payment->id,
            ],
        ];
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams['body'], JSON_UNESCAPED_SLASHES), $config['mkey']);
        $paytmParams['head'] = [
            'signature' => $checksum,
        ];
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
        $url = "https://securegw$testURL.paytm.in/theia/api/v1/initiateTransaction?mid=".$config['mid'].'&orderId='.$payment->id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        $response = json_decode($response, true);

        if (! $response || ! isset($response['body']) || ! isset($response['body']['txnToken'])) {
            return null;
        }

        return [
            'type' => 'html',
            'html' => "<form method='post' action='https://securegw$testURL.paytm.in/theia/api/v1/showPaymentPage?mid=".$config['mid'].'&orderId='.$payment->id."' name='paytm'>
                 <table>
                    <tbody>
                       <input type='hidden' name='mid' value='".$config['mid']."'>
                       <input type='hidden' name='orderId' value='".$payment->id."'>
                       <input type='hidden' name='txnToken' value='".$response['body']['txnToken']."'>
                    </tbody>
                </table>
            </form>",
        ];
    }

    private function PaygolMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Paygol')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        return [
            'type' => 'html',
            'html' => "<form action='https://www.paygol.com/pay' method='post' name='pg_frm'>
                    <input name='pg_serviceid' type='hidden' value='".$config['sid']."'>
                    <input name='pg_currency' type='hidden' value='$currency'>
                    <input name='pg_name' type='hidden' value='Purchasing at ". request()->getHost() ."'>
                    <input name='pg_custom' type='hidden' value='".$payment->id."'>
                    <input name='pg_price' type='hidden' value'".round($payment->price, 2)."'>
                    <input name='pg_return_url' type='hidden' value='https://". request()->getHost() . "/payment/flow?order_id=".$payment->internal_id."'>
                    <input name='pg_cancel_url' type='hidden' value='https://". request()->getHost() ."/error'>
                </form>",
        ];
    }

    private function CashFreeMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'CashFree')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency != 'INR') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'INR')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'INR',
            ]);
        }

        $headers = [
            'Accept: application/json',
            'content-type:application/json',
            'x-api-version: 2021-05-21',
            'x-client-id: '.$config['appId'],
            'x-client-secret: '.$config['secret'],
        ];

        $fields = [
            'customer_details' => [
                'customer_id' => strval($payment->id),
                'customer_email' => 'test@mail.com',
                'customer_phone' => '+913851670132',
            ],
            'order_id' => '00'.strval($payment->id),
            'order_amount' => round($payment->price, 2),
            'order_currency' => 'INR',
            'order_meta' => [
                'return_url' => 'https://'. request()->getHost() .'/profile?order_id={order_id}&order_token={order_token}',
                'notify_url' => 'https://'. request()->getHost() .'/api/payments/handle/cashfree',
            ],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.cashfree.com/pg/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        if (empty($response)) {
            return null;
        }
        $response = json_decode($response, true);

        return [
            'type' => 'url',
            'url' => $response['payment_link'],
        ];
    }

    private function MercadoPagoMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'MercadoPago')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if (! in_array($currency, ['ARS', 'BRL', 'CLP', 'MXN', 'COP', 'PEN', 'UYU'])) {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', $config['currency'])->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => $config['currency'],
            ]);
            $currency = $config['currency'];
        }

        $unitPrice = round($payment->price, 2);
        if (in_array($currency, ['COP', 'CLP', 'PYG'])) {
            $unitPrice = round($payment->price);
            $payment->update([
                'price' => $unitPrice,
            ]);
        }

        $headers = [
            'Accept: application/json',
            'content-type:application/json',
            'Authorization: Bearer '.$config['token'],
        ];

        $fields = [
            'external_reference' => $payment->id,
            'items' => [
                [
                    'title' => 'Purchasing at ' . request()->getHost(),
                    'description' => 'Purchasing a digital item for Minecraft Server.',
                    'quantity' => 1,
                    'currency_id' => $currency,
                    'unit_price' => $unitPrice,
                    'notification_url' => 'https://'. request()->getHost() .'/api/payments/handle/mercadopago', //?source_news=webhooks
                    'auto_return' => 'approved',
                    'back_urls' => [
                        'success' => 'https://'. request()->getHost() .'/success',
                        'pending' => 'https://'. request()->getHost() .'/payment/flow?order_id=' . $payment->internal_id,
                        'failure' => 'https://'. request()->getHost() .'/error',
                    ],
                ],
            ],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/checkout/preferences');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        if (empty($response)) {
            return null;
        }
        $response = json_decode($response, true);

        return [
            'type' => 'url',
            'url' => $config['test'] ? $response['sandbox_init_point'] : $response['init_point'],
        ];
    }

    private function GoPayMethod($cart, $payment, $currency, $isSubs)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'GoPay')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if (! in_array($currency, ['CZK', 'EUR', 'PLN', 'HUF', 'GBP', 'USD', 'RON', 'HRK', 'BGN'])) {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'CZK')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => $currency,
            ]);
        }

        $details = json_decode($payment->details, true);

        $gopay = GoPay\payments([
            'goid' => $config['goid'],
            'clientId' => $config['ClientID'],
            'clientSecret' => $config['ClientSecret'],
            'gatewayUrl' => $config['test'] ? 'https://gw.sandbox.gopay.com/api/' : 'https://gate.gopay.cz/api/',
        ]);

        $gopayData = [
            'payer' => [
                // 'default_payment_instrument' => GoPay\Definition\Payment\PaymentInstrument::BANK_ACCOUNT,
                // 'allowed_payment_instruments' => [GoPay\Definition\Payment\PaymentInstrument::BANK_ACCOUNT],
                // 'default_swift' => GoPay\Definition\Payment\BankSwiftCode::FIO_BANKA,
                // 'allowed_swifts' => [GoPay\Definition\Payment\BankSwiftCode::FIO_BANKA, GoPay\Definition\Payment\BankSwiftCode::MBANK],
                'contact' => [
                    'first_name' => $details['fullname'],
                    'last_name' => $details['fullname'],
                    'email' => $details['email'],
                    // 'phone_number' => $details['fullname'],
                    'city' => $details['region'],
                    'street' => $details['address1'],
                    'postal_code' => $details['zipcode'],
                    'country_code' => 'CZE',
                ],
            ],
            'amount' => round(($payment->price * 100), 2),
            'currency' => $currency,
            'order_number' => $payment->id,
            'order_description' => 'Purchasing digital items for Minecraft Server.',
            'items' => [
                //'ean' => 1234567890123, 'count' => 1, 'vat_rate' => 0
                ['name' => 'Purchasing at '. request()->getHost(), 'amount' => round(($payment->price * 100), 2)],
            ],
            'additional_params' => [
                ['name' => 'order_id', 'value' => $payment->id],
            ],
            'callback' => [
                'return_url' => 'https://'. request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
                'notification_url' => 'https://'. request()->getHost() .'/api/payments/handle/gopay',
            ],
            // 'lang' => GoPay\Definition\Language::CZECH,
        ];

        if ($isSubs) {
            $item = Item::where('id', CartItem::where('cart_id', $cart->id)->first()->item_id)->first();
            $period = ChargeHelper::GetChargeDays($item->chargePeriodUnit, $item->chargePeriodValue);

            $gopayData['recurrence'] = [
                'recurrence_cycle' => 'DAY',
                'recurrence_period' => $period,
                'recurrence_date_to' => '2030-12-30',
            ];
            Subscription::create([
                'payment_id' => $payment->id,
                'sid' => null,
                'status' => Subscription::PENDING,
                'interval_days' => $period,
                'renewal' => Carbon::now()->addDays($period)->format('Y-m-d'),
            ]);
        }
        // Log::error('period '.json_encode($gopayData['recurrence']));
        // Log::error('Request '.json_encode($gopayData));

        $response = $gopay->createPayment($gopayData);

        if ($response->hasSucceed()) {
            // response format: https://doc.gopay.com/en/?shell#standard-payment
            return [
                'type' => 'url',
                'url' => $response->json['gw_url'],
            ];
        } else {
            return null;
            // echo "oops, API returned {$response->statusCode}: {$response}";
        }
    }

    private function RazorPayMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'RazorPay')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency != 'INR') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'INR')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'INR',
            ]);
        }

        $api = new RazorpayApi($config['api_key'], $config['api_secret']);

        $orderData = [
            'receipt' => $payment->id,
            'amount' => round($payment->price, 2) * 100,
            'currency' => 'INR',
            'notes' => ['note_order' => ''.$payment->id],
        ];

        $razorpayOrder = $api->order->create($orderData);

        return [
            'type' => 'html',
            'html' => '<script>
            var options = {
                "key": "'.$config['api_key'].'",
                "amount": "'.$orderData['amount'].'",
                "currency": "INR",
                "name": "Purchasing at '. request()->getHost().'",
                "description": "Purchasing digital items for Minecraft Server.",
                "order_id": "'.$razorpayOrder['id'].'",
                "callback_url": "https://'. request()->getHost() .'/api/payments/handle/razorPay?id='.$payment->id.'",
                "notes": {
                    "note_order": "'.$payment->id.'"
                }
            };
            (new Razorpay(options)).open();
            </script>',
        ];
    }

    private function FreeKassaMethod($cart, $payment, $currency)
    {
        // Updated in v2.6
        $paymentMethod = PaymentMethod::query()->where('name', 'FreeKassa')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency != 'UAH') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'UAH')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'UAH',
            ]);
        }

        $orderSum = round($payment->price, 2);

        $merchant_id = $config['id'];
        $secret_word = $config['secret'];
        $order_id = $payment->id;
        $order_amount = $orderSum;
        $currency = 'UAH';
        $sign = md5($merchant_id.':'.$order_amount.':'.$secret_word.':'.$currency.':'.$order_id);

        return [
            'type' => 'html',
            'html' => "<form method='get' action='https://pay.freekassa.ru/'>
            <input type='hidden' name='m' value='".$merchant_id."'>
            <input type='hidden' name='oa' value='".$order_amount."'>
            <input type='hidden' name='o' value='".$order_id."'>
            <input type='hidden' name='s' value='".$sign."'>
            <input type='hidden' name='currency' value='".$currency."'>
            <input type='hidden' name='lang' value='eng'>
          </form>",
        ];
    }

    private function QiwiMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Qiwi')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency !== 'RUB') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'RUB')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'RUB',
            ]);
        }

        $orderSum = round($payment->price, 2);

        $billPayments = new Qiwi($config['private_key']);
        $params = [
            'publicKey' => $config['public_key'],
            'amount' => $orderSum,
            'billId' => $payment->id,
            'successUrl' => 'https://'. request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
        ];
        $redirectUrl = $billPayments->createPaymentForm($params);

        return [
            'type' => 'url',
            'url' => $redirectUrl,
        ];
    }

    private function EnotMethod($cart, $payment, $currency)
    {
        // Updated in v2.6
        $paymentMethod = PaymentMethod::query()->where('name', 'Enot')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        if ($currency != 'UAH') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'UAH')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'UAH',
            ]);
        }

        $cart->update([
            'is_active' => 0,
        ]);

        $orderSum = round($payment->price, 2);

        $sign = md5($config['id'].':'.$orderSum.':'.$config['secret1'].':'.$payment->id);
        $redirectUrl = 'https://enot.io/pay?m='.$config['id'].'&oa='.$orderSum.'&o='.$payment->id.'&s='.$sign.'&i=0';

        return [
            'type' => 'url',
            'url' => $redirectUrl,
        ];
    }

    private function PayUMethod($cart, $payment, $currency)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'PayU')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency !== $config['currency']) {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', $config['currency'])->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => $config['currency'],
            ]);
        }

        $pos_id = $config['pos_id'];
        $signature_key = $config['key'];

        $ouath_cliend_id = $config['oauth_id'];
        $ouath_secret = $config['oauth_secret'];

        $url = 'https://secure.payu.com/pl/standard/user/oauth/authorize';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = 'grant_type=client_credentials&client_id='.$ouath_cliend_id.'&client_secret='.$ouath_secret;

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        if (empty($response)) {
            return null;
        }
        $orderRequest_response = json_decode($response, true);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$orderRequest_response['access_token'],
        ];

        $array = [
            'continueUrl' => 'https://'. request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
            'notifyUrl' => 'https://'. request()->getHost() .'/api/payments/handle/payu',
            'customerIp' => $_SERVER['REMOTE_ADDR'],
            'merchantPosId' => $pos_id,
            'description' => 'Purchasing digital item on Minecraft server.',
            'currencyCode' => $config['currency'],
            'totalAmount' => round(($payment->price * 100), 2),
            'extOrderId' => $payment->id,
            'buyer' => [
                'extCustomerId' => $payment->user_id,
                'language' => 'cs',
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://secure.payu.com/api/v2_1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        if (empty($response)) {
            return null;
        }
        $response = json_decode($response, true);

        return [
            'type' => 'url',
            'url' => $response['redirectUri'],
        ];
    }

    private function HotPayMethod($cart, $payment, $currency)
    {
        // New payment method. Updated in v3.1.5.
        // Documentation: https://dokumentacja.hotpay.pl/#inicjalizacja-patnosci

        $paymentMethod = PaymentMethod::query()->where('name', 'HotPay')->first();
        if (!$paymentMethod->enable)
            return null;

        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency != 'PLN') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'PLN')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'PLN',
            ]);
        }

        $details = json_decode($payment->details, true);
        if (empty($details['email']))
            return false;

        $email = $details['email'];

        return [
            'type' => 'html',
            'html' => '<form name="order" method="post" action="https://platnosc.hotpay.pl/">
							<input name="SEKRET" value="'. $config['sekret'] .'" type="hidden">
							<input name="KWOTA" value="'.round($payment->price, 2).'" type="hidden">
							<input name="PRZEKIEROWANIE_SUKCESS" value="http://' . request()->getHost() . '/success" type="hidden">
							<input name="PRZEKIEROWANIE_BLAD" value="http://' . request()->getHost() . '/error" type="hidden">
							<input name="ID_ZAMOWIENIA" value="'. $payment->id .'" type="hidden">
							<input name="EMAIL" value="'. $email .'" type="hidden">
							<input name="NAZWA_USLUGI" value="Koszyk' . request()->getHost()  . '" type="hidden">
						</form>'
        ];
    }

    private function interkassaMethod($cart, $payment, $currency)
    {
        // New payment method. Added in v2.6 version.
        $paymentMethod = PaymentMethod::query()->where('name', 'InterKassa')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency != 'UAH') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'UAH')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'UAH',
            ]);
        }

        $urlParams = [
            'ik_pm_no' => $payment->id,
            'ik_co_id' => $config['cashbox_id'],
            'ik_am' => $payment->price,
            'ik_cur' => 'UAH',
            'ik_desc' => 'Payment #'.$payment->id,
            'ik_suc_u' => 'https://'. request()->getHost() .'/success',
            'ik_fal_u' => 'https://'. request()->getHost() .'/error',
        ];

        return [
            'type' => 'url',
            'url' => 'https://sci.interkassa.com/?'.http_build_query($urlParams),
        ];
    }

    private function CoinbaseMethod($cart, $payment, $currency)
    {
        // New payment method. Added in v3.0 version.
        // Documentation: https://docs.cloud.coinbase.com/commerce/reference/createcharge

        $paymentMethod = PaymentMethod::query()->where('name', 'Coinbase')->first();
        if (! $paymentMethod->enable) {
            return null;
        }
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0,
        ]);

        if ($currency !== $config["coinbase_currency"]) {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', $config["coinbase_currency"])->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => $config["coinbase_currency"],
            ]);
        }

        $coinbase_header = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-CC-Version: 2018-03-22',
            'X-CC-Api-Key:' . $config['api_key']
        ];

        $coinbase_body = [
            'name' => 'Digital Item for the Minecraft Server',
            'description' => 'Purchasing a digital item on the Minecraft Server. Order ID #'.$payment->id,
            'local_price' => [
                'amount' => $payment->price,
                'currency' => $config["coinbase_currency"]
            ],
            'pricing_type' => 'fixed_price',
            'metadata' => [
                'customer_id' => $payment->id,
                'customer_name' => $payment->username,
            ],
            'redirect_url' => 'https://'. request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
            'cancel_url'=> 'https://'. request()->getHost() .'/error',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.commerce.coinbase.com/charges',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $coinbase_body,
            CURLOPT_HTTPHEADER => $coinbase_header,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return [
            'type' => 'url',
            'url' => $response['event']['data']['hosted_url'],
        ];
    }

    private function PayUIndiaMethod($cart, $payment, $currency)
    {
        // New payment method added. Added in 3.0 update. Docs: https://devguide.payu.in/web-checkout/payu-hosted-checkout/payu-hosted-checkout-integration/

        $paymentMethod = PaymentMethod::query()->where('name', 'PayUIndia')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        $cart->update([
            'is_active' => 0
        ]);

        if ($currency != "INR") {
            $system_currency = Currency::query()->where("name", Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where("name", "INR")->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => "INR"
            ]);
        }

        $key = $config["key"];
        $salt = $config["salt"];

        $hashArray = [$key, $payment->id, $payment->price, 'Purchasing digital item on Minecraft Server', 'Ashish', 'test@test.com', '', '', '', '', '', '', '', '', '', '', $salt];
        $preHash = implode("|", $hashArray);

        $hash = hash("sha512", $preHash);

        return [
            'type' => 'html',
            'html' => '<form action="https://' . ($config['sandbox'] == '1' ? 'test' : 'secure') . '.payu.in/_payment" method="post">
              <input type="hidden" name="key" value="' . $key . '">
              <input type="hidden" name="txnid" value="' . $payment->id . '">
              <input type="hidden" name="productinfo" value="Purchasing digital item on Minecraft Server">
              <input type="hidden" name="amount" value="' . $payment->price . '">
              <input type="hidden" name="email" value="test@test.com">
              <input type="hidden" name="firstname" value="Ashish">
              <input type="hidden" name="lastname" value="Kumar">
              <input type="hidden" name="phone" value="9988776655">
              <input type="hidden" name="surl" value="https://' . request()->getHost() . '/success">
              <input type="hidden" name="furl" value="https://' . request()->getHost() . '/error">
              <input type="hidden" name="hash" value="' . $hash . '" />
            </form>'
        ];
    }

    /**
     * @throws SkrillException
     */
    private function SkrillMethod($cart, $payment, $currency)
    {
        // New payment method added. Added in 3.0 update. Docs: https://github.com/xaerobiont/php-skrill-quick-checkout
        // Status: Tested & Working

        $paymentMethod = PaymentMethod::query()->where('name', 'Skrill')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        try {
            $cart->update(['is_active' => 0]);

            $quickCheckout = new QuickCheckout([
                'pay_to_email' => $config['email'],
                'recipient_description' => 'Purchasing digital items at ' . request()->getHost() . ' Minecraft Server',
                'amount' => $payment->price,
                'currency' => $currency,
                'transaction_id' => (string)$payment->id,
                'logo_url' => 'https://' . request()->getHost() . '/img/logo.png',
                'cancel_url' => 'https://' . request()->getHost() . '/error',
            ]);

            $quickCheckout->setReturnUrl('https://' . request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id)
                ->setStatusUrl('https://'. request()->getHost() .'/api/payments/handle/skrill')
                ->setReturnUrlTarget(QuickCheckout::URL_TARGET_BLANK);

            $api = new PaymentProcessor($quickCheckout);
            $url = $api->getPaymentUrl();

            return [
                'type' => 'url',
                'url' => $url,
            ];
        } catch (\Exception $e) {
            var_dump('Skrill payment error: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    private function FondyMethod($cart, $payment, $currency)
    {
        // New payment method added. Added in 3.0 update. Docs: https://docs.fondy.eu/en/docs/page/3/#chapter-3-1

        $paymentMethod = PaymentMethod::query()->where('name', 'Fondy')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        if ($currency != "UAH") {
            $system_currency = Currency::query()->where("name", Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where("name", "UAH")->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => "UAH"
            ]);
        }

        $cart->update([
            'is_active' => 0
        ]);

        // Generate data array for Fondy signature
        $data = [
            'server_callback_url' => 'https://' . request()->getHost() . '/api/payments/handle/fondy',
            'response_url' => 'https://' . request()->getHost() . '/profile',
            'order_id' => $payment->id,
            'order_desc' => 'Purchasing digital item on Minecraft Server ' . request()->getHost(),
            'currency' => $config['currency'],
            'amount' => $payment->price * 100,
            'merchant_id' => $config['merchant_id'],
        ];

        $signature = $this->getFondySignature($config['merchant_id'], $config['password'], $data);

        return [
            'type' => 'html',
            'html' => '<form name="tocheckout" method="POST" action="https://pay.fondy.eu/api/checkout/redirect/">
                      <input type="text" name="server_callback_url" value="'. $data['server_callback_url'] .'">
                      <input type="text" name="response_url" value="'. $data['response_url'] .'">
                      <input type="text" name="order_id" value="' . $data['order_id'] . '">
                      <input type="text" name="order_desc" value="' . $data['order_desc'] . '">
                      <input type="text" name="currency" value="' . $data['currency'] . '">
                      <input type="text" name="amount" value="' . $data['amount'] . '">
                      <input type="text" name="signature" value="' . $signature . '">
                      <input type="text" name="merchant_id" value="' . $config['merchant_id'] . '">
                      <input type="submit">
                    </form>'
        ];
    }

    private function getFondySignature($merchant_id, $password, $params = array()) {
        $params['merchant_id'] = $merchant_id;
        $params = array_filter($params,'strlen');
        ksort($params);
        $params = array_values($params);
        array_unshift( $params , $password );
        $params = join('|',$params);
        return(sha1($params));
    }

    private function MidtransMethod($cart, $payment, $currency)
    {
        // New payment method added. Added in 3.0 update. Docs: https://docs.midtrans.com/reference/create-payment-link
        $paymentMethod = PaymentMethod::query()->where('name', 'Midtrans')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        if ($currency != "IDR") {
            $system_currency = Currency::query()->where("name", Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where("name", "IDR")->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => ceil($payment->price),
                'currency' => "IDR"
            ]);
        }

        $cart->update([
            'is_active' => 0
        ]);

        // Get items from the cart
        $items = CartItem::where('cart_id', $cart->id)->get();
        $itemsArray = [];
        foreach ($items as $item) {
            $itemData = Item::where('id', $item->item_id)->first();

            $system_currency = Setting::find(1)->currency;

            $itemPrice = $item->price;
            if ($system_currency != 'IDR') {
                $currencyRate = Currency::where('name', 'IDR')->first()->value;
                $system_currency_rate = Currency::where('name', $system_currency)->first()->value;
                $itemPrice = round($this->toActualCurrency($itemPrice, $currencyRate, $system_currency_rate), 2);
            }

            $itemsArray[] = [
                'id' => $itemData->id,
                'name' => $itemData->name,
                'quantity' => $item->count,
                'price' => ceil($itemPrice),
                'subtotal' => ceil($itemPrice) * $item->count,
            ];
        }

        // Updating the price of the payment based on calculated $itemsArray to avoid issues with ceil
        $payment->price = 0;
        foreach ($itemsArray as $item) {
            $payment->price += $item['subtotal'];
        }

        // Get the value of the server key
        $serverKey = $config['serverKey'];

        // Encode this value into base 64 format
        $authKey = base64_encode($serverKey . ':');

        $headers = [
            'Accept: application/json',
            'content-type:application/json',
            'Authorization: Basic '. $authKey,
        ];

        $fields = [
            'title' => 'Purchasing at ' . request()->getHost(),
            'customer_required' => true,
            'item_details' => $itemsArray,
            'transaction_details' => [
                'order_id' => $payment->id,
                'gross_amount' => ceil($payment->price),
            ],
            'callbacks' => [
                'finish' => "https://" . request()->getHost() . "/payment/flow?order_id=" . $payment->internal_id,
            ],
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.midtrans.com/v1/payment-links", // Sandbox: https://api.sandbox.midtrans.com/ & Production: https://api.midtrans.com/
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if (empty($response) || !$response) {
            return $err;
        }

        $response = json_decode($response, true);

        return [
            'type' => 'url',
            'url' => $response['payment_url'],
        ];
    }

    private function CordariumMethod($cart, $payment, $currency)
    {
        // New payment method added. Added in 3.0 update.

        $paymentMethod = PaymentMethod::query()->where('name', 'Cordarium')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        if ($currency != 'USD') {
            $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where('name', 'USD')->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => 'USD',
            ]);
        }

        $cart->update([
            'is_active' => 0
        ]);

        // Build item array for cart array
        $items = CartItem::where('cart_id', $cart->id)->get();
        $system_currency = Setting::find(1)->currency;

        $itemsArray = [];
        foreach ($items as $item) {
            $itemData = Item::where('id', $item->item_id)->first();
            if ($itemData->discount > 0) {
                $discount = $item->price * ($item->discount / 100);
                $itemPrice = round($item->price - $discount, 2);
            } else {
                $itemPrice = $item->price;
            }
            $hasVariables = false;

            $cartItem = CartItem::where('cart_id', $cart->id)->where('item_id', $itemData->id)->first();
            if ($cartItem) {
                $itemVar = CartItemVar::where('cart_item_id', $cartItem->id)->first();
                if ($itemVar) {
                    $hasVariables = true;
                }
            }

            $system_currency = Setting::find(1)->currency;
            if ($system_currency != 'USD') {
                $currencyRate = Currency::where('name', 'USD')->first()->value;
                $system_currency_rate = Currency::where('name', $system_currency)->first()->value;
                $itemPrice = round($this->toActualCurrency($itemPrice, $currencyRate, $system_currency_rate), 2);
            }

            $itemsArray[] = [
                'id' => $itemData->id,
                'price' => $itemPrice,
                'quantity' => $cartItem->count,
                'image' => 'https://' . request()->getHost()  . '/img/items/'. $itemData->image,
                'name' => $itemData->name,
                'has_variables' => $hasVariables,
            ];
        }

        // Get tax value for cart array
        $tax = 0;
        if ($cart->tax) {
            if ($system_currency !== 'USD') {
                $currencyRate = Currency::where('name', 'USD')->value('value');
                $systemCurrencyValue = Currency::where('name', $system_currency)->value('value');
                $tax = round($this->toActualCurrency($cart->tax, $currencyRate, $systemCurrencyValue), 2);
            } else {
                $tax = $cart->tax;
            }
        }

        // Get coupon data for cart array
        $couponData = null;
        if ($cart->coupon_id) {
            $coupon = Coupon::find($cart->coupon_id);

            if ($coupon) {
                $couponType = $coupon->type === 0 ? '%' : 'USD';

                if ($system_currency !== 'USD') {
                    $currencyRate = Currency::where('name', 'USD')->value('value');
                    $systemCurrencyValue = Currency::where('name', $system_currency)->value('value');
                    $coupon->discount = round($this->toActualCurrency($coupon->discount, $currencyRate, $systemCurrencyValue), 2);
                }

                $couponData = [
                    'name' => $coupon->name,
                    'discount' => $coupon->discount,
                    'type' => $couponType,
                ];
            }
        }

        // Get giftcard data for cart array
        $giftcardData = null;
        if ($cart->giftcard_id) {
            $giftcard = Gift::find($cart->giftcard_id);
            if ($system_currency !== 'USD') {
                $currencyRate = Currency::where('name', 'USD')->value('value');
                $systemCurrencyValue = Currency::where('name', $system_currency)->value('value');
                $cart->gift_sum = round($this->toActualCurrency($cart->gift_sum, $currencyRate, $systemCurrencyValue), 2);
            }

            if ($giftcard) {
                $giftcardData = [
                    'code' => $giftcard->code,
                    'amount' => $cart->gift_sum,
                ];
            }
        }

        // Get the username of the user that owns this cart
        $username = User::find($cart->user_id)->identificator;

        $cart = [
            'items' => $itemsArray,
            'total' => $payment->price,
            'tax' => $tax,
            'coupon' => $couponData,
            'giftcard' => $giftcardData,
            'currency' => 'USD',
        ];

        // Generate data array for Cordarium signature
        $data = [
            'username' => $username,
            'order_id' => $payment->id,
            'currency' => 'USD',
            'amount' => $payment->price,
            'cart' => $cart,
            'server_id' => $config['server_id'],
            'server_callback_url' => 'https://' . request()->getHost() . '/api/payments/handle/cordarium',
            'response_url' => 'https://' . request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
        ];

        $signature = $this->getCordariumSignature($config['server_id'], $config['secret_key'], $data);

        $html = '<form name="tocheckout" method="POST" action="https://app.cordarium.com/api/checkout/minestore/pay">';
        $html .= '<input type="text" name="username" value="' . htmlspecialchars($username) . '">';
        $html .= '<input type="text" name="order_id" value="' . htmlspecialchars($data['order_id']) . '">';
        $html .= '<input type="text" name="currency" value="' . htmlspecialchars($data['currency']) . '">';
        $html .= '<input type="text" name="amount" value="' . htmlspecialchars($data['amount']) . '">';

        // Add input fields for each item in the cart array
        foreach ($data['cart']['items'] as $index => $item) {
            $hasVariablesValue = $item['has_variables'] ? 'true' : 'false';

            $html .= '<input type="hidden" name="cart[items][' . $index . '][id]" value="' . htmlspecialchars($item['id']) . '">';
            $html .= '<input type="hidden" name="cart[items][' . $index . '][name]" value="' . htmlspecialchars($item['name']) . '">';
            $html .= '<input type="hidden" name="cart[items][' . $index . '][quantity]" value="' . htmlspecialchars($item['quantity']) . '">';
            $html .= '<input type="hidden" name="cart[items][' . $index . '][price]" value="' . htmlspecialchars($item['price']) . '">';
            $html .= '<input type="hidden" name="cart[items][' . $index . '][image]" value="' . htmlspecialchars($item['image']) . '">';
            $html .= '<input type="hidden" name="cart[items][' . $index . '][has_variables]" value="' . htmlspecialchars($hasVariablesValue) . '">';
        }

        // Add input fields for coupon if it exists
        if ($data['cart']['coupon']) {
            $coupon = $data['cart']['coupon'];
            $html .= '<input type="hidden" name="cart[coupon][name]" value="' . htmlspecialchars($coupon['name']) . '">';
            $html .= '<input type="hidden" name="cart[coupon][discount]" value="' . htmlspecialchars($coupon['discount']) . '">';
            $html .= '<input type="hidden" name="cart[coupon][type]" value="' . htmlspecialchars($coupon['type']) . '">';
        }

        // Add input fields for giftcard if it exists
        if ($data['cart']['giftcard']) {
            $giftcard = $data['cart']['giftcard'];
            $html .= '<input type="hidden" name="cart[giftcard][code]" value="' . htmlspecialchars($giftcard['code']) . '">';
            $html .= '<input type="hidden" name="cart[giftcard][amount]" value="' . htmlspecialchars($giftcard['amount']) . '">';
        }

        $html .= '<input type="hidden" name="cart[total]" value="' . htmlspecialchars($data['cart']['total']) . '">';
        $html .= '<input type="hidden" name="cart[tax]" value="' . htmlspecialchars($data['cart']['tax']) . '">';
        $html .= '<input type="hidden" name="cart[currency]" value="' . htmlspecialchars($data['cart']['currency']) . '">';
        $html .= '<input type="text" name="signature" value="' . htmlspecialchars($signature) . '">';
        $html .= '<input type="text" name="server_id" value="' . htmlspecialchars($config['server_id']) . '">';
        $html .= '<input type="url" name="server_callback_url" value="'. htmlspecialchars($data['server_callback_url']) .'">';
        $html .= '<input type="url" name="response_url" value="'. htmlspecialchars($data['response_url']) .'">';
        $html .= '<input type="submit">';
        $html .= '</form>';

        return [
            'type' => 'html',
            'html' => $html,
        ];
    }

    private function getCordariumSignature($server_id, $secretKey, $params = array()) {
        $params['server_id'] = $server_id;
        unset($params['cart']);
        $params = array_filter($params,'strlen');
        ksort($params);
        $params = array_values($params);
        array_unshift( $params , $secretKey );
        $params = join('|',$params);
        return hash('sha256', $params);
    }

    private function PayTRMethod($cart, $payment, $currency)
    {
        // New payment method added. Added in 3.0 update. Docs: https://dev.paytr.com/link-api/link-api-create
        // Status: Not Tested

        $paymentMethod = PaymentMethod::query()->where('name', 'PayTR')->first();
        if (!$paymentMethod->enable) return null;
        $config = json_decode($paymentMethod->config, true);

        if ($currency != "TRY") {
            $system_currency = Currency::query()->where("name", Setting::query()->select('currency')->find(1)->currency)->first();
            $currencyRate = Currency::query()->where("name", "TRY")->first();
            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
            $payment->update([
                'price' => $payment->price,
                'currency' => "TRY"
            ]);
        }

        $cart->update([
            'is_active' => 0
        ]);

        $merchant_id = $config['merchant_id'];
        $merchant_key = $config['merchant_key'];
        $merchant_salt = $config['merchant_salt'];

        ## Required Information
        $payment_data = [
            'name'            => "Digital Product at " . request()->getHost(),
            'price'           => round(($payment->price * 100), 2), # For 14.45 TL, send 1445 (price * 100 as an integer)
            'currency'        => "TL",                             # Available: TL, USD, EUR, GBP
            'max_installment' => "12",                             # Between 2 - 12. If 1, individual cards cannot be used for installments.
            'link_type'       => "product",                        # 'collection' (invoice/collecting) or 'product' (product/service sale)
            'lang'            => "tr"                              # 'tr' or 'en'
        ];

        $email = "";
        if (!empty($payment->details)) {
            $details = json_decode($payment->details, true);
            if (isset($details['email'])) {
                $email = $details['email'];
            }
        }

        if ($payment_data['link_type'] === "product") {
            $payment_data['min_count'] = "1";
        } elseif ($payment_data['link_type'] === "collection") {
            $payment_data['email'] = $email;
        }

        $optional_data = [
            'callback_link' => "https://" . request()->getHost() . "/api/payments/handle/paytr",
            'callback_id' => $payment->id,
        ];

        $debug_on = 1;

        // Generating PayTR Token
        $required_fields = $payment_data['name'] . $payment_data['price'] . $payment_data['currency'] .
            $payment_data['max_installment'] . $payment_data['link_type'] . $payment_data['lang'];

        if (isset($payment_data['min_count'])) {
            $required_fields .= $payment_data['min_count'];
        } elseif (isset($payment_data['email'])) {
            $required_fields .= $payment_data['email'];
        }

        $paytr_token = base64_encode(hash_hmac('sha256', $required_fields . $merchant_salt, $merchant_key, true));

        // Build a single array including all data
        $post_data = array_merge($payment_data, $optional_data, [
            'merchant_id'     => $merchant_id,
            'debug_on'        => $debug_on,
            'paytr_token'     => $paytr_token
        ]);

        // Generate Curl Request to create Payment Link
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/link/create");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $result = @curl_exec($ch);

        if (curl_errno($ch)) {
            die("PAYTR LINK CREATE API request timeout. Error: " . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($result, true);

        if ($result['status'] === 'error') {
            Log::error("PAYTR LINK CREATE API error: " . $result['err_msg']);
            var_dump($result['err_msg']);
        } elseif ($result['status'] === 'failed') {
            Log::error('PAYTR LINK CREATE API failed: ' . $result['err_msg']);
            var_dump($result['err_msg']);
        } else {
            return [
                'type' => 'url',
                'url' => $result['link'],
            ];
        }
    }


    private function TBankMethod($cart, $payment, $currency)
    {
        try {

            $paymentMethod = PaymentMethod::query()->where('name', 'TBank')->first();
            if (!$paymentMethod->enable) {
                return null;
            };
            // $config = json_decode($paymentMethod->config, true);
    
            $details = json_decode($payment->details ?? '{}', true);
            if (!isset($details['items']) || !is_array($details['items'])) {
                throw new \Exception('Invalid payment details: items missing or not an array');
            };
    
            // Конвертация валюты при необходимости
            $system_currency = Currency::query()
                ->where('name', Setting::query()->select('currency')->find(1)->currency)
                ->first();
            
            if ($system_currency->name != 'RUB' || $currency != 'RUB') {
                $currencyRate = Currency::query()->where('name', 'RUB')->first();
                $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
                $payment->update([
                    'price' => $payment->price,
                    'currency' => 'RUB',
                ]);
            };
            $items = array_map(function ($item) {
                return [
                    'Name' => $item['name'] ?? '',
                    'Price' => $item['price'] ?? 0,
                    'Quantity' => $item['quantity'] ?? 1,
                    'Amount' => ($item['amount'] ?? ($item['price'] * $item['quantity'])) * 100,
                    'PaymentMethod' => $item['paymentMethod'] ?? 'full_prepayment',
                    'PaymentObject' => $item['paymentObject'] ?? 'commodity',
                    'Tax' => $item['tax'] ?? 'none',
                    'MeasurementUnit' => 'шт'
                ];
            }, $details['items'] ?? []);
            $email = $details['email'];

            // Подготовка параметров для Tinkoff API
            $params = [
                'OrderId' => $payment->id,
                'Amount' => $payment->price * 100, // Конвертация в копейки
                'Description' => 'Оплата заказа #' . $payment->id,
                'TerminalKey' => '1746192279843',
                'NotificationURL' => 'https://' . request()->getHost() . '/api/payments/handle/tbank',
                'SuccessURL' => 'https://'. request()->getHost() .'/success',
                'FailURL' => 'https://'. request()->getHost() .'/error',
                'DATA' => [
                    'Email' => $email ?? '',
                    'Phone' => ''
                ],
                'Receipt' => [
                    "Email" => $email,
                    "Taxation" => "usn_income",
                    "FfdVersion" => "1.2",
                    'Items' => $items
                ]
            ];
    
            // Генерация токена
            $params['Token'] = $this->generateTinkoffToken($params);
    
            // Отправка запроса к API Tinkoff
            $response = $this->sendTinkoffRequest(
                // $config['test'] ? 'https://rest-api-test.tinkoff.ru/v2/Init' : 'https://securepay.tinkoff.ru/v2/Init',
                'https://securepay.tinkoff.ru/v2/Init',
                $params
            );
            
            if (!$response['Success']) {
                Log::error('Tinkoff payment error: ' . json_encode($response));
                return redirect('https://'. request()->getHost() .'/error');
            }
            
            $cart->update(['is_active' => 0]);
            
            return [
                'type' => 'url',
                'url' => $response['PaymentURL']
            ];
        } catch (\Exception $e) {
            Log::error('TinkoffMethod error: ' . json_encode($response));
        }
    }

    private function generateTinkoffToken(array $params): string
    {
        // 1. Собираем только параметры верхнего уровня (без вложенных массивов)
        $tokenData = [
            'TerminalKey' => $params['TerminalKey'],
            'Amount' => $params['Amount'],
            'OrderId' => $params['OrderId'],
            'Description' => $params['Description'],
            'NotificationURL' => $params['NotificationURL'],
            'SuccessURL' => $params['SuccessURL'],
            'FailURL' => $params['FailURL'],
            'Password' => '9kQ4sKoA^i*%xyo8' // Ваш SecretKey из личного кабинета
        ];
    
        // 2. Сортируем массив по ключам в алфавитном порядке
        ksort($tokenData);
    
        // 3. Конкатенируем только значения
        $valuesString = implode('', array_values($tokenData));
    
        // 4. Применяем SHA-256
        return hash('sha256', $valuesString);
    }

    private function sendTinkoffRequest(string $url, array $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception('Tinkoff API error: ' . $error);
        }
        
        return json_decode($response, true);
    }

//    private function PaypalCheckout($cart, $payment, $currency)
//    {
//        $paymentMethod = PaymentMethod::query()->where('name', 'PayPal (Checkout)')->first();
//        if (! $paymentMethod->enable) {
//            return null;
//        }
//        $config = json_decode($paymentMethod->config, true);
//
//        $cart->update([
//            'is_active' => 0,
//        ]);
//
//        $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();
//        if ($system_currency->name != $config['currency'] || $currency != $config['currency']) {
//            $currencyRate = Currency::query()->where('name', $config['currency'])->first();
//            $payment->price = round($this->toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
//            $payment->update([
//                'price' => $payment->price,
//                'currency' => $config['currency'],
//            ]);
//        }
//
//        if ($config['sandbox']) {
//            $url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders';
//        } else {
//            $url = 'https://api-m.paypal.com/v2/checkout/orders';
//        }
//
//        $bearer = $this->getPayPalHeaderBearer($config['client_id'], $config['client_secret'], $config['sandbox']);
//        if ($bearer == null) {
//            return response()->json(['error' => 'Failed to get PayPal bearer'], 500);
//        }
//
//        $headers = [
//            'Content-Type: application/json',
//            'Authorization: Bearer ' . $bearer,
//        ];
//
//        // Get items from the cart
//        $items = CartItem::where('cart_id', $cart->id)->get();
//        $itemsArray = [];
//        foreach ($items as $item) {
//            $itemData = Item::where('id', $item->item_id)->first();
//            if ($itemData->discount > 0) {
//                $discount = $item->price * ($item->discount / 100);
//                $itemPrice = round($item->price - $discount, 2);
//            } else {
//                $itemPrice = $item->price;
//            }
//
//            $cartItem = CartItem::where('cart_id', $cart->id)->where('item_id', $itemData->id)->first();
//            $count = 0;
//            if ($cartItem) {
//                $count = $cartItem->count;
//            }
//
//            $system_currency = Setting::find(1)->currency;
//            if ($system_currency->name != $config['currency'] || $currency != $config['currency']) {
//                $currencyRate = Currency::query()->where('name', $config['currency'])->first();
//                $itemPrice = round($this->toActualCurrency($itemPrice, $currencyRate->value, $system_currency->value), 2);
//            }
//
//            $itemsArray[] = [
//                'name' => $itemData->name,
//                'quantity' => $count,
//                'unit_amount' => [
//                    'currency_code' => $config['currency'],
//                    'value' => ceil($itemPrice),
//                ],
//                'image_url' => 'https://' . request()->getHost() . '/img/items/' . $itemData->image,
//            ];
//        }
//
//
//
//        $body = [
//            'purchase_units' => [
//                [
//                    'items' => $itemsArray,
//                    'amount' => [
//                        'currency_code' => $config['currency'],
//                        'value' => $payment->price,
//                    ]
//                ]
//            ],
//            'intent' => 'CAPTURE',
//            'payment_source' => [
//                'paypal' => [
//                    'experience_context' => [
//                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
//                        'brand_name' => 'EXAMPLE INC',
//                        'locale' => 'en-US',
//                        'landing_page' => 'LOGIN',
//                        'shipping_preference' => 'SET_PROVIDED_ADDRESS',
//                        'user_action' => 'PAY_NOW',
//                        'return_url' => 'https://example.com/returnUrl',
//                        'cancel_url' => 'https://example.com/cancelUrl'
//                    ]
//                ]
//            ]
//        ];
//
//        $ch = curl_init($url);
//
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $response = curl_exec($ch);
//        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//
//        curl_close($ch);
//
//        if ($httpcode == 201) {  // 201 is the status code for a successful order creation in PayPal
//            return response()->json(json_decode($response), 201);
//        } else {
//            return response()->json(['error' => 'Failed to create PayPal order', 'response' => json_decode($response)], $httpcode);
//        }
//    }

    private function getPayPalHeaderBearer($client_id, $client_secret, $sandbox)
    {
        if ($sandbox) {
            $url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
        } else {
            $url = 'https://api-m.paypal.com/v1/oauth2/token';
        }

        // encode client id and secret to base64
        $base64Credentials = base64_encode($client_id . ':' . $client_secret);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US', 'Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_USERPWD, $base64Credentials);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpcode == 200) {
            $response = json_decode($response);
            return $response->access_token;
        } else {
            return null;
        }
    }

    public function paypalHandle(Request $r)
    {
        // Log::error('paypalHandle: ' . json_encode($r->all()));
        $id = $r->get('id');

        $payment = Payment::query()->where([['id', $id], ['status', Payment::PROCESSED]])->first();

        if (empty($payment)) {
            return redirect('/');
        }

        $paymentMethod = PaymentMethod::query()->where('name', 'PayPal')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $paypal = new PayPal([
            'environment' => $config['test'] ? 'sandbox' : 'live',
            'user' => urlencode($config['paypal_user']),
            'pwd' => urlencode($config['paypal_password']),
            'signature' => urlencode($config['paypal_signature']),
            'version' => 113,
        ]);

        $result = $paypal->call([
            'method' => 'DoExpressCheckoutPayment',
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_AMT' => $payment->price,
            'PAYMENTREQUEST_0_CURRENCYCODE' => $payment->currency,
            'token' => urldecode($r->get('token')),
            'payerid' => $r->get('PayerID'),
        ]);
        //Log::error(json_encode($result));
        if (empty($result) || ! isset($result['PAYMENTINFO_0_PAYMENTSTATUS']) || $result['PAYMENTINFO_0_PAYMENTSTATUS'] != 'Completed') {
            return redirect('/');
        }

        $this->FinalHandler($id);

        return redirect('/');
    }

    public function CoinpaymentsHandle(Request $r)
    {
        $merchant = isset($_POST['merchant']) ? $_POST['merchant'] : '';
        if (empty($merchant)) {
            exit('No Merchant ID passed');
        }

        if ($merchant != $this->config->merchant) {
            exit('Invalid Merchant ID');
        }

        $request = file_get_contents('php://input');
        if (empty($request)) {
            exit('Error reading POST data');
        }

        $paymentMethod = PaymentMethod::query()->where('name', 'Coinpayments')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $hmac = hash_hmac('sha512', $request, $config['secret']);
        if ($hmac != $_SERVER['HTTP_HMAC']) {
            exit('HMAC signature does not match');
        }

        if (intval($_POST['status']) < 99 and intval($_POST['status']) != 2) {
            exit('Invalid status');
        }

        $this->FinalHandler($_POST['custom']);

        exit('Accept order, accept code');
    }

    public function G2AHandle(Request $r)
    {
        $orderId = $r->get('order_id');

        //$this->FinalHandler($orderId);

        exit('Accept order, accept code');
    }

    public function Terminal3Handle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Terminal3')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        Log::info('Terminal3Handle: ' . json_encode($r->all()));

        \Paymentwall_Config::getInstance()->set([
            'api_type' => \Paymentwall_Config::API_GOODS,
            'public_key' => $config['public'],
            'private_key' => $config['private'],
        ]);

        $pingback = new \Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
        if ($pingback->validate(true)) {
            $productId = $pingback->getProduct()->getId();
            if ($pingback->isDeliverable()) {
                $subscr = Subscription::where('payment_id', $productId)->first();
                $payment = Payment::where('id', $productId)->first();
                if ($payment->transaction !== null && $payment->transaction == $pingback->getReferenceId()) {
                    Log::error('Terminal3Handle: Transaction ID mismatch for payment ID ' . $productId);
                    exit('ERR');
                }

                $payment->update([
                    'transaction' => $pingback->getReferenceId(),
                ]);

                if ($subscr) {
                    $subscr->update([
                        'sid' => $pingback->getReferenceId(),
                        'payment_method' => 'terminal3',
                        'status' => Subscription::ACTIVE,
                        'count' => $subscr->count + 1,
                        'renewal' => Carbon::now()->addDays($subscr->interval_days)->format('Y-m-d'),
                    ]);
                    if ($subscr->count > 1) {
                        $this->RenewHandler($productId);
                    } else {
                        $this->FinalHandler($productId);
                    }
                } else {
                    $this->FinalHandler($productId);
                }
            } elseif ($pingback->isCancelable()) {
                $subscr = Subscription::where('payment_id', $productId)->first();
                if ($subscr) {
                    $subscr->update([
                        'status' => Subscription::CANCELLED,
                    ]);
                }

                $this->ExpireHandler($productId);
            }
            echo 'OK';
        } else {
            echo 'ERR';
            Log::error(json_encode($pingback->getErrorSummary()));
        }
    }

    public function StripeHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Stripe')->first();
        if ($paymentMethod && !$paymentMethod->enable) {
            return response()->json(['error' => 'Payment method is disabled'], 403);
        }
        $config = json_decode($paymentMethod->config, true);

        $event = null;

        try {
            $event = Stripe\Webhook::constructEvent(
                file_get_contents('php://input'),
                $_SERVER['HTTP_STRIPE_SIGNATURE'],
                $config['whsec']
            );
        } catch (Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }

        $type = $event['type'];
        $object = $event['data']['object'];

        $chargeback = null;
        $orderId = null;

        // Log::error('Stripe Event: ' . json_encode($event));

        if ($type == 'checkout.session.completed') {
            $orderId = $object['metadata']['merchant_order_id'];
        }

        switch ($type) {
            case 'checkout.session.completed':
                Payment::where('id', $orderId)->update([
                    'transaction' => $r->data["object"]["payment_intent"],
                ]);
                $this->FinalHandler($orderId);
                break;
            case 'invoice.payment_succeeded':
                $stripe = new Stripe\StripeClient($config['private']);
                $intents = $stripe->checkout->sessions->all(['payment_intent' => $r->data['object']['payment_intent'], 'limit' => 1]);
                $orderId = $intents['data'][0]['metadata']['merchant_order_id'];
                $customer = $object['customer'];
                $subscription = $object['subscription'];

                $payment = Payment::where('id', $orderId)->first();

                $payment?->update([
                    'transaction' => $r->data['object']['payment_intent'],
                ]);

                $subscr = Subscription::where('payment_id', $orderId)->first();
                if (!$subscr) {
                    return response()->json(['error' => 'Subscription not found'], 404);
                }

                $subscr->update([
                    'sid' => $subscription,
                    'payment_method' => 'Stripe',
                    'customer_id' => $customer,
                    'status' => Subscription::ACTIVE,
                    'count' => $subscr->count + 1,
                    'renewal' => Carbon::now()->addDays($subscr->interval_days)->format('Y-m-d'),
                ]);

                $payment?->update([
                    'internal_subscription_id' => $subscr->id,
                ]);

                if ($subscr->count > 1) {
                    if ($payment) {
                        SubscriptionHelper::replicatePaymentRecord($payment, $subscr);
                    }

                    $this->RenewHandler($orderId);
                } else {
                    $this->FinalHandler($orderId);
                }
                break;

            case 'customer.subscription.pending_update_expired':
            case 'customer.subscription.deleted':
            case 'invoice.payment_failed':
                $stripe = new Stripe\StripeClient($config['private']);
                $intents = $stripe->checkout->sessions->all(['payment_intent' => $r->data['object']['payment_intent'], 'limit' => 1]);
                $orderId = $intents['data'][0]['metadata']['merchant_order_id'];

                Subscription::where('payment_id', $orderId)->update([
                    'status' => Subscription::CANCELLED,
                ]);

                self::ExpireHandler($orderId);
                break;
            case 'charge.dispute.created':
                $stripe = new Stripe\StripeClient($config['private']);
                $intents = $stripe->checkout->sessions->all(['payment_intent' => $r->data['object']['payment_intent'], 'limit' => 1]);
                $orderId = $intents['data'][0]['metadata']['merchant_order_id'];
                Payment::where('id', $orderId)->update(['status' => Payment::CHARGEBACK]);
                Subscription::where('payment_id', $orderId)->update(['status' => Subscription::CANCELLED]);
                Chargeback::create([
                    'payment_id' => $orderId,
                    'sid' => $object['payment_intent'],
                    'status' => Chargeback::PENDING,
                    'details' => json_encode([
                        'case_id' => $object['id'],
                        'case_type' => $object['object'],
                        //'case_creation_date' => Carbon::createFromTimestamp($object['created'])->toDateTimeString(),
                        'status' => $object['status'],
                        'reason' => $object['reason'],
                    ], JSON_THROW_ON_ERROR),
                ]);
                $this->PaymentHandler(Payment::where('id', $orderId)->first());
                $this->ChargebackHandler($orderId);
                break;
            case 'charge.dispute.updated':
                $stripe = new Stripe\StripeClient($config['private']);
                $intents = $stripe->checkout->sessions->all(['payment_intent' => $r->data['object']['payment_intent'], 'limit' => 1]);
                $orderId = $intents['data'][0]['metadata']['merchant_order_id'];
                Payment::where('id', $orderId)->update(['status' => Payment::CHARGEBACK]);
                Subscription::where('payment_id', $orderId)->update(['status' => Subscription::CANCELLED]);
                Chargeback::where('payment_id', $orderId)->update([
                    'status' => Chargeback::PENDING,
                    'details' => json_encode([
                        'case_id' => $object['id'],
                        'case_type' => $object['object'],
                        'status' => $object['status'],
                        'reason' => $object['reason'],
                    ], JSON_THROW_ON_ERROR),
                ]);
                break;
            case 'charge.dispute.closed':
                $stripe = new Stripe\StripeClient($config['private']);
                $intents = $stripe->checkout->sessions->all(['payment_intent' => $r->data['object']['payment_intent'], 'limit' => 1]);
                $orderId = $intents['data'][0]['metadata']['merchant_order_id'];

                if ($object['status'] === 'lost') {
                    Chargeback::where('payment_id', $orderId)->update([
                        'status' => Chargeback::CHARGEBACK,
                        'details' => json_encode([
                            'case_id' => $object['id'],
                            'case_type' => $object['object'],
                            'status' => $object['status'],
                            'reason' => $object['reason'],
                        ], JSON_THROW_ON_ERROR),
                    ]);
                    Payment::where('id', $orderId)->update(['status' => Payment::CHARGEBACK]);
                    Subscription::where('payment_id', $orderId)->update(['status' => Subscription::CANCELLED]);
                    $this->ChargebackHandler($orderId);
                    $this->PaymentHandler(Payment::where('id', $orderId)->first());
                } elseif ($object['status'] === 'won') {
                    Chargeback::where('payment_id', $orderId)->update([
                        'status' => Chargeback::COMPLETED,
                        'details' => json_encode([
                            'case_id' => $object['id'],
                            'case_type' => $object['object'],
                            'status' => $object['status'],
                            'reason' => $object['reason'],
                        ], JSON_THROW_ON_ERROR),
                    ]);
                }
                break;
            case 'charge.refunded':
                $stripe = new Stripe\StripeClient($config['private']);
                $intents = $stripe->checkout->sessions->all(['payment_intent' => $r->data['object']['payment_intent'], 'limit' => 1]);
                $orderId = $intents['data'][0]['metadata']['merchant_order_id'];
                Payment::where('id', $orderId)->update(['status' => Payment::REFUNDED]);
                Subscription::where('payment_id', $orderId)->update(['status' => Subscription::CANCELLED]);
                self::ExpireHandler($orderId);

                break;
            default:
                // Unhandled event type
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function PaytmHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Paytm')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $paytmParams = $_POST;
        // Log::error("PAYTM- ".json_encode($paytmParams));
        $paytmChecksum = $_POST['CHECKSUMHASH'];
        unset($paytmParams['CHECKSUMHASH']);
        $isVerifySignature = PaytmChecksum::verifySignature($paytmParams, $config['mkey'], $paytmChecksum);
        if (! $isVerifySignature) {
            exit();
        }
        // Log::error("PAYTM- ok");

        if ($paytmParams['STATUS'] == 'TXN_FAILURE') {
            exit();
        }

        $id = $paytmParams['ORDERID'];

        $this->FinalHandler($id);

        exit('Accept order, accept code');
    }

    public function PaygolHandle(Request $r)
    {
        exit('Unused');
    }

    public function MollieHandle(Request $r)
    {
        Log::error('MollieHandle: '.json_encode(file_get_contents('php://input')));

        $paymentMethod = PaymentMethod::query()->where('name', 'Mollie')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $mollie = new MollieApiClient();
        $mollie->setApiKey($config['apiKey']);

        $payment = $mollie->payments->get($_POST['id']);
        //if ($payment->isPaid()){
        Log::error('Mollie Response'.json_encode($payment));

        if ($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()) {
            $orderId = $payment->metadata->pay_id;
            $this->FinalHandler($orderId);
        } elseif ($payment->hasChargebacks()) {
            $orderId = $payment['paymentId'];

            $chargeback_data = [
                'resource' => $payment['resource'], // Resource == chargeback
                'chargebackId' => $payment['id'], // example: chb_n9z0tp
                'paymentId' => $orderId, // original paymentId
                'createdAt' => $payment['createdAt'], // date
                'chargeback_status' => $payment['status'],
                'reason' => $payment['reason']['description'], // chargeback reason description
            ];

            Chargeback::create([
                'payment_id' => $orderId,
                'sid' => $payment['payment_intent'],
                'status' => Chargeback::CHARGEBACK,
                'details' => json_encode($chargeback_data),
            ]);
            // $get_payment = $mollie->payments->get($chargeback_data['paymentId']);
            //$orderId = $payment->metadata->pay_id; I don't know would it work, because we don't know $payment->hasChargebacks() response.
            // $orderId = $get_payment['metadata']['order_id'];
            //$this->FinalHandler($orderId);
            //Initiate chargeback procedure
            Payment::where('id', $orderId)->update(['status' => Payment::CHARGEBACK]);
            Subscription::where('payment_id', $orderId)->update(['status' => Subscription::CANCELLED]);
            $this->ChargebackHandler($orderId);
            $this->PaymentHandler(Payment::where('id', $orderId)->first());
        } elseif ($payment->hasRefunds()) {
            // TODO: Is it needed?
        }
    }

    public function CashFreeHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'CashFree')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $response = $r->all();

        if ($response['txStatus'] = 'SUCCESS') {
            $response = substr($response['orderId'], 2);
            $this->FinalHandler($response);

            return redirect('https://'. request()->getHost() .'/success');
        } else {
            Log::error('CashFreeHandle FAIL'.json_encode($response));

            return redirect('https://'. request()->getHost() .'/error');
        }
    }

    public function MercadoPagoHandle(Request $r)
    {
        //Log::error('MercadoPagoHandle: ' . json_encode(file_get_contents('php://input')));

        $paymentMethod = PaymentMethod::query()->where('name', 'MercadoPago')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);

        $data = json_decode(file_get_contents('php://input'), true);

        MercadoPago\SDK::setAccessToken($config['token']);

        if (isset($data["type"])){
            switch($data["type"]) {
                case "payment":
                    if ($data["data"]["id"]) {
                        $payment = MercadoPago\Payment::find_by_id($data["data"]["id"]);
                        if (empty($payment)) return;

                        $orderId = $payment->external_reference;
                        if (!empty($payment->date_approved)) {
                            Log::error('MercadoPago Payment Data: ' . json_encode($payment));
                            Payment::where('id', $orderId)->update([
                                'transaction' => $data['id'],
                            ]);
                            $this->FinalHandler($orderId);
                        } else {
                            Log::error("MercadoPago: ". json_encode($payment));
                        }
                    } else {
                        Log::error("MercadoPago (No payment ID): ". json_encode($data));
                    }
                    break;
                case "plan":
                    $plan = MercadoPago\Plan::find_by_id($data["data"]["id"]);
                    break;
                case "subscription":
                    $plan = MercadoPago\Subscription::find_by_id($data["data"]["id"]);
                    break;
                case "invoice":
                    $plan = MercadoPago\Invoice::find_by_id($data["data"]["id"]);
                    break;
                case "point_integration_wh":
                    // $_POST contains the information related to the notification.
                    break;
            }
        } else {
            Log::error('MercadoPagoHandle FAIL: ' . json_encode($data));
        }
    }

    public function GoPayHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'GoPay')->first();
        if (! $paymentMethod->enable) {
            return response()->json(['error' => 'Payment method is disabled'], 403);
        }

        $config = json_decode($paymentMethod->config, true);
        $gopay = GoPay\payments([
            'goid' => $config['goid'],
            'clientId' => $config['ClientID'],
            'clientSecret' => $config['ClientSecret'],
            'gatewayUrl' => $config['test'] ? 'https://gw.sandbox.gopay.com/api/' : 'https://gate.gopay.cz/api/',
        ]);

        $response = $gopay->getStatus($r->all()['id']);

        if ($response->hasSucceed()) {
            if ($response->json['state'] === 'PAID') {
                $orderId = $response->json['order_number'];
                $this->FinalHandler($orderId);
            }

            if (in_array($response->json['state'], ['CANCELED', 'TIMEOUTED'])) {
                $payment = Payment::query()
                    ->where([['id', $response->json['order_number']], ['status', Payment::PROCESSED]])
                    ->first();

                if ($payment) {
                    $payment->update([
                        'status' => Payment::PROCESSED,
                    ]);
                }
            }

            if (isset($response->json['recurrence'])) {
                // Subscription handling
                if ($response->json['recurrence']['recurrence_state'] == 'STARTED') {
                    $subscription_data = [
                        'subscriptionId' => $response->json['id'],
                        'renewal_date' => $response->json['recurrence']['recurrence_date_to'],
                        'status' => $response->json['recurrence']['recurrence_state'],
                    ];

                    $subscription = Subscription::where('payment_id', $response->json['order_number'])->first();

                    if ($subscription) {
                        $subscription->update([
                            'sid' => $subscription_data['subscriptionId'],
                            'payment_method' => 'gopay',
                            'status' => Subscription::ACTIVE,
                            'renewal' => $subscription_data['renewal_date'],
                        ]);
                    }

                    $orderId = $response->json['order_number'];
                    $this->FinalHandler($orderId);
                }
                if ($response->json['recurrence']['recurrence_state'] == 'STOPPED') {
                    // CLOSE SUBSCRIPTION
                    $orderId = $response->json['order_number'];
                    Subscription::where('payment_id', $orderId)->update([
                        'status' => Subscription::CANCELLED,
                    ]);
                    $this->ExpireHandler($orderId);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } else {
            echo "oops, API returned {$response->statusCode}: {$response}";
        }
    }

    public function RazorPayHandle(Request $r)
    {
        $inputData = $r->all();

        $paymentMethod = PaymentMethod::query()->where('name', 'RazorPay')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $api = new RazorpayApi($config['api_key'], $config['api_secret']);

        $success = true;

        if (! empty($inputData['razorpay_payment_id'])) {
            try {
                $attributes = [
                    'razorpay_signature' => $inputData['razorpay_signature'],
                    'razorpay_payment_id' => $inputData['razorpay_payment_id'],
                    'razorpay_order_id' => $inputData['razorpay_order_id'],
                ];
                $api->utility->verifyPaymentSignature($attributes);
            } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
                $success = false;
            }
            if ($success) {
                $this->FinalHandler($inputData['id']);

                return redirect('https://'. request()->getHost() .'/success');
            }
        }

        return redirect('https://'. request()->getHost() .'/error');
    }

    public function FreeKassaHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'FreeKassa')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $sign = md5($r->MERCHANT_ID.':'.$r->AMOUNT.':'.$config['secret'].':'.$r->MERCHANT_ORDER_ID);
        if ($sign != $r->SIGN) {
            exit('wrong sign');
        }

        $this->FinalHandler($r->MERCHANT_ORDER_ID);

        exit('OK');
    }

    public function QiwiHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Qiwi')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $billPayments = new Qiwi($config['private_key']);
        $notificationData = json_decode(file_get_contents('php://input'), true);
        $result = $billPayments->checkNotificationSignature($_SERVER['HTTP_X_API_SIGNATURE_SHA256'], $notificationData, $config['private_key']);

        if (! $result) {
            exit('FAIL');
        }

        $this->FinalHandler($notificationData['bill']['billId']);

        header('Content-Type: text/xml');
        echo "<?xml version=\"1.0\"?>\n<result>\n<result_code>0</result_code>\n</result>";
        exit();
    }

    public function EnotHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Enot')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $sign = md5($config['id'].':'.$_REQUEST['amount'].':'.$config['secret2'].':'.$_REQUEST['merchant_id']);
        if ($sign != $_REQUEST['sign_2']) {
            exit('bad sign!');
        }

        $this->FinalHandler($_REQUEST['merchant_id']);

        exit('OK');
    }

    public function PayUHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'PayU')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);

        $response = $r->all();

        $correctResponse = json_encode($response);
        $correctResponse = str_replace('\/', '/', $correctResponse);

        $expected_signature = md5($correctResponse . $config['key']);
        Log::error('ExpSignature >> ' . $expected_signature);
        $incoming_signature = $r->header('Openpayu-Signature');
        Log::error($incoming_signature);

        $parts = explode(';', $incoming_signature);
        $signature_part = '';

        foreach ($parts as $part) {
            $keyValue = explode('=', $part);
            if ($keyValue[0] === 'signature') {
                $signature_part = $keyValue[1];
                break;
            }
        }

        //Log::error("PayUHandle: ".json_encode($response));

        if($expected_signature === $signature_part){
            if ($response["order"]["status"] == "COMPLETED"){
                $response = $response["order"]["extOrderId"];
                $this->FinalHandler($response);
            } else {
                Log::error("PayUHandle FAIL". json_encode($response));
                return redirect('https://' . $_SERVER['HTTP_HOST'] . '/error');
            }
        } else {
            Log::error("PayU Header Signatures != match!");
        }
    }

    public function PayUIndiaHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'PayUIndia')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);

        $response = $r->all();

        Log::error("PayUIndiaHandle: ".json_encode($response));

        if ($response["status"] == "success" && $response['key'] == $config['key']){
            $response = $response["txnid"];
            $this->FinalHandler($response);
        } else {
            Log::error("PayUHandle FAIL". json_encode($response));
            return redirect('https://' . $_SERVER['HTTP_HOST'] . '/error');
        }
    }

    public function HotPayHandle(Request $r)
    {
        // New payment method. Updated in v3.1.5 version.
        // Documentation: https://dokumentacja.hotpay.pl/#odbior-notyfikacji

        $paymentMethod = PaymentMethod::query()->where('name', 'HotPay ')->first();
        if (! $paymentMethod->enable) {
            return response()->json(['error' => 'Payment method is disabled'], 403);
        }

        $config = json_decode($paymentMethod->config, true);
        $response = $r->all();

        // Allowed IPs for HotPay
        $allowedIPs = [
            '18.197.55.26',
            '3.126.108.86',
            '3.64.128.101',
            '18.184.99.42',
            '3.72.152.155',
            '35.159.7.168'
        ];

        if (! in_array($r->ip(), $allowedIPs)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        //Log::error("HotPay_Handle: ".json_encode($response));
        if (! empty($response['KWOTA']) && ! empty($response['ID_PLATNOSCI']) && ! empty($response['ID_ZAMOWIENIA']) && ! empty($response['STATUS']) && ! empty($response['SEKRET'])) {
            if ($response["STATUS"] == "SUCCESS" && $response["SEKRET"] = $config["sekret"]) {
                $orderID = $response['ID_ZAMOWIENIA'];
                $this->FinalHandler($orderID);
                echo "Płatność została poprawnie opłacona";
            } else if($response["STATUS"] == "FAILURE") {
                Log::error("HotPay_Handle FAIL". json_encode($response));
                echo "Płatność zakończyła się błędem";
            } else {
                echo "Płatność oczekuje na realizację";
            }
        }
    }

    public function InterkassaHandle(Request $r)
    {
        // New payment method. Added in v2.6 version.
        $paymentMethod = PaymentMethod::query()->where('name', 'Interkassa')->first();
        if (! $paymentMethod->enable) {
            return;
        }
        $config = json_decode($paymentMethod->config, true);

        $response = $r->all();

        //Log::error("Interkassa Handle >> ".json_encode($response));

        if ($response['ik_inv_st'] == 'success') {
            $response = $response['ik_pm_no'];
            $this->FinalHandler($response);
        } else {
            $this->FailHandler($response);
        }
    }

    public function CoinbaseHandle(Request $r)
    {
        // New payment method. Added in v3.0 version.
        // Documentation: https://docs.cloud.coinbase.com/commerce/reference/createcharge

        $paymentMethod = PaymentMethod::query()->where('name', 'Coinbase')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);

        $data = json_decode(file_get_contents('php://input'), true);

        $hmac = hash_hmac('sha256', $data, $config['webhookSecret']);

        if ($hmac != $r->header('X-CC-Webhook-Signature')) {
            exit('HMAC signature does not match');
        }

        switch($data["type"]) {
            case "charge:confirmed":
                $orderId = $data['event']['metadata']['customer_id'];
                $this->FinalHandler($orderId);
                break;
            case "charge:created":
            case "charge:failed":
            case "charge:pending":
            case "charge:delayed":
            case "charge:resolved":
                break;
        }
    }

    /**
     * @throws SkrillException
     */
    public function SkrillHandle(Request $r)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Skrill')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);
        $response = $r->all();
        $signature = $this->skrillVerifySignature($response, $config['signature']);

        if ($signature !== $response['md5sig']) {
            Log::error('Error by accessing with not verified signature! Income signature: ' . $response['md5sig'] . ' Expected signature: ' . $signature);
            return 'Error by accessing with not verified signature!';
        }

        if ($response['pay_to_email'] !== $config['email']) {
            Log::error('Error by accessing with not verified email! Income email: ' . $response['pay_to_email'] . ' Expected email: ' . $config['email']);
            return 'Error by accessing with not verified email!';
        }

        switch ((int)$response['status']) {
            case 2:
                // STATUS_PROCESSED
                $orderID = $response['transaction_id'];
                $this->FinalHandler($orderID);
                http_response_code(200);
                break;
            case -3:
                // STATUS_CHARGEBACK
                Chargeback::create([
                    'payment_id' => $response['transaction_id'],
                    'sid' => $response['mb_transaction_id'],
                    'status' => Chargeback::CHARGEBACK,
                    'details' => json_encode([
                        'case_id' => $response['mb_transaction_id'],
                        'case_type' => 'chargeback',
                        'status' => 'chargeback',
                        'reason' => 'Visit Skrill account for more details',
                    ]),
                ]);

                $this->PaymentHandler(Payment::where('id', $response['transaction_id'])->first());
                $this->ChargebackHandler($response['transaction_id']);
                break;
            case -1:
                // STATUS_CANCELED
            case 0:
                // STATUS_PENDING
                break;
            case -2:
                // STATUS_FAILED
                // Note that you should enable receiving failure code in Skrill account
                $errorCode = $response['failed_reason_code'];
        }
    }

    private function skrillVerifySignature($response, $secretWord): string
    {
        return strtoupper(md5(implode('', [
            $response['merchant_id'],
            $response['transaction_id'],
            strtoupper(md5($secretWord)),
            $response['mb_amount'],
            $response['mb_currency'],
            $response['status'],
        ])));
    }

    public function FondyHandle(Request $r)
    {
        // New payment method. Added in v3.0 version.

        $paymentMethod = PaymentMethod::query()->where('name', 'Fondy')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);

        $response = $r->all();
        $data = $response;
        unset($data['response_signature_string']);
        unset($data['signature']);

        $signature = $this->getFondySignature($config['merchant_id'], $config['password'], $data);

        if ($signature !== $response['signature']) {
            Log::error("Fondy Bad Signature >> ".json_encode($response));
            exit('BAD SIGNATURE');
        }

        if ($response['order_status'] !== 'approved') {
            exit('PAYMENT NOT APPROVED');
        }

        if ($response["order_status"] === "approved") {
            $order_id = $response["order_id"];
            $this->FinalHandler($order_id);
        }
    }

    public function MidtransHandle(Request $request)
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Midtrans')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);

        //Log::info('MidtransHandle: '.json_encode($request->all()));

        $response = $request->all();
        $orderId = $response['order_id'];
        $orderId = strstr($orderId, '-', true);

        $receivedSignature = $response['signature_key'];
        $generatedSignature = hash('sha512', $response['order_id'].$response['status_code'].$response['gross_amount'].$config['serverKey']);

        if ($receivedSignature !== $generatedSignature){
            exit('BAD SIGNATURE');
        }

        if ($response['transaction_status'] === 'capture' || $response['transaction_status'] === 'settlement') {
            if ($response['status_code'] == '200') {
                $this->FinalHandler($orderId);
            }
        }
    }

    public function CordariumHandle(Request $r)
    {
        // New payment method. Added in v3.0 version.

        $paymentMethod = PaymentMethod::query()->where('name', 'Cordarium')->first();
        if (!$paymentMethod->enable) return;
        $config = json_decode($paymentMethod->config, true);

        $response = $r->all();
        $data = $response;
        unset($data['signature']);

        $signature = $this->getCordariumSignature($config['server_id'], $config['secret_key'], $data);

        if ($signature !== $response['signature']){
            exit('BAD SIGNATURE');
        }

        $payment = Payment::where('id', $response['order_id'])->first();
        if (!$payment) {
            exit('UNABLE TO FIND THE PAYMENT');
        }

        if (abs($payment->price - $response['amount']) > 0.0001) {
            exit('BAD AMOUNT');
        }

        if ($response['order_status'] !== 'completed') {
            exit('PAYMENT NOT APPROVED');
        }

        if ($response["order_status"] === "completed"){
            $order_id = $response["order_id"];
            $this->FinalHandler($order_id);
            exit('OK');
        }
    }

    public function PayTRHandle(Request $request)
    {
        // New payment method. Added in v3.0 version.
        // Link: https://dev.paytr.com/link-api/linkle-api-callback

        $paymentMethod = PaymentMethod::query()->where('name', 'PayTR')->first();
        if (!$paymentMethod->enable) {
            return response('PayTR Payment Method is not enabled', 400);
        }
        $config = json_decode($paymentMethod->config, true);

        $merchant_id = $config['merchant_id'];
        $merchant_key = $config['merchant_key'];
        $merchant_salt = $config['merchant_salt'];

        $post = $request->all();

        // Generate hash using POST response data
        $hash = base64_encode(
            hash_hmac(
                'sha256',
                $post['callback_id'] . $post['merchant_oid'] . $merchant_salt . $post['status'] . $post['total_amount'],
                $merchant_key,
                true
            )
        );

        if ($hash != $post['hash']) {
            return response('PayTR Notification failed: Bad Hash', 400);
        }

        if ($post['merchant_id'] !== $merchant_id) {
            return response('PayTR Notification failed: Bad Merchant ID', 400);
        }

        if ($post['status'] === 'success') {
            $order_id = $post['callback_id'];
            $this->FinalHandler($order_id);
            exit('OK');
        } else {
            Log::error('PayTR Handle Request Failed: '.json_encode($post));
            return response('PayTR Request Failed', 400);
        }
    }

    public function FinalHandler($paymentId)
    {
        $payment = Payment::query()->with(['user'])->where([['id', $paymentId], ['status', Payment::PROCESSED]])->first();
        if (! $payment) {
            exit('Payment already paid');
        }

        global $result, $cart;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC3\x02\x28\x71\x9E\x47\x10\xF8\x40\x8A\xA7\x39\x29\x79\xA3\xF7\x83\x64\x63\x65\x5F\xED\x23\x0F\xCD\xBE\xF9\xCC\xFB\xE1\x38\x82\x70\x91\x58\xFE\x01\x36\x87\x87\xB4\x3F\xEB\xA5\x96\xAE\xBB\x68\xEA\xCF\x71\x04\xF4\xD0\xCA\xF5\x23\x0A\xA7\x72\xBA\xE0\x69\x12\x73\x33\x5F\x8D\xE2\x36\x32\x77\xF8\x7C\x70\xF7\x5F\x6A\x24\x0E\xAC\x3C\x76\xF5\x00\xCF\x60\x67\x1A\x21\x83\x6D\x6A\x88\xAA\x0D\xC9\xBE\x8E\x48\xF7\xB0\xC6\xB1\x6F\xA4\xA0\x3C\x42\x92\x7C\x13\x14\x11\x2E\xFC\xBE\x66\xAF\x3E\x0F\x18\xBD\xA7\xD7\xA9\xCF\x0E\xB1\x2F\x38\x22\x8A\x87\x2C\xEF\x9C\xE2\xCC\xBB\xDB\xE1\x58\xF0\xAF\xB1\x79\x1C\x16\x01\x46\xAA\x33\x61\xB0\x4A\x26\x9E\xB7\x73\x90\x5D\x27\x75\x5D\x8C\x09\x2C\x90\x51\xDD\x8D\xB0\x8B\x82\xB4\xF4\xFE\x01\x74\x09\x73\xC2\xE4\x2E\xED\x4A\x38\xA0\xD0\x13\xE2\x30\xF0\x2F\xF2\x07\x23\xE8\x85\x86\x13\x82\x03\x5E\xEF\x4A\x38\xAC\x94\x03\x2D\xD3\xD9\xDF\x55\x1A\x97\x90\x12\xAC\x58\xA9\x7B\x0B\xBC\xFC\x5D\x0D\x7A\xE6\xDF\x40\x81\x03\xBD\xB1\x39\x96\xE0\x7C\xB7\x14\x7B\x7D\xF4\x56\x8D\xAD\xE9\x30\xD4\x07\xE2\x42\xF6\x0C\xCF\x73\x30\xF0\x70\xF3\x1D\xF8\xA3\x1E\x29\x73\xCC\x0C\xA9\x02\x70\xDB\x78\xC8\xAD\x08\x45\x42\x3A\xF9\xA2\xAB\x00\x54\x78\xB9\x26\x29\xCD\x3F\xAB\x59\x1B\xA3\x5F\xE9\x03\x72\x5E\xF4\xFC\x24\x48\x30\x82\xB7\x4E\x9D\x1B\x38\x61\x7B\xEF\x7C\x6A\xC8\xAF\x68\xD3\x90\xB1\x03\x25\x44\x05\xB9\xB1\xBF\x81\x23\x32\x46\x08\x45\x4F\x2A\x53\x82\xF6\xD7\xB4\xBE\xB0\x37\xF0\xA1\x3C\xE8\xF5\x04\xF4\xDD\xA2\x86\xE0\x3A\x98\x1B\x03\xF2\xD0\x3F\xCA\x4B\x6C\xCA\xC8\x77\x91\x93\x92\xEE\xCD\xF5\xFA\x48\xD4\xE5\x43\xE0\xE8\xF4\xBB\x12\x0D\xC1\x94\x2E\x7B\xB4\x39\xA5\x3E\xCC\x1F\x8B\x03\x83\x95\x19\x32\x1F\x21\x8F\x9A\xB7\x4F\x40\xDD\xB9\x0B\x86\xD2\x16\x9A\x66\x1C\x24\x34\x5F\x8A\xB0\x6E\x23\xEB\x33\xF6\x9B\x9C\x1F\x6D\xDE\x98\x9E\xA7\x9C\x42\x07\x1A\x6E\x25\x65\xD4\x8E\x19\xFE\xB7\x28\x7F\x48\x8B\xE5\xA7\xA2\x10\xF9\xB0\x49\xD4\x99\xC8\xFA\x42\xF4\x18\x62\x5E\x0A\x13\x84\x90\x14\x68\xA8\x32\x27\xFC\x15\x3B\xDE\xD4\x00\x0B\x22\x3E\x72\x2C\xB2\xDD\xFC\x58\xBA\x47\x26\xE1\xAB\x14\x4B\x8A\xD7\xEF\x44\x3A\x87\xB7\x6A\x53\x26\x30\xA1\xCF\xF2\x29\x23\xAA\xE9\xA0\xE3\xAD\xB4\xB6\xED\x43\xF6\x0E\x5D\x38\xF8\x23\xE0\x92\xF6\xB0\x08\xFB\xD9\x7C\x5B\xD3\xB6\x9C\x8F\xB0\xD0\xB3\xC2\xCA\x65\x76\x51\xF5\xB1\x3E\xA0\x55\x25\xA0\x78\x5B\x88\x67\x55\x15\xB1\x7D\x12\x65\xF8\x27\x01\xE6\xFC\x97\x68\x97\x98\x63\x7E\xB5\x6B\x6A\xE2\x92\x7E\xBF\x2E\x0A\xF1\xD2\xA7\xAD\x9A\x4F\x09\x0F\x7E\xB3\xB4\x0B\x24\x36\x4E\x05\x93\xA1\xA7\x8F\x59\x2D\xDF\x73\x20\x3C\x7E\x51\xFE\x34\x29\x17\xD1\xEE\xE3\xC0\xDC\x09\xF7\x8C\xD0\x0F\x26\x01\x36\xF3\xA8\x68\xD1\x97\x45\xA3\x1C\x4B\x0C\x6D\x32\x0E\x7D\x36\x53\x13\x92\x62\xA6\xA0\xBE\xD9\xF1\x29\x06\x78\x93\xEB\xE6\xA1\xA1\x9B\xD2\x80\xA0\xD9\xC8\xBE\x07\xD2\x8C\x94\x73\x1D\x12\x6B\x78\xEC\x61\xD9\xA6\x38\x38\x5E\x0D\xDA\x46\xB9\xBE\x09\xD4\x24\xBD\xAA\x92\x8A\xCA\x14\x41\x66\x6E\x9C\x18\xC9\x9A\x12\x96\x85\xC4\x95\xD9\xCF\x04\xCB\xB6\x1C\x58\x6E\x81\x33\xEA\xE9\x3E\x03\xFA\x35\x1F\x80\xF9\xF9\xBE\x0A\x9E\xBD\x16\xA9\x54\x83\xEC\x64\x47\x29\x92\xE7\xA5\xD7\x7B\xED\xBD\x1D\xA9\xDE\x85\x8F\xCE\x31\xD3\xA9\xD4\x54\xF7\xFA\x06\x80\xA3\xE3\xFA\x10\x23\xC1\x39\x73\x56\x0D\x2F\x5B\x51\x1E\x80\xFA\x7B\x3B\xA5\x38\xA8\x42\x60\x50\x69\x86\xE9\x92\x8F\x2E\xF9\x9D\x92\xDA\xAB\x62\xED\xBA\xA3\xE3\x02\x37\x42\xEA\x9A\x8D\x6E\xB5\x3E\xAE\x8F\xC6\x0D\x78\x59\x2D\x56\xCD\x00\xC6\x3F\xAF\xE4\xD0\x47\x16\x9F\x24\xD6\x5F\x7E\xFB\xA7\xCA\x01\x1D\x18\x05\x10\x39\x52\xF6\x33\xE2\x0E\x2E\x8C\x4B\x23\xCA\xC2\x26\x24\x74\x9B\x09\x98\x23\xB3\x8E\x8C\x2C\x4B\xD9\x8C\xEF\x0E\x9C\x01\x6B\xA2\x7D\xAC\x5E\x9A\x92\xFB\x27\x4D\x96\x0D\x02\x96\xF0\xA0\x4B\xB7\xE5\x05\xBB\x1C\xB7\xAD\x59\xAB\x9C\xCA\xD9\x19\x8C\x4E\xFF\x43\x72\x37\xF8\x6A\xE2\x9B\x1B\x2C\xF5\x70\x98\xA1\xBD\x1D\xE6\xFF\xB4\x22\xED\x4C\xFC\x10\x92\x65\xAA\xD8\x2F\x32\xB6\xC6\x43\xE1\x9F\x91\x06\x69\x91\xB8\x1B\xAB\x89\xF3\xA9\x1D\x8F\x93\xE7\xB1\xC0\x53\xE3\x5D\x98\x17\xD0\xA1\xCF\x30\x45\x4D\x05\xA7\xC5\xB3\x4E\xDC\xAD\xDC\x46\xD6\xF3\xEB\x51\xC3\x69\xCD\xBD\x6F\x5B\x10\x72\x11\x14\x15\x1A\x17\x79\xF6\xDC\x0B\x44\x18\x94\xFF\xD0\x81\x6B\x9E\x91\xDC\xDE\xB8\xD7\xE1\xB9\x0F\xCF\xF7\xE4\x55\x16\x15\xED\xB6\xBD\x12\x4B\x32\xA4\x93\xFC\x63\x3B\x00\x51\x80\x7C\xB1\x2C\x9B\xED\x02\xF3\x20\x0B\x8A\x49\x7E\xB1\xFD\x1C\xC5\x83\xDD\x86\x00\xCD\xDE\x47\x03\xBE\xC6\x08\x63\x8E\x3F\xA6\x3A\xB5\xE0\x52\xDC\x2B\xB7\x86\x84\x88\x56\xCE\x0B\x8E\xA1\x71\x93\xEF\xDD\x88\x05\xC2\xF5\x70\x28\x2C\xE9\x54\xA2\x14\x7C\x4F\xFD\xF2\x2A\xE0\x35\x54\x35\x5D\x87\xB9\x78\xCD\x58\xF0\xC8\xC3\xD3\xEC\x94\xC6\x97\xB6\xCA\x88\x41\xF0\xAD\x37\x49\x06\x83\x4D\x14\x66\xF5\xE1\x68\x46\x49\xAA\x51\x8C\x1E\xDD\xEA\x77\x4B\x99\x57\x4B\x84\x53\x80\x42\xBC\x03\xC4\x3F\x48\xC2\x3C\x66\x22\x2B\x5A\x68\x2D\x20\x88\x09\xCC\xA5\x6A\x57\xCB\xE0\x74\x16\x4F\x06\x2E\xE3\xCD\xF8\x6E\x89\xF7\x6D\x6F\x15\x8D\x23\x14\x86\x8A\xA0\x27\xFC\x5F\x98\xE9\xD2\x65\x72\xC4\xE2\x80\x32\x0C\xAC\x9B\xE5\xE1\xE8\x26\xFB\x19\xB6\xE2\x44\xB9\x0F\xC1\x47\x2E\xFB\x21\xAF\x98\xC7\xC9\x58\x9D\xF2\x67\x0E\xA6\xAC\x08\xBB\x38\x46\x9B\x49\xBA\x32\x53\x51\x13\xD1\x33\x92\x5C\x10\xFE\x51\x2A\xAF\x7E\xB2\xB1\x09\xAF\x44\x0C\xE0\x7E\xCD\xCC\x8B\x91\x4C\xB4\xFF\x59\x28\x18\xC0\x1C\x5D\xDA\xFE\xA0\x66\xF8\xEA\x9F\x7E\x79\x56\xA2\xAB\x8B\xCB\x1A\x96\x93\x71\xF1\x48\xD5\xE5\xC9\x53\x70\x89\xA1\xDF\x2B\x0E\x1F\x2B\x54\xEE\x1F\xA7\x89\x58\xF0\x6C\x53\x3B\xED\x78\x93\x95\x41\xE0\x95\x9F\xB9\x61\x1F\xC5\x6D\xE6\x4A\x08\x5B\x13\x20\xC8\x29\x3D\xF0\xC6\x05\x87\xD2\x8F\xAA\x78\xB3");
        ProcessCartSubscriptionsJob::dispatch($cart->id);

        return true;
    }

    public function RenewHandler($paymentId)
    {
        $payment = Payment::query()->with(['user'])->where('id', $paymentId)->first();
        if (! $payment) {
            exit('Unable to find the payment!');
        }

        global $result;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC3\x02\x28\x71\x9E\x47\x10\xF8\x40\x8A\xA7\x39\x29\x79\xA3\xF7\x83\x64\x63\x65\x5F\xED\x23\x0F\xCD\xBE\xF9\xCC\xFB\xE1\x38\x82\x70\x91\x58\xFE\x01\x36\x87\x87\xB4\x3F\xEB\xA5\x96\xAE\xBB\x68\xEA\xCF\x71\x04\xF4\xD0\xCA\xF5\x23\x0A\xA7\x72\xBA\xE0\x69\x12\x73\x33\x5F\x8D\xE2\x36\x32\x77\xF8\x7C\x70\xF7\x5F\x6A\x24\x0E\xAC\x3C\x76\xF5\x00\xCF\x60\x67\x1A\x21\x83\x6D\x6A\x88\xAA\x0D\xC9\xBE\x8E\x48\xF7\xB0\xC6\xB1\x6F\xA4\xA0\x3C\x42\x92\x7C\x13\x14\x11\x2E\xFC\xBE\x66\xAF\x3E\x0F\x18\xBD\xA7\xD7\xA9\xCF\x0E\xB1\x2F\x38\x22\x8A\x87\x2C\xEF\x9C\xE2\xCC\xBB\xDB\xE1\x58\xF0\xAF\xB1\x79\x1C\x16\x01\x46\xAA\x33\x61\xB0\x4A\x26\x9E\xB7\x73\x90\x5D\x27\x75\x5D\x8C\x09\x2C\x90\x51\xDD\xA7\x95\x84\x82\xC0\x9B\x9A\x6E\x6E\x09\x3A\xD1\xF8\x7D\xFA\x43\x6C\xEE\x88\x56\xDA\x51\xE3\x37\xCF\x21\x30\xF9\xD5\xAE\x35\x80\x1D\x46\xFC\x51\x31\xE0\x97\x1E\x2C\xAF\xE2\xCE\x5E\x12\x93\xB2\x11\xFD\x26\xD1\x34\x47\xF0\xB9\x0F\x17\x60\xA1\x92\x44\x81\x39\xBC\xB8\x20\xC5\xF5\x78\x9B\x34\x72\x60\xCD\x70\x8D\xF0\xA2\x66\x9D\x48\xAC\x16\xA4\x43\x83\x3F\x75\xEB\x65\x8F\x7C\xA8\xB4\x16\x29\x45\xCF\x16\xF4\x50\x64\xBD\x34\x8D\xFF\x12\x5F\x05\x73\xAF\xE7\xC2\x54\x11\x31\xBA\x6F\x74\xD0\x3B\xBC\x40\x53\xF3\x5E\xB5\x47\x52\x5A\xE1\x88\x32\x2D\x74\xC7\xFB\x1D\xE1\x78\x77\x2C\x36\xAE\x32\x2E\xD2\xB5\x0D\xA5\xF2\x8C\x23\x1B\x62\x35\x84\xD3\xC8\xEF\x34\x29\x0D\x72\x15\x33\x47\x1C\xC6\xB3\x9B\xE7\x8B\x86\x76\xA1\xE8\x2B\xE3\xF2\x4B\xA2\xF9\xCA\xEF\xDF\x1C\xB2\x1B\x03\xF2\xD0\x3F\xCA\x4B\x6C\xCA\xC8\x77\x95\xC3\xD3\xB7\x80\xB7\xF7\x4E\x9C\xBA\x42\xF5\xE8\xCA\xAE\x03\x02\xBA\xA3\x30\x7B\xD7\x78\xF7\x7C\x83\x51\xF7\x60\xC2\xC7\x5B\x7D\x51\x3B\x92\x87\xAC\x59\x1C\x81\xE6\x26\x86\xCF\x08\x9A\x07\x4C\x74\x48\x32\xC5\xF4\x2B\x12\xB1\x54\xAC\xDA\xC5\x52\x28\x90\xCC\x84\xBD\xEC\x23\x6E\x7E\x27\x59\x20\x9A\xDA\x11\xB0\xF2\x7F\x7F\x29\xDB\xB5\xDB\xBA\x4F\xA7\xD4\x1D\x87\xE5\xB8\xBB\x1B\xB9\x5D\x2C\x0A\x7A\x52\x88\x82\x59\x22\xAC\x7B\x30\xF4\x07\x75\xEB\x8D\x59\x6C\x6D\x68\x37\x62\xE6\x8E\x80\x28\xFB\x43\x6B\xE1\xA9\x13\x7E\xCB\xC5\x81\x4C\x3E\xD7\xF6\x33\x1E\x63\x7E\xF5\xC6\xFB\x32\x0D\xFA\xA8\xF9\xAE\xE8\xFA\xE2\xE0\x00\xA3\x1B\x55\x2A\xE9\x66\xB3\xC3\xDC\xB0\x08\xFB\xD9\x7C\x5B\xD3\xB6\x9C\x8F\xB0\xD4\xE3\x83\x93\x28\x34\x4C\xF5\xFD\x74\xA0\x56\x66\xE1\x31\x00\x80\x7D\x2F\x45\xCD\x10\x5D\x21\xBD\x6B\x52\x9A\x8C\xD6\x31\xDA\xDD\x2D\x2A\xA8\x22\x5B\xD1\xB4\x44\x9E\x25\x20\xEC\xCC\xA7\xCC\xCA\x1F\x75\x62\x31\xF7\xF1\x47\x77\x4A\x3E\x44\xCD\xAF\xB0\x84\x4C\x63\x80\x52\x2D\x0F\x45\x24\xF2\x03\x37\x17\xB2\xAF\xB1\x82\x93\x47\x8B\xEF\x91\x5D\x19\x47\x63\xC3\xB2\x26\x9E\xC0\x4D\xAA\x10\x61\x51\x47\x18\x0E\x7D\x36\x53\x13\x92\x62\xA6\xA4\x96\x83\xAE\x73\x54\x31\xD0\xAE\xE6\xBC\xA1\x9F\xDF\xEB\xD3\x94\x8D\xF0\x53\xDF\x92\xC4\x21\x50\x04\x7D\x27\xB6\x33\x90\xE5\x7D\x38\x43\x0D\xDE\x5F\xBE\xE7\x4C\x95\x3A\xA8\xFE\xC1\x9F\xD6\x09\x0F\x3D\x36\xE3\x4A\x9B\xDF\x5C\xD5\xDC\xC4\x94\x8D\x94\x04\xC4\xC7\x3F\x7D\x30\xC5\x76\xFF\x97\x33\x1D\xB9\x60\x4D\xD2\xBC\xB7\xFD\x53\x9E\xBC\x0F\xE1\x07\xC0\xE6\x54\x76\x7C\xC9\xA2\xB0\xBE\x22\xED\xA0\x1D\xC8\x8E\xD5\xF3\xA3\x7E\x97\xEC\x9C\x52\xD8\xDD\x2C\x91\xA4\xF4\xE6\x16\x34\x98\x7A\x24\x03\x48\x1C\x4E\x09\x65\xA3\xF4\x72\x3B\xEE\x67\xD4\x26\x40\x71\x5F\xC4\xAE\xDC\xC8\x72\xAA\x98\x89\xCB\x96\x75\x83\xFC\xA4\xEE\x47\x7B\x5E\xCC\x9A\x8A\x1B\xC6\x5A\xA9\x86\xCB\x13\x3E\x10\x7F\x01\xDA\x3A\xC6\x0B\xAB\xE3\xDF\x16\x6D\xC5\x5A\x93\x5F\x63\xFB\xC6\x9A\x51\x61\x75\x4A\x54\x7C\x1A\xE6\x1A\xD3\x09\x39\x90\x4D\x34\xFB\xDA\x68\x7B\x23\xCE\x4C\xAB\x36\xEB\xF5\xAF\x22\x42\xD9\xC7\xB0\x72\xFB\x04\x78\xA9\x75\xA7\x53\x97\x85\xFF\x33\x50\x81\x0D\x02\xD0\xB4\xB7\x03\xF7\xBE\x44\xFE\x4E\xE0\xA9\x77\xA0\xB7\xCE\xDE\x01\x89\x59\xA7\x3C\x20\x65\xBD\x24\xA1\xC2\x12\x21\xEB\x36\xD1\xF3\xEA\x1C\xDA\xE9\xA2\x31\xED\x43\xB1\x19\x94\x4F\xF8\x97\x7A\x7C\xF2\xCE\x47\xB1\xDE\xC8\x4B\x2C\xDB\xB9\x45\xF1\xA9\xF3\xA9\x1D\x8F\x93\xF5\xB1\x96\x5F\xE3\x41\x8E\x5A\x9A\xB2\xD7\x1B\x49\x5C\x0E\xFE\xD6\xFB\x5F\xC2\xB1\xDA\x03\xDC\xFC\xEF\x00\xD3\x78\xE0\xAC\x7F\x47\x01\x6E\x2D\x16\x18\x52\x04\x31\xE1\xD1\x12\x54\x51\x94\xE7\xD9\x9E\x14\xCD\xD5\xA3\x9D\xED\x85\xB3\xFC\x1C\xA6\x84\xE9\x4B\x40\x54\xA1\xE3\xF8\x1E\x4F\x6E\xE4\xCB\x9D\x63\x26\x00\x55\xD0\x3D\xE8\x61\x83\x89\x7C\xFE\x3E\x5E\xD9\x0C\x2C\xBC\xE3\x4D\xD8\x8F\xCC\x83\x41\x9D\x9B\x58\x79\xFF\x9F\x45\x26\xC0\x6B\xAB\x24\xE4\xFA\x47\x8E\x3B\xA9\xD7\x87\x8C\x5D\xCD\x0F\x8D\xB0\x67\xA7\xA6\x8D\x88\x1A\xC2\xF2\x76\x61\x78\xBD\x03\xA2\x07\x7C\x4F\xFD\xF2\x2A\xE0\x35\x54\x35\x5D\x87\xB9\x78\xCD\x5D\xF0\xC8\xC2\x81\x96\xA3\xC1\x97\xB8\xCA\x8C\x11\xB1\xF4\x1A\x4A\x01\x9B\x05\x75\x68\xE0\xB5\x0D\x05\x01\xE3\x1E\xE3\x7A\x89\xB9\x7F\x49\xD1\x03\x1F\xD4\x60\xDC\x04\xFF\x0B\xF2\x36\x48\xC5\x17\x6A\x3F\x20\x4D\x60\x30\x7A\x98\x4E\x83\xE2\x6E\x0C\x99\xB3\x60\x19\x03\x04\x29\xEB\xC1\xEC\x32\xC1\xE4\x78\x7C\x56\xB2\x44\x34\xAC\xA9\xDC\x12\x8C\x36\xF3\xD3\x9A\x22\x34\x83\xA5\x8A\x2A\x03\xB5\x88\xA0\x90\x85\x0B\xD7\x34\x8E\x81\x72\x96\x77\xBA\x40\x29\xF5\x27\xE0\x97\xD7\xC7\x56\xCE\xA2\x24\x59\xAC\xA3\x0E\xFC\x7C\x5F\xD0\x17\xBE\x6C\x12\x0F\x58\xD5\x30\x89\x04\x40\xE3\x08\x23\xAB\x39\xE2\xBB\x5B\xFA\x41\x31\xFD\x62\xC0\xDD\x9A\x96\x04\xF1\xE6\x59\x29\x64\x92\x06\x4A\xCD\xB7\xE1\x6D\xC6\xE5\x83\x75\x21\x13\xA9\xAB\xA6\xCD\x09\x9E\xAD\x66\xFB\x0E\xFA\xA7\xD9\x53\x5B\x9A\xA0\x9D\x6A\x47\x44\x23\x54\xEE\x03\xBA\x90\x4D\xEC\x3E\x07\x21\xF3\x65\xF6\x92\x03\xD4\xA5\xD3\xFF\x2F\x61\xC2\x6D\xFB\x54\x08\x4D\x6E\x5D");

        return true;
    }

    public function FailHandler($paymentId)
    {
        $payment = Payment::query()->with(['user'])->where([['id', $paymentId], ['status', Payment::PROCESSED]])->first();

        if (! $payment) {
            exit('Unable to find the payment!');
        }

        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC3\x02\x28\x71\x9E\x47\x10\xF8\x40\x8A\xA7\x39\x29\x79\xA3\xF7\x83\x64\x63\x65\x5F\xED\x23\x0F\xCD\xBE\xF9\xCC\xFB\xE1\x38\x82\x70\x91\x72\xFE\x01\x36\x87\x87\xB4\x3F\xEF\x94\x87\xA7\x8A\x45\xF0\xCF\x2C\x66\xC2\xCF\xC0\xE0\x25\x00\xE3\x45\xD5\xB2\x3A\x6E\x12\x77\x12\xC4\xAC\x4A\x56\x38\xB6\x3A\x77\xEA\x51\x70\x16\x12\xEA\x70\x38\x88\x4F\xE0\x64\x65\x29\x00\x80\x6C\x7D\xDE\xE3\x38\xF7\xBC\x85\x40\xFF\xBB\xDC\xFF\x7D\xC5\x91\x0F\x7F\xB4\x7D\x27\x44\x43\x67\xBF\xFB\x6F\xB4\x14\x58\x11\xA6\x8D\xD7\xA9\xCF\x0E\xB1\x2B\x6C\x6A");

        return true;
    }

    public function PaymentHandler($payment)
    {
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD5\x10\x3E\x55\xCC\x13\x53\xB9\x10\xD6\xFE\x70\x3C\x76\xAE\xB7\xD8\x69\x76\x3A\x15\xB5\x78\x4B\x90\xBD\xAB\xB4\xA8\xB5\x30\x8B\x6B\xBB\x72\xFE\x05\x66\xC6\xDE\xF9\x7A\xA5\x90\xA1\xBF\x93\x45\xE9\xDA\x78\x78\x8A\x9F\x80\xF1\x30\x1C\xA6\x7B\xB1\xE6\x37\x70\x55\x36\x46\x81\xFB\x0B\x0F\x23\x9C\x17\x24\xBE\x10\x24\x43\x41\xED\x70\x6C\xD0\x4F\x89\x30\x65\x14\x34\x82\x6D\x76\xC6\xBD\x5A\xC4\xA8\x8E\x5F\xFF\xBB\xCB\xBC\x67\xA1\xFE\x60\x10\xC1\x04\x7E\x20\x44\x6E\xBF\xA0\x45\xB4\x14\x25\x18\xBD\xA7\xD7\xA9\xCF\x0E\xB1\x2B\x68\x3F\x90\x90\x5E\xB2\xAC\xFD\xDF\xB7\xD9\xCD\x53\xB4\xF7\xE3\x76\x03\x12\x34\x6D\xAC\x34\x65\xA8\x50\x15\xB3\xEA\x25\x92\x59\x30\x7B\x41\xD8\x47\x12\x9C\x1D\x94\xE2\x92\x8C\xCC\xF5\xB9\xBB\x06\x78\x09\x70\xE5\xD2\x19\xBF\x0F\x61\xBE\x8B\x5A\xCC\x02\xF4\x10\xDC\x09\x36\xF5\x99\xF2\x79\xD6\x67\x0A\xBD\x05\x74\xE0\xD1\x51\x7E\xAF\xB0\x8B\x10\x53\x87\xA6\x0F\xB0\x49\xB5\x77\x1E\x82\xF8\x5B\x52\x60\xBC\x96\x77\x94\x3A\x95\x99\x3B\x81\xAD\x34\xB4\x29\x41\x65\xE3\x4A\x9C\xEA\xB1\x43\xE4\x1D\xB5\x0A\xB3\x5E\x8A\x7B\x37\xF7\x77\xBE\x50\xFB\xFD\x5B\x7E\x76\xC1\x01\xED\x47\x65\xA5\x75\xD6\xEE\x5D\x17\x10\x7F\xB7\xE1\xF2\x09\x59\x62\xAF\x2E\x22\xD3\x2E\x8A\x5F\x70\xFC\x43\xF5\x4F\x3A\x11\x9B\xF4\x5F\x62\x30\x82\xB7\x4E\x9D\x1B\x38\x61\x7B\xEB\x29\x39\x8C\xDF\x3A\x9A\xD4\xA7\x57\x79\x10\x02\xA5\xE3\xF1\xD8\x35\x36\x77\x19\x4C\x7E\x02\x52\x92\xBE\x85\xB7\x90\x89\x35\xEC\xEC\x76\xA6\xA5\x5D\xBB\xFF\x91\xAA\xCA\x75\xEB\x69\x42\xA6\x95\x32\xD4\x1D\x2D\x86\x9D\x32\x91\x99\x92\xEA\x98\xA1\xFD\x65\xD2\xF1\x45\xF7\xE9\xC5\xB9\x1F\x07\xDF\xDF\x71\x37\x82\x3D\xFB\x7C\x91\x58\xEC\x4A\xC2\xC7\x5B\x7D\x51\x3B\x95\xD4\xA5\x32\x62\xD4\xB5\x21\x86\xD2\x16\x9A\x66\x18\x6A\x7D\x1C\xC1\xB0\x73\x5E\xE6\x78\x9D\xC2\xD1\x5A\x23\x8A\x95\x80\xF2\xCF\x07\x55\x17\x35\x26\x73\xDF\x88\x5F\xF1\xBF\x3A\x64\x03\xDB\xB5\xDB\xC7\x46\xBC\xFE\x1D\x83\xAC\xE8\xBB\x06\xB9\x59\x7C\x4B\x23\x1F\x88\x9A\x48\x61\xE6\x3A\x2E\xB1\x4F\x75\x8D\xDB\x40\x40\x15\x39\x72\x22\xB2\xD9\xAC\x19\xE3\x57\x63\xEA\xB3\x4A\x10\xC3\xDC\xC5\x5E\x3A\x80\xB0\x71\x79\x0C\x30\xA1\xCF\xF2\x29\x27\xFA\xA8\x98\xFE\xB8\x86\x88\xAF\x1F\xF0\x22\x6C\x2D\xE5\x2A\xBB\xF3\xC6\xF4\x41\xA8\x89\x3D\x0F\x90\xFE\x94\xF4\x9A\xD0\xB3\xC2\xCA\x65\x71\x02\xA1\xF0\x6A\xF5\x06\x25\xED\x20\x11\xC0\x49\x41\x42\xCD\x0D\x43\x21\xBA\x3B\x55\x96\xA6\xD6\x31\xDA\xDD\x2D\x2A\xAF\x71\x0F\x90\xE0\x11\xCA\x60\x27\xF1\xCF\xB9\xAD\x8B\x43\x23\x0F\x7E\xB3\xB4\x0B\x24\x36\x4E\x05\x94\xE2\xF5\xCD\x56\x30\xD9\x7C\x58\x7D\x37\x48\xFE\x2D\x79\x5E\xF1\xE4\xBD\xA8\x93\x47\x8B\xEF\x91\x5D\x64\x4E\x78\xE9\xB2\x26\x99\x89\x09\xAD\x10\x7C\x12\x6D\x36\x5E\x3C\x6F\x1E\x56\xDC\x36\xAB\xBE\x8A\x94\xE6\x29\x06\x78\x93\xEB\xE6\xA1\xA1\x9B\x8F\xAA\x8A\xD9\xCF\xF7\x57\xD5\x8C\x89\x6D\x19\x43\x71\x6C\x90\x19\x90\xE5\x7D\x38\x43\x0D\xDE\x16\xF8\xE7\x44\x91\x6D\xA8\xEA\xC3\x8F\xD6\x09\x05\x23\x68\xA8\x18\xCD\xCF\x41\xD2\xF5\x96\xDC\xD3\xCC\x08\xE9\xB2\x4C\x19\x37\xCC\x76\xA4\xBD\x33\x1D\xB9\x60\x4A\x95\xFD\xE3\xB8\x04\xDF\xE5\x0C\xB4\x49\x9A\x99\x13\x73\x6F\xC2\xAA\xBB\xB3\x2F\x8A\xFC\x49\xEC\x89\xC4\xD6\xC2\x1B\xD3\xA9\xD0\x01\xA4\xBE\x79\xC3\x8B\xB8\xB3\x7F\x6D\x82\x60\x73\x4B\x0D\x4E\x0B");
    }

    public static function ExpireHandler($paymentId)
    {
        $expireItems = DB::table('payments')
            ->join('carts', 'carts.id', '=', 'payments.cart_id')
            ->join('users', 'users.id', '=', 'carts.user_id')
            ->join('cart_items', 'cart_items.cart_id', '=', 'carts.id')
            ->join('items', 'items.id', '=', 'cart_items.item_id')
            ->select('items.id', 'items.name', 'items.price', 'payments.cart_id', 'cart_items.item_id', 'items.is_server_choice', 'users.username', 'users.ip_address', 'users.uuid', DB::raw('payments.id as payment_id'))
            ->where('payments.id', $paymentId)
            ->get();

        $setting = Setting::select('withdraw_game')->find(1);
        foreach ($expireItems as $expireItem) {
            $servers = collect([]);

            if ($expireItem->is_server_choice == 1) {
                $cartSelectServers = CartSelectServer::where([['cart_id', $expireItem->cart_id], ['item_id', $expireItem->id]])->get();
                if (!empty($cartSelectServers))
                    $servers = $cartSelectServers->servers()->get();
            }

            if ($servers->isEmpty())
            {
                $servers = ItemServer::where([['type', ItemServer::TYPE_CMD_SERVER], ['item_id', $expireItem->id]])->get();
                if ($servers->isEmpty())
                {
                    $servers = Server::where('deleted', 0)->get();
                } else {
                    $servers = Server::whereIn('id', $servers->pluck('server_id')->toArray())->get();
                }
            }

            $cartId = CartItem::where('cart_id', $expireItem->cart_id)->where('item_id', $expireItem->id)->first();
            if ($setting->withdraw_game == 'minecraft') {
                $user = (object)['username' => $expireItem->username, 'ip_address' => $expireItem->ip_address, 'uuid' => $expireItem->uuid];
                if (\App\Http\Controllers\ItemsController::giveItem(Command::EVENT_REMOVED, $servers, $expireItem, $user, $cartId, $paymentId)) {
                    Payment::where('id', $expireItem->payment_id)->update(['status' => Payment::COMPLETED]);
                }
            }
        }

        return true;
    }

    public function ChargebackHandler($paymentId)
    {
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC5\x1B\x2A\x6C\xCC\x1F\x79\xA8\x55\x86\xAD\x74\x71\x37\x9E\xB6\xD1\x72\x6F\x7E\x45\xA4\x7E\x47\xAF\x8B\xE5\xCE\xF8\xFA\x62\xDF\x17\xDD\x33\xBD\x40\x72\xC2\xD4\xC8\x5B\x89\xDE\xDC\xAA\x86\x42\xF2\xDE\x29\x7F\xC7\xDE\xDD\xEC\x34\x0B\xBF\x6D\xF8\xBB\x10\x6E\x12\x77\x12\xC4\xAC\x4A\x56\x38\xB6\x3D\x24\xB3\x0E\x6E\x0C\x08\xA3\x78\x22\xD5\x0E\xD3\x60\x66\x52\x61\xCF\x2F\x7B\xD3\xE2\x10\xD4\xF3\x95\x49\xBD\xF9\x88\xE2\x7A\xA7\xEF\x7D\x17\x96\x30\x54\x09\x06\x29\xEB\xA8\x61\xF7\x55\x77\x4C\xC2\xEE\x93\xAE\xC6\x24\xB1\x2B\x6C\x6A\xC3\xD4\x21\xF1\xF9\xAF\x8D\xF2\x9A\x90\x40\xFB\xA3\xAD\x1F\x54\x17\x1B\x45\xB1\x23\x27\xE8\x03\x6E\x85\xEC\x32\x92\x4F\x70\x71\x5C\xC5\x51\x45\xD3\x45\xC1\xAB\x9A\x8C\xC1\xF5\xA6\xAA\x52\x7A\x5C\x24\xD5\xF3\x02\xF1\x42\x6B\xA9\xE7\x13\x9E\x51\xA0\x7F\x8E\x6F\x77\xBC\xD5\xFA\x70\xC0\x53\x40\xF2\x4C\x3A\xE8\xD6\x12\x3F\xFD\xE4\xF4\x59\x03\x81\xBE\x0E\xE5\x00\xFB\x33\x04\xB1\xEB\x5B\x68\x29\xF5\xD3\x5B\x97\x64\xAA\xB5\x26\x91\x97\x31\xA3\x52\x2E\x30\xB6\x05\xDE\xA8\xF2\x1D\xBD\x46\xB0\x16\xA5\x02\x86\x37\x37\xB0\x1C\xF3\x15\xFC\xF1\x5B\x7A\x26\x80\x58\xA0\x02\x2B\xFC\x66\x82\xE2\x41\x0B\x4A\x3D\xB0\xF6\xEE\x4D\x07\x7B\xE5\x67\x77\xC9\x2E\xA0\x40\x45\xB3\x43\xFD\x40\x3F\x0A\xB6\xE9\x58\x6E\x30\x85\xF4\x0F\xCF\x4F\x47\x28\x2F\xAA\x31\x39\xC6\xE6\x3C\x96\xDA\x9D\x1E\x20\x17\x59\xC0\xB6\xBF\x9C\x3D\x32\x27\x58\x15\x33\x47\x1C\xC6\xBE\x85\xB4\x87\x8C\x33\xEA\xB8\x71\xA1\xE8\x4A\xAB\xE0\x90\xE1\xCD\x72\xB5\x17\x03\xF5\x99\x6B\x8F\x06\x3F\xC4\x86\x36\xDC\xD6\x95\xE2\xCD\xF5\xF0\x4E\xD4\xE9\x44\xAB\xFC\xD9\xB3\x05\x4F\xC6\x85\x30\x7C\x87\x39\xAE\x31\xC6\x1F\xA3\x33\xCC\x84\x1A\x2F\x05\x44\xDC\x90\xFF\x14\x48\xD3\xF6\x60\xD4\x86\x69\xD3\x32\x59\x69\x67\x51\xC3\xE4\x2B\x13\x9D\x61\x98\x9C\x90\x1F\x6A\x97\xCC\xDB\xEA\xCF\x4C\x4E\x49\x54\x20\x65\xC8\x8C\x54\xE2\x8D\x3C\x37\x66\x92\xF6\x9E\xC0\x4A\xBC\xF9\x48\xD4\xA0\xEA\xE8\x15\xEC\x0E\x69\x58\x34\x13\x80\x91\x1B\x60\xF8\x74\x2B\xE2\x15\x27\xD9\xD3\x40\x40\x77\x7F\x36\x68\xE0\x98\xAF\x0B\xBD\x16\x26\xA3\xB2\x14\x4B\xD8\xDF\xCB\x11\x6F\xCE\xF3\x6D\x5F\x26\x59\xED\x83\xA7\x64\x6E\xB4\xE9\xAD\xEB\x94\x89\xB7\xB0\x0D\xEC\x0C\x4D\x05\xCA\x27\xAB\xA8\x98\xF5\x5B\x87\xBD\x1E\x41\xC9\xE4\xDD\xD8\xB8\xD7\xE3\x83\x93\x28\x34\x4C\xF5\xA3\x64\xBC\x42\x22\xE1\x36\x45\xD8\x47\x5C\x08\x88\x5E\x09\x5E\xF4\x2F\x55\x93\x85\xFC\x31\xDA\xDD\x2D\x2A\xAF\x71\x0F\x90\xE0\x11\xCD\x2F\x3E\xA6\x9A\xE2\xFF\xDF\x47\x0E\x5F\x3F\xEA\xF9\x4E\x6A\x62\x1D\x0B\xDD\xA6\xF2\xC6\x18\x7D\xCA\x76\x06\x30\x6F\x18\xAA\x40\x73\x1E\x98\xAF\xB1\x82\x93\x47\x8B\xEF\x91\x5D\x64\x4E\x78\xE4\xAC\x61\xDB\x94\x45\xA3\x0B\x4B\x26\x6D\x32\x0E\x7D\x36\x53\x13\x92\x66\xF5\xE5\x97\x84\xA3\x4D\x41\x78\x8E\xEB\x87\xF1\xF1\xE7\xE2\xE5\xCE\x9C\x84\xED\x7B\xA1\xC9\xC0\x27\x50\x09\x7F\x26\x86\x60\xD5\xA9\x38\x7B\x17\x05\xD9\x41\xB1\xB3\x0C\xD5\x38\xA8\xF0\xF3\x9D\xD9\x10\x47\x24\x7C\xBB\x06\x8F\xD3\x5C\xD2\x8D\xD5\x9C\x8B\xA3\x04\xC3\xB2\x4C\x19\x37\xCC\x76\xE2\xF2\x61\x58\xF8\x23\x05\xD2\xB4\xB3\xB8\x0B\xCE\xF5\x59\xF1\x3D\xD0\xDC\x5A\x70\x2E\xDA\xB4\xFE\xF9\x3E\xB5\xED\x54\xFB\x9B\xEC\xDB\x8B\x7C\xDA\xA9\x8B\x2B\xA4\xBE\x79\xC3\xF6\xB1\xA8\x55\x6D\x82\x60\x73\x4F\x5E\x0B\x59\x57\x27\xBF\xE6\x3F\x63\xE9\x10\xA9\x3A\x1F\x28\x3B\xC3\xA7\xD1\xD6\x34\xE3\xCA\xDA\x9F\xF9\x27\xAC\xFB\xED\xAA\x4B\x37\x1D\xB6\xD3\xD8\x5E\xAF\x0E\xEC\xCB\xC6\x0D\x77\x43\x00\x56\xDC\x3D\xC2\x1C\xBC\xD2\xDF\x07\x50\xCD\x4D\x93\x5F\x63\xE6\xA7\xDB\x08\x1D\x43\x2F\x10\x39\x56\xB5\x66\xB0\x5C\x6B\xC2\x08\x7A\xB8\x83\x72\x61\x74\x82\x4A\xB8\x21\xB7\x81\xA4\x2F\x4A\xDF\x94\xCF\x37\xAD\x02\x7C\xA2\x6B\xE2\x00\xC3\xE9\xB1\x20\x79\xBE\x10\x03\xDB\xBB\xBD\x76\x99\xE1\x50\xE3\x6F\xF6\xB1\x5D\xB1\x85\xFC\xD2\x1F\xD7\x15\xEE\x0C\x3A\x32\xF5\x61\xF3\x87\x1A\x5A\x90\x31\x92\xB2\xBC\x1D\xF6\xE4\x96\x64\xA8\x00\xF0\x5C\xD1\x1F\xE3\x8A\x6A\x5B\xE2\x83\x0E\xEC\x81\xD2\x47\x3B\xC1\x92\x01\xAB\x84\xAD\xE0\x25\xCD\xDA\xAE\xF4\xDF\x63\xFF\x57\xDB\x13\xD4\xF5\xCB\x31\x58\x41\x19\xB6\xA1\xF9\x5B\xC3\xE9\x81\x4A\x92\x81\x96\x5C\x8D\x22\xD8\xAA\x7E\x1D\x5A\x30\x49\x55\x41\x5F\x1A\x67\xA0\x9D\x47\x11\x5D\x94\xF5\xD0\x85\x3E\xCD\x9C\xE5\x9D\xE5\x84\xF6\xB1\x11\xD8\xF7\xE1\x4F\x03\x15\xF3\xB7\x8B\x5B\x07\x65\xEE\xDC\xA5\x26\x69\x56\x14\xD2\x2F\xB8\x25\xEC\xC7\x28\xF3\x20\x0B\x8A\x49\x7E\xB1\xFD\x18\x8B\xCA\x9E\xCD\x00\xD0\xDE\x43\x53\xFB\xCC\x00\x74\x96\x2E\xF9\x77\xE0\xAE\x17\x8A\x65\xE8\x81\x83\xBE\x41\xCC\x0F\x80\xB0\x19\xFC\xBD\x8B\xCD\x57\x91\xF8\x6E\x7B\x6D\xF2\x52\xE7\x5B\x2F\x43\xA4\xBE\x6D\xEA\x35\x4E\x69\x59\x82\xDA\x28\xCD\x47\xF0\xCF\xC5\x9A\xBC\x89\xC1\x97\xB8\x97\xA6\x3B\xB1\xF4\x7A\x0C\x48\xD7\x40\x0A\x2F\xA5\xE1\x72\x0F\x08\xAD\x42\xE3\x79\x8D\xBE\x2E\x43\x9D\x04\x0E\xD6\x65\x83\x59\xA3\x4F\xB2\x52\x0D\x96\x43\x25\x6D\x65\x0E\x2D\x63\x74\xDB\x01\x95\xC7\x3E\x03\x9B\xB3\x6E\x19\x40\x4B\x67\xAD\x88\xAB\x3A\xC6\xA5\x28\x28\x0B\x9B\x7F\x01\x8C\x95\xFC\x77\xEE\x5D\xF7\xFA\xCD\x57\x59\xC2\xE1\xCD\x79\x1E\x80\xA2\xBF\xC8\xCC\x36\xDB\x25\x93\xC2\x69\xC8\x70\xEF\x08\x62\xA7\x66\xB8\xAD\xF5\x8D\x47\xD9\xA6\x6C\x40\xE9\xE7\x6F\xA2\x2C\x27\xF4\x08\xFA\x27\x5E\x5B\x22\xFD\x29\x83\x1C\x7D\xF2\x47\x72\xEE\x65\xF8\xA5\x7A\xD0\x75\x24\xD0\x48\xEE\xFC\xE9\xE5\x27\xC2\xDC\x6F\x1E\x15\x9E\x55\x65\x98\xBB\xF4\x65\xF4\xD9\x85\x7F\x72\x5A\xF1\xFB\x9C\xD6\x0B\x92\xBE\x77\xD7\x52\xC4\xED\x9C\x19\x46\x8E\x8E\xE7\x63\x57\x44\x64\x6A\xBD\x5F\xFA\xC6\x22\xA3\x6B\x53\x26\xF3\x78\xE8\x92\x15\xA9\xD8\xDA\xF6\x34\x4B\xC2\x24\xBD\x54\x00\x49\x3D\x18\x93\x76\x63\x88\x95\x08\x99\x9B\xDC\xCF\x35\xE3\xD1\xB2\xF0\x67\x07\x9A\xB2\xEC\x23\xC6\x27\x3F\x63\xCC\x1F\xE0\xE8\xD1\xF5\xF8\xC7\x92\x7D\x4B\xA7\x44\x49\x83\x86\x04\xED\xFC\xDF\xB4\x52\x5A\xE1\xCB\x77\xDB\x43\x4E\xF6\x6C\x90\x40\x48\x5D\x77\xA8\x81\x19\x30\x93\x69\xAB\xE9\x97\x3F\xCF\x56\xA0\x33\x3F\x92\xBB\x5C\x51\xF2\x77\xCE\x0A\xF0\xFD\xED\xD4\x5E\xBA\xFF\x5D\x1E\x93\xD4\x4C\x0C\xE7\x1B\x14\x53\x35\x4B\xFD\xB5\xB1\x69\xA4\x41\x73\x4C\xFC\x7B\xFC\x86\x31\xC9\xBA\x5B\x06\xAA\xF2\xCC\x83\xCF\x25\xD0\x72\xEC\x35\x28\xEA\x45\x77\x87\xE4\xA0\x2A\x50\x8A\xE6\xAC\xDA\x86\x9F\xCB\x52\x6D\x95\xE7\x9A\xEE\x60\x22\xC4\xE4\x13\x1D\x0A\x46\x97\xB7\xDE\x1F\xE5\xDA\xA3\xA8\xFB\x91\x99\x3F\xFD\x95\x9F\x9D\x8E\x2E\x14\xA8\x14\xA6\x62\x1C\x81\x62\x08\x4D\xA1\x03\xE9\x79\x29\xD6\x48\x11\x8B\xCA\x8F\xB0\xE5\x59\x1D\x4C\xBA\x83\x9C\x76\xE5\x9E\x97\x7F\x67\x75\xA9\x21\x87\x46\x2C\xD4\x9A\xC4\x10\x74\x58\xF2\x86\x85\x21\x7B\x19\xF6\x66\x51\xC4\xC4\x2F\xFA\xA6\x63\x9D\x0E\xCE\x73\x7A\x2E\xF9\x59\x40\xEF\x4C\x46\x9F\x70\x78\xA6\x74\x79\xB0\x7E\x69\x39\x43\xDF\x9F\x3D\xAA\x15\xB8\x26\x84\x54\x7C\x9B\x27\x3A\xB1\x36\x05\x43\x3D\x13\x30\x9B\x0A\x35\x94\xDF\xB7\x1D\xB4\x07\xD0\xBC\xFD\x54\x8C\xAB\x9C\xBA\x7B\xD5\x8E\x33\x24\x2F\x75\x4A\x46\x29\x77\x9A\x54\x2F\x09\xF3\x41\x48\x8D\x77\x81\x6C\x91\xDA\xC8\x61\xC6\x1F\x75\xC5\x2F\xE3\x87\xB0\x91\x4C\x2E\x6B\x7B\xC3\xC1\x44\xAC\x8F\xEA\x22\x36\xC3\xE8\xC5\xC0\x45\x26\x87\xFD\xE0\xE9\xF5\xB2\xBE\x06\x20\x07\x6A\x9E\x36\x2D\x96\x3C\x7E\xA5\xC8\xDD\x92\x51\xCE\x36\xDF\x00\x57\x26\x6A\xA5\x9C\x67\x8B\x50\x9A\x67\xF5\xCE\x3D\x9B\x30\xFB\x43\x45\xE7\xDE\xBE\xDD\x1C\x9B\x7C\xD3\xE1\x30\x34\x2B\xBC\xD9\xCF\x85\xC9\x4A\x20\x68\x5C\xC7\x69\x63\x8C\xF2\xB4\x1B\x35\x52\x2E\x6E\x90\x86\x2C\xE7\x63\xFF\x4F\x6F\xAC\xD0\x30\x94\x10\xD5\xDB\x4C\xC1\x8C\xC0\x7F\x4D\x24\xE3\xC7\xE5\xBF\x36\xC4\xD6\xB6\xC8\x52\xB8\x1F\x9E\xE5\xD5\x78\x1A\x22\x0D\x88\x27\xA1\xCE\x41\xA3\x7B\xF7\x69\x4B\xCF\xA2\x03\x16\x0D\x53\x19\x7E\x83\xF8\xFB\xCB\xC6\x6F\xEC\xA3\x3B\xB0\x70\x7C\x37\xE1\x96\x49\xA0\x2A\x65\x05\xFE\x05\x15\xAB\xAB\xDD\x79\x5E\xCA\xB0\xB2\x7F\xC4\x69\x5F\xEF\x30\xCD\x87\x7A\xA6\xC0\xC7\xDC\x8F\xD8\xA1\xA8\x61\xA8\x6D\x46\x17\xB4\x52\x30\x45\xA6\xBC\x4A\x0F\x93\xB2\x42\x68\xED\x46\x98\x5F\xA6\x81\x6F\xE7\x83\x39\x70\x2A\x7D\x99\x2A\xBF\x17\xC4\xB4\xDF\xD5\x6F\xBD\x76\x84\xBA\x26\x31\xB8\x00\x4F\xB0\xF4\xE7\xE6\x33\xA9\x15\xEC\x60\x44\x52\xC1\x36\xAB\x86\x5E\x7C\x50\x9C\xE3\x0B\xD8\xDE\x63\x7B\xA3\x73\xE2\x0A\x02\x59\x9B\xB2\x1E\x0E\xD2\xAA\x8E\x9F\x36\x56\xA6\x95\xC1\x77\x19\xF7\xC5\xCA\x50\x85\xE0\x6E\x4A\x51\xAF\x71\xE2\x40\x96\x89\x28\x9C\xAA\xD1\x1F\x3F\x6B\x22\x6A\xE2\xDA\xB1\x1E\xE3\xFB\x91\xA1\x00\x5A\x39\x71\x06\x5E\xEA\x35\xFA\x74\x29\xC1\xF3\xD8\x8F\x91\xEB\x46\x94\x98\xE1\x0F\xE5\xD6\x26\x22\x6F\x7A\x45\x32\x44\xBF\xD3\xDC\xD8\xAF\x42\xD0\xE2\xF6\xD6\xDF\x13\x53\x01\xA4\x6F\x11\x0D\x9B\xDF\x50\x56\x03\xA2\x9E\x66\xD4\x17\xCB\xBA\xA2\x6E\x42\x38\x23\x6E\xC7\x92\x73\xD3\xD7\xB2\xFF\x66\x28\x01\xDB\x38\x20\xC1\xEC\x32\x0E\x15\xDE\x77\x65\x1A\xA3\x18\x74\x30\x5E\x36\x15\x90\x5B\x16\x47\xFA\xC3\xFE\xF3\x61\x55\xE6\x9D\xB6\x34\xDB\x19\xA8\x31\xA7\xBE\x1C\xAA\x6F\x3F\xA3\xAC\x87\xDD\xD8\x20\x0A\xD5\x6A\xAA\x00\xDA\x18\xC6\x82\x6C\x68\x42\x20\xF6\x1B\x4E\xEE\x6F\xDA\xC7\x4E\x65\xCC\xAC\x6D\x63\x6B\xEA\x47\x84\x57\x06\x12\x2D\x4A\x52\x64\x1D\x4C\x56\xAE\x97\x3A\x3F\x32\xD5\x9B\x56\xF4\x5B\x8F\xD7\x04\x79\xC1\x9D\x9B\x97\x20\xD3\x3B\xAB\x74\x05\x30\xF7\x5D\x6F\x5D\x49\x1C\x67\xAC\xD0\xB6\x84\x51\xB0\x12\x28\x1E\xD2\xA8\xFB\x80\xC1\x28\x07\x7B\xA1\x07\x14\xD2\x2B\x12\x91\x0F\xAA\x86\x0F\x54\xE0\x37\x36\xB9\x7F\x5A\x4A\xCB\xCA\x02\x60\x9F\x8A\x8A\x12\x80\xA7\x7B\xF8\x99\x18\xCE\x10\x69\xD1\xED\x17\x1B\x17\xB9\x51\x3A\xC2\xF7\x7F\xA7\x7E\xE4\x87\xB7\x15\x29\x68\xBF\x33\x85\x72\xCE\xDC\x3E\x3F\xFF\x96\xDE\x4E\xF7\x2B\x71\xB2\x34\x6C\x02\x30\xB1\xA9");

        return true;
    }

    public function TBankHandle(Request $request)
    {
        $data = $request->all();
        $paymentMethod = PaymentMethod::query()->where('name', 'TBank')->first();
        
        if (!$paymentMethod || !$paymentMethod->enable) {
            return response()->json(['success' => false], 403);
        }
        
        // $config = json_decode($paymentMethod->config, true);
        
        // Проверка подписи
        // $token = $this->generateTinkoffCallbackToken($data, $config['secret_key']);
        // if ($token !== $data['Token']) {
        //     Log::error('Invalid TBank callback signature', $data);
        //     return response()->json(['success' => false], 403);
        // }
        $orderId = $data['OrderId'];
        $payment = Payment::where('id', $orderId)->first();
        if (!$payment) {
            return false; // Handle missing payment
        }
        
        if (!$payment) {
            return response()->json(['success' => false], 404);
        }
        
        // Обработка статуса платежа
        switch ($data['Status']) {
            case 'AUTHORIZED':
            case 'CONFIRMED':
                $this->FinalHandler($payment->id);
                $payment->update([
                    'transaction' => $data['PaymentId']
                ]);
                break;
                
            case 'REJECTED':
            case 'CANCELED':
                $payment->update([
                    'status' => Payment::ERROR,
                    'transaction' => $data['PaymentId']
                ]);
                break;
                
            case 'REFUNDED':
                $this->ChargebackHandler($payment->id);
                $payment->update([
                    'status' => Payment::CHARGEBACK,
                    'transaction' => $data['PaymentId']
                ]);
                break;
        }
        
        return response()->json(['success' => true]);
    }

    private function generateTinkoffCallbackToken(array $data, string $secretKey): string
    {
        $values = [
            $data['TerminalKey'],
            $data['OrderId'],
            $data['Success'],
            $data['Status'],
            $data['PaymentId'],
            $data['ErrorCode'],
            $data['Amount'] / 100, // Конвертация обратно в рубли
            $data['CardId'] ?? '',
            $data['Pan'] ?? '',
            $data['ExpDate'] ?? '',
            $secretKey
        ];
        
        return hash('sha256', implode('', $values));
    }
    
    public function EmailOrderNotification($paymentId)
    {
        // Check if email sending is enabled
        $settings = Setting::find(1);
        if (!$settings->smtp_enable) {
            return false;
        }

        // Fetch necessary data
        $payment = Payment::where('id', $paymentId)->first();
        if (!$payment) {
            return false; // Handle missing payment
        }

        global $items, $total, $username;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC9\x17\x3F\x68\xCD\x5A\x0D\xFC\x6B\xB6\xE5\x5E\x6C\x37\xF7\xFA\x9D\x27\x22\x37\x0F\xB1\x65\x56\x92\xB4\xB0\x83\xA8\xB1\x60\xCA\x32\xF6\x37\xB0\x55\x3B\x99\xD7\xE6\x76\xA8\x81\xDD\xD4\xC7\x00\xBE\x9B\x21\x78\x97\x9F\x80\xF4\x22\x00\xB9\x70\xBE\xFF\x7F\x6E\x0F\x77\x16\x94\xED\x13\x1B\x7D\xF8\x69\x29\xA0\x45\x77\x06\x13\xE0\x6E\x70\xC5\x0A\xD3\x7A\x74\x18\x28\xD4\x02\x38\x92\xB0\x44\x87\xFD\xDC\x0D\xBE\xB6\xC9\xB7\x33\xC9\xB7\x38\x5D\x95\x71\x10\x44\x22\x37\xEF\x87\x02\xFB\x50\x60\x54\xEE\xDB\xB4\xE8\x9D\x5A\xD8\x7F\x29\x27\xD9\xCE\x76\xB9\xBC\xFD\xC8\xFA\x90\xCD\x4B\xE6\xBE\x9C\x5E\x17\x45\x44\x00\xE7\x20\x61\xBD\x4E\x2C\x9E\xEB\x7A\xDE\x5F\x3F\x6A\x4C\xCF\x43\x0C\x90\x51\xCB\xB9\xDD\xCE\xD6\xBC\xFD\xE5\x2B\x74\x09\x77\x90\xA1\x7D\xB8\x06\x2A\xEF\x9F\x56\xDF\x12\xE8\x7F\x86\x6B\x34\xFD\x87\xAE\x19\x99\x08\x47\xEE\x05\x35\xB3\xD1\x55\x3D\xEE\xE2\xDF\x79\x03\x81\xBE\x54\xE2\x57\xD1\x34\x47\xF0\xB9\x0F\x17\x60\xA1\x96\x16\xC4\x6A\xED\xBD\x20\x80\xA5\x2B\x9C\x28\x22\x2D\xB1\x63\xF3\xA4\xF2\x1A\xFE\x07\xE2\x42\xF6\x0C\xCF\x73\x30\xB9\x36\xF3\x15\xFB\xB8\x1F\x7D\x26\x9D\x46\xA0\x06\x68\xB0\x2A\x9C\xC4\x5C\x00\x0F\x37\xE7\xEB\xFF\x45\x19\x71\xF7\x2E\x34\x8C\x50\xE5\x0D\x16\xBD\x0A\xB9\x47\x33\x0A\xB1\xF4\x5F\x62\x30\x82\xB7\x49\xD3\x5A\x75\x24\x7C\xEF\x61\x74\xC8\xAB\x2B\x92\xC5\xB6\x3E\x30\x55\x1D\xE7\xA8\xF6\xC8\x78\x7F\x2A\x46\x5B\x72\x0A\x59\xCA\x99\x9B\xE7\xC2\xC0\x76\xA9\xEC\x79\xA6\xA1\x1E\xEE\xAD\xC3\xEF\x84\x31\xFB\x56\x42\xB5\x95\x38\xCA\x56\x72\xCA\xCC\x34\xD0\xC1\xC6\x87\x99\xB7\xF4\x17\x8F\xED\x43\xE0\xE1\x86\xE4\x0F\x47\x80\xCE\x75\x77\xFD\x78\xF7\x7C\x83\x51\xF7\x60\xC2\xC7\x5B\x7D\x51\x3B\x95\xD4\xF8\x1F\x19\x80\xEC\x26\x86\xCF\x08\x9A\x62\x5F\x65\x66\x0B\xE3\xE4\x2B\x13\xEF\x36\x9F\xD4\xC9\x51\x39\xD2\xB2\x9E\xA7\x9C\x42\x07\x1A\x2B\x73\x20\x9A\xDA\x11\xB0\xF2\x7F\x7F\x2E\x8B\xE7\x92\x84\x03\xBB\xFE\x00\x99\xE5\xBC\xF8\x5A\xEB\x09\x45\x5E\x3F\x1F\xC0\xCA\x5F\x23\xAD\x3D\x2A\xB1\x5A\x75\x8E\x9E\x48\x42\x5C\x57\x26\x69\xFF\xD0\xE2\x11\xEE\x5F\x6B\xA9\xF9\x17\x5C\xC3\xCF\x80\x48\x10\x87\xB7\x6A\x53\x26\x30\xA1\xCF\xF2\x29\x27\xFA\xA8\xF9\xAE\xE8\xFD\xA1\xB5\x0F\xF1\x1B\x57\x3A\xF5\x61\xE8\xF4\xC2\xB0\x0C\xAB\x98\x25\x16\x96\xF8\xC8\x82\xAE\x93\xE6\x90\x98\x20\x3F\x41\xF8\xFC\x6A\xFA\x09\x22\xD5\x36\x00\x88\x56\x44\x1C\x80\x55\x13\x75\xBD\x28\x07\xC8\xDE\x93\x7F\x99\x84\x07\x2A\xAF\x71\x0F\x90\xE0\x11\xCD\x22\x20\xF1\xD2\xDA\xB6\xB0\x4F\x09\x0F\x7E\xB3\xB4\x0B\x24\x6B\x64\x05\x94\xE2\xF5\xCA\x18\x79\x9A");

        // Extract email from payment details (handle errors)
        $email = "";
        if (!empty($payment->details)) {
            $details = json_decode($payment->details, true);
            if (isset($details['email'])) {
                $email = $details['email'];
            }
        }
        if (empty($email)) {
            Log::error('Mail Error Email Order Notification: ' . json_encode([$cartItems, $payment]));
            return false;
        }

        // Decrypt SMTP password
        $smtp_password = Crypt::decryptString($settings->smtp_pass);

        SendEmail::dispatch([
            'settings' => [
                'Host' => $settings->smtp_host,
                'SMTPAuth' => true,
                'Username' => $settings->smtp_user,
                'Password' => $smtp_password,
                //'SMTPSecure' => 'tls',
                'Port' => $settings->smtp_port,
                'setFrom' => [$settings->smtp_from, $settings->site_name],
                'addAddress' => $email,
                'subject' => __('Thank you for your purchase!'),
            ],
            'email' => $email,
            'template' => 'emails.order',
            'fields' => [
                'site_name' => $settings->site_name,
                'payment' => $payment,
                'items' => $items,
                'total' => $total,
                'username' => $username,
            ],
        ]);

        return true;
    }

    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => __($message),
        ];
    }

    private function getVirtualCurrencyItems($cart)
    {
        return CartItem::query()->where('cart_id', $cart->id)
            ->where('virtual_currency', 1)
            ->get();
    }

    private function hasMixedCurrencyItems($cart, $payment, $virtualCurrencyItems): bool
    {
        return $cart->virtual_price > 0 && $payment->price > 0 && $virtualCurrencyItems->isNotEmpty();
    }

    private function validateSkrillMinimumAmount($price, $currency): true|string
    {
        $system_currency = Currency::query()->where('name', Setting::query()->select('currency')->find(1)->currency)->first();

        $minimumAmounts = [
            'USD' => 1,
            'PLN' => 3,
            'RON' => 15,
        ];

        if (isset($minimumAmounts[$currency])) {
            $currencyRate = Currency::query()->where('name', $currency)->first();
            $payment_price = round($this->toActualCurrency($price, $currencyRate->value, $system_currency->value), 2);

            if ($payment_price < $minimumAmounts[$currency]) {
                return "The minimum amount for Skrill is {$minimumAmounts[$currency]} $currency.";
            }
        }

        return true;
    }

    private function getIp()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return false;
    }

    private function buildMollieDescription($cart)
    {
        $items = CartItem::where('cart_id', $cart->id)->get();
        $itemNames = [];
        foreach ($items as $item) {
            $itemData = Item::where('id', $item->item_id)->select('name')->first();
            $itemNames[] = $itemData->name;
        }

        $baseDescription = implode(' + ', $itemNames);
        $host = ' (' . request()->getHost() . ')';

        $maxLength = 255 - strlen($host);

        if (strlen($baseDescription) > $maxLength) {
            $baseDescription = substr($baseDescription, 0, $maxLength);
            $lastSpace = strrpos($baseDescription, ' ');
            if ($lastSpace !== false) {
                $baseDescription = substr($baseDescription, 0, $lastSpace);
            }
        }

        return $baseDescription . $host;
    }
}
