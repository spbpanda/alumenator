<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnCouponReference extends Model
{
    protected $table = 'pn_coupons_references';

    protected $fillable = [
        'cart_id',
        'external_coupon_id',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
}
