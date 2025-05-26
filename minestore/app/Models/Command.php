<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Command
 *
 * @property int $id
 * @property int $item_id
 * @property string $command
 * @property int $event
 * @property int $is_online_required
 * @property int $execute_once_on_any_server
 * @property int $delay_value
 * @property int $delay_unit
 * @property int $repeat_value
 * @property int $repeat_unit
 * @property int $repeat_cycles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Server> $servers
 * @property-read int|null $servers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Command newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Command newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Command query()
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereDelayUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereDelayValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereIsOnlineRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereRepeatCycles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereRepeatUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Command whereRepeatValue($value)
 * @mixin \Eloquent
 */
class Command extends Model
{
    const EVENT_PURCHASED = 0;
    const EVENT_CHARGEBACKED = 1;
    const EVENT_REMOVED = 2; // aka EXPIRED
    const EVENT_RENEWS = 3;

    const ITEM_COMMAND = 0;
    const REF_COMMAND = 1;

    protected $table = 'commands';

    public $timestamps = true;

    protected $fillable = [
        'item_type',
        'item_id',
        'command',
        'event',
        'is_online_required',
        'execute_once_on_any_server',
        'delay_value',
        'delay_unit',
        'repeat_unit',
        'repeat_value',
        'repeat_cycles'
    ];

    public function servers()
    {
        return $this->hasManyThrough(Server::class, ItemServer::class, 'item_id', 'id', 'id', 'server_id')->where('item_servers.type', '=', ItemServer::TYPE_CMD_SERVER);
    }

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Item::class, 'id', 'item_id');
    }
}
