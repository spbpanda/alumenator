<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\belongsTo;

/**
 * App\Models\CartItems
 *
 * @property int $id
 * @property int $cart_id
 * @property int $item_id
 * @property int $payment_type
 * @property float $price
 * @property float $initial_price
 * @property float $variable_price
 * @property float $initial_variable_price
 * @property float $virtual_currency
 * @property int $is_promoted
 * @property int $coupon_applied
 * @property int $count
 * property tinyInt $payment_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Item $item
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CartItem extends Model
{
    const REGULAR_PAYMENT = 0;
    const SUBSCRIPTION_PAYMENT = 1;

    protected $fillable = [
        'cart_id',
        'item_id',
        'payment_type',
        'is_promoted',
        'price',
        'initial_price',
        'variable_price',
        'virtual_currency',
        'initial_variable_price',
        'count',
        'coupon_applied'
    ];

    public function item(): belongsTo
    {
        return $this->belongsTo(Models\Item::class, 'item_id', 'id');
    }
}
