<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateVirtualCurrencyValuesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $virtualCurrency;

    /**
     * Create a new job instance.
     */
    public function __construct($virtualCurrency)
    {
        $this->virtualCurrency = $virtualCurrency;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $carts = Cart::where('virtual_price', '>', 0)->get();
        foreach ($carts as $cart) {
            $payment = Payment::where('cart_id', $cart->id)->first();
            $payment?->update([
                'currency' => $this->virtualCurrency,
                'note' => $payment->note . '.' . ' Virtual currency updated to ' . $this->virtualCurrency . ' at ' . now()->toDateTimeString(),
            ]);
        }
    }
}
