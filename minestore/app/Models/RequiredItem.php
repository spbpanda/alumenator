<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RequiredItem
 *
 * @property int $item_id
 * @property int $required_item_id
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequiredItem whereRequiredItemId($value)
 * @mixin \Eloquent
 */
class RequiredItem extends Model
{
    protected $table = 'required_items';
    protected $primaryKey = ['item_id', 'required_item_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'item_id', 'required_item_id'
    ];
}
