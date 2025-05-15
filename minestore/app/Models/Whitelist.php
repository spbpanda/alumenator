<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Whitelist
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $ip
 * @property string $date
 * @method static \Database\Factories\WhitelistFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Whitelist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Whitelist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Whitelist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Whitelist whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Whitelist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Whitelist whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Whitelist whereUsername($value)
 * @mixin \Eloquent
 */
class Whitelist extends Model
{
    use HasFactory;

    protected $table = 'whitelist';

    public $timestamps = false;

    protected $fillable = [
        'username', 'ip', 'date', 'reason',
    ];
}
