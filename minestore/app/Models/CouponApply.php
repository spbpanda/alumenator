<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\CouponApply
 *
 * @property int $id
 * @property int $coupon_id
 * @property int $apply_id
 * @property-read \App\Models\Coupon|null $coupon
 * @method static \Illuminate\Database\Eloquent\Builder|CouponApply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponApply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponApply query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponApply whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponApply whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponApply whereId($value)
 * @mixin \Eloquent
 */
class CouponApply extends Model
{
    protected $table = 'coupon_apply';
    public $timestamps = false;

    const TYPE_WHOLE_STORE = 0;
    const TYPE_CATEGORIES = 1;
    const TYPE_PACKAGES = 2;

    protected $fillable = [
        'coupon_id', 'apply_id',
    ];

    public function coupon(): HasOne
    {
        return $this->hasOne(Models\Coupon::class, 'id', 'coupon_id');
    }
}
