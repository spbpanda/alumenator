<?php

namespace App\Helpers;

use App\Models\Cart;
use App\Models\Payment;
use App\Models\Subscription;
use DB;
use Illuminate\Support\Facades\Log;

class SubscriptionHelper
{
    /**
     * Replicates a payment record and associates it with a subscription
     *
     * @param Payment $payment The payment to replicate
     * @param Subscription $subscription The subscription to associate with the new payment
     */
    public static function replicatePaymentRecord(Payment $payment, Subscription $subscription)
    {
        try {
            return DB::transaction(function () use ($payment, $subscription) {
                $cart = Cart::find($payment->cart_id);
                if ($cart) {
                    // Replicate Cart
                    $newCart = $cart->replicate();
                    $newCart->save();

                    // Replicate Cart Items
                    $cartItems = $cart->cart_items;
                    foreach ($cartItems as $cartItem) {
                        $newCartItem = $cartItem->replicate();
                        $newCartItem->cart_id = $newCart->id;
                        $newCartItem->save();
                    }
                }

                // Replicate Payment
                $newPayment = $payment->replicate();
                $newPayment->cart_id = $newCart->id ?? $payment->cart_id;
                $newPayment->internal_subscription_id = $subscription->id;
                $newPayment->save();
            });
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to replicate payment record', [
                'payment_id' => $payment->id,
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
