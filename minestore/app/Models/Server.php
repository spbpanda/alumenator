<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Server
 *
 * @property int $id
 * @property string $name
 * @property string $method
 * @property string $host
 * @property string $port
 * @property string $password
 * @property string $host_websocket
 * @property string $port_websocket
 * @property string $password_websocket
 * @property string $secret_key
 * @property int $deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereHostWebsocket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePasswordWebsocket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePortWebsocket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Server extends Model
{
    protected $table = 'servers';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'method',
        'host',
        'port',
        'password',
        'secret_key',
        'deleted'
    ];
}
