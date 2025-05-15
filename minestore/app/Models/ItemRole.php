<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

/**
 * ItemVar
 *
 * @property int $item_id
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRole whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRole whereRoleId($value)
 * @mixin Eloquent
 */
class ItemRole extends Model
{
    protected $table = 'item_discord_roles';
    protected $primaryKey = ['item_id', 'role_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'role_id'
    ];
}
