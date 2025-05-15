<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PlayerData
 *
 * @property int $id
 * @property string $username
 * @property string $uuid
 * @property string $prefix
 * @property string $suffix
 * @property float $balance
 * @property string $player_group
 * @property int $sorting
 * @method static \Database\Factories\PlayerDataFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData wherePlayerGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData whereSorting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerData whereUuid($value)
 * @mixin \Eloquent
 */
class PlayerData extends Model
{
    use HasFactory;

    protected $table = 'playerdata';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'uuid',
        'prefix',
        'suffix',
        'balance',
        'player_group'
    ];
}
