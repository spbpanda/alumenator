<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Gift;
use Illuminate\Console\Command;

class DeactivateActiveCartsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minestore:deactivate-carts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate all active carts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carts = Cart::where('is_active', 1)
            ->where('updated_at', '<', now()->subHours(3))
            ->get();

        foreach ($carts as $cart) {
            $cart->is_active = 0;
            $cart->save();

            if ($cart->coupon_id) {
                $coupon = Coupon::find($cart->coupon_id);
                if ($coupon->uses == 0 && $coupon->available !== null) {
                    $coupon->update([
                        'available' => $coupon->available + 1,
                    ]);
                } elseif ($coupon->available !== null) {
                    $coupon->update([
                        'available' => $coupon->available + 1,
                        'uses' => $coupon->uses - 1,
                    ]);
                } else {
                    $coupon->update([
                        'uses' => $coupon->uses - 1
                    ]);
                }

                $cart->update([
                    'coupon_id' => null
                ]);

                $cart->save();
            }

            if ($cart->gift_id) {
                $gift = Gift::find($cart->gift_id);
                $gift->update([
                    'end_balance' => $gift->end_balance + $cart->gift_sum,
                ]);

                $cart->update([
                    'gift_id' => null,
                    'gift_sum' => 0,
                ]);
            }

            $this->info("Cart {$cart->id} deactivated");
        }

        $this->info('All active carts have been deactivated');
    }
}
