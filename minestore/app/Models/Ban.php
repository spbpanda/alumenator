<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ban
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $uuid
 * @property string|null $ip
 * @property string $date
 * @property string|null $reason
 * @method static \Database\Factories\BanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUuid($value)
 * @mixin \Eloquent
 */
class Ban extends Model
{
    use HasFactory;

    protected $table = 'bans';

    public $timestamps = false;

    protected $fillable = [
        'username', 'uuid', 'ip', 'date', 'reason',
    ];
}
