<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\SaleApply
 *
 * @property int $id
 * @property string $apply_type
 * @property int $sale_id
 * @property int $apply_id
 * @property-read \App\Models\Sale|null $sale
 * @method static \Illuminate\Database\Eloquent\Builder|SaleApply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleApply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleApply query()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleApply whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleApply whereApplyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleApply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleApply whereSaleId($value)
 * @mixin \Eloquent
 */
class SaleApply extends Model
{
    protected $table = 'sale_apply';
    public $timestamps = false;

    const TYPE_WHOLE_STORE = 0;
    const TYPE_CATEGORIES = 1;
    const TYPE_PACKAGES = 2;

    protected $fillable = [
        'apply_type',
        'sale_id',
        'apply_id',
    ];

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class, 'id', 'sale_id');
    }
}
