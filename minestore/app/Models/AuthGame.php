<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AuthGame
 *
 * @property string $id
 * @property string $username
 * @property int $status
 * @property string $date
 * @method static \Illuminate\Database\Eloquent\Builder|AuthGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthGame whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthGame whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthGame whereUsername($value)
 * @mixin \Eloquent
 */
class AuthGame extends Model
{
    protected $table = 'auth_game';

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'username', 'status', 'date',
    ];
}
