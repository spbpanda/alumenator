<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\hasMany;
use \Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\RefCode
 *
 * @property int $id
 * @property string $referer
 * @property string $code
 * @property int $percent
 * @property int $cmd
 * @property int $deleted
 * @property string $commands
 * @property string|null $server_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cart> $carts
 * @property-read int|null $carts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode whereCmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode whereCommands($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RefCode whereServerId($value)
 * @mixin \Eloquent
 */
class RefCode extends Model
{
    protected $table = 'ref_codes';

    public $timestamps = false;

    protected $fillable = [
        'referer',
        'code',
        'percent',
        'cmd',
        'deleted',
        'commands'
    ];

    protected $casts = [
        'commands' => 'array'
    ];

    public function setCmdAttribute($cmd)
    {
        $this->attributes['cmd'] = $cmd == 'on';
    }

    public function setCommandsAttribute(array $commands)
    {
        $this->attributes['commands'] = json_encode($commands);
    }

    public function getCommandsAttribute(): string
    {
        return json_encode($this->attributes['commands']);
    }

    public function payments(): hasMany
    {
        return $this->hasMany(Payment::class, 'ref');
    }

    public function carts(): HasManyThrough
    {
        return $this->hasManyThrough(Cart::class, Payment::class, 'ref', 'id', 'id','cart_id')
            ->where('');
    }

    public function servers()
    {
        return $this->hasManyThrough(Server::class, ItemServer::class, 'item_id', 'id', 'id', 'server_id')->where('item_servers.type', '=', ItemServer::TYPE_REF_COMMAND_SERVER);
    }
}
