<?php

namespace App\Models;

use Doctrine\DBAL\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Cart
 *
 * @property int $id
 * @property int $user_id
 * @property int $items
 * @property float $price
 * @property float $clear_price
 * @property float $tax
 * @property float $virtual_price
 * @property int|null $coupon_id
 * @property int|null $gift_id
 * @property float $gift_sum
 * @property int $is_active
 * @property int $discord_sync
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cart_items
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read \App\Models\Gift|null $gift
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\CartFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereClearPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGiftSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereVirtualPrice($value)
 * @mixin \Eloquent
 */
class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'items',
        'price',
        'clear_price',
        'tax',
        'referral',
        'virtual_price',
        'is_active',
        'coupon_id',
        'gift_id',
        'gift_sum',
        'discord_sync'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function cart_items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class, 'gift_id', 'id');
    }
    public function user(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'user_id');
    }
}
