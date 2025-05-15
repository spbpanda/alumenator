<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\Gift;

class CleanupPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending_payments:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will cleanup all pending payments that are older than 3 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up pending payments...');

        $pendingPayments = Payment::where('status', Payment::PROCESSED)
            ->where('created_at', '<', now()->subHours(3))
            ->get();

        foreach ($pendingPayments as $payment) {
            $cart = Cart::find($payment->cart_id)->first();

            if ($cart->gift_id !== null & $cart->gift_sum > 0) {
                $giftcard = Gift::find($cart->gift_id)->where('deleted', 0)->first();
                $giftcard->update([
                    'end_balance' => $giftcard->end_balance + $cart->gift_sum
                ]);
            } elseif ($cart->coupon_id !== null) {
                $coupon = Coupon::find($cart->coupon_id)->where('deleted', 0)->first();

                if ($coupon->uses = 0 ) {
                    $coupon->update([
                        'uses' => 0
                    ]);
                }

                $coupon->update([
                    'uses' => $coupon->uses - 1,
                    'available' => $coupon->available + 1
                ]);
            }

            $cart->delete();
            $payment->delete();

            $this->info('Deleted pending payment & cart with ID: ' . $payment->id);
        }

        $this->info('Done!');
    }
}
