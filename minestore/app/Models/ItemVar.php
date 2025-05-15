<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * ItemVar
 *
 * @property int $item_id
 * @property int $var_id
 * @method static \Illuminate\Database\Eloquent\Builder|ItemVar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemVar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemVar query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemVar whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemVar whereVarId($value)
 * @mixin Eloquent
 */
class ItemVar extends Model
{
    protected $table = 'item_vars';
    protected $primaryKey = ['item_id', 'var_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'item_id', 'var_id'
    ];
}
