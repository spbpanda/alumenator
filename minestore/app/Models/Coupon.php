<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property string $name
 * @property int $type
 * @property float $discount
 * @property int|null $uses
 * @property int|null $available
 * @property int|null $limit_per_user
 * @property float $min_basket
 * @property int $apply_type
 * @property string $note
 * @property int|null $user_id
 * @property int $deleted
 * @property string|null $start_at
 * @property string|null $expire_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponApply> $applies
 * @property-read int|null $applies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $applyCategories
 * @property-read int|null $apply_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $applyItems
 * @property-read int|null $apply_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereApplyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereLimitPerUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereMinBasket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUses($value)
 * @mixin \Eloquent
 */

class Coupon extends Model
{
    // Coupon Type
    const TYPE_PERCENT = 0;
    const TYPE_AMOUNT = 1;

    // Coupon Apply Type
    const WHOLE_WEBSTORE = 0;
    const CATEGORIES = 1;
    const PACKAGES = 2;

    protected $fillable = [
        'name',
        'type',
        'discount',
        'uses',
        'available',
        'limit_per_user',
        'min_basket',
        'apply_type',
        'note',
        'user_id',
        'deleted',
        'start_at',
        'expire_at',
    ];

    public function applies(): HasMany
    {
        return $this->hasMany(CouponApply::class, 'coupon_id', 'id');
    }

    public function applyItems(): HasManyThrough
    {
        return $this->hasManyThrough(Item::class, CouponApply::class, 'coupon_id', 'id', 'id', 'apply_id');
    }

    public function applyCategories(): HasManyThrough
    {
        return $this->hasManyThrough(Category::class, CouponApply::class, 'coupon_id', 'id', 'id', 'apply_id');
    }
}
