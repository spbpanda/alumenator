<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CartItemVar
 *
 * @property int $cart_item_id
 * @property int $var_id
 * @property string $var_value
 * @method static \Illuminate\Database\Eloquent\Builder|CartItemVar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItemVar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItemVar query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItemVar whereCartItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItemVar whereVarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItemVar whereVarValue($value)
 * @mixin \Eloquent
 */
class CartItemVar extends Model
{
    protected $table = 'cart_item_vars';
    protected $primaryKey = ['item_id', 'var_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'cart_item_id', 'var_id', 'var_value'
    ];
}
