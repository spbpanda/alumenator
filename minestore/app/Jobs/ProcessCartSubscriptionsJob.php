<?php

namespace App\Jobs;

use App\Helpers\ChargeHelper;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CartItem;
use App\Models\Item;
use Carbon\Carbon;

class ProcessCartSubscriptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $cartId;

    public function __construct(int $cartId)
    {
        $this->cartId = $cartId;
    }

    public function handle()
    {
        $cartItems = CartItem::query()->where('cart_id', $this->cartId)->get();
        $payment = Payment::where('cart_id', $this->cartId)->first();

        if ($payment && $payment->status === Payment::PAID && $cartItems->count() > 0) {
            foreach ($cartItems as $cartItem) {
                $item = Item::query()->where('id', $cartItem->item_id)
                    ->where('deleted', 0)
                    ->first();

                if ($item && $item->is_subs && $cartItem->payment_type === CartItem::REGULAR_PAYMENT) {
                    // Detecting the subscription duration and scheduling the job to expire the subscription
                    $delayValue = ChargeHelper::GetChargeDays($item->chargePeriodUnit, $item->chargePeriodValue);
                    $updatedAt = $payment->updated_at ? Carbon::parse($payment->updated_at) : Carbon::now();
                    $delay = $updatedAt->addDays($delayValue);
                    ExpireRegularPaymentSubscriptionJob::dispatch($payment->id)->delay($delay);
                }
            }
        }
    }
}

