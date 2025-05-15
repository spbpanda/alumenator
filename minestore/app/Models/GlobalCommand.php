<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\GlobalCommand
 *
 * @property int $id
 * @property float $price
 * @property int $is_online
 * @property string $cmd
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Server> $servers
 * @property-read int|null $servers_count
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalCommand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalCommand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalCommand query()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalCommand whereCmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalCommand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalCommand whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalCommand wherePrice($value)
 * @mixin \Eloquent
 */
class GlobalCommand extends Model
{
    protected $table = 'global_cmds';

    public $timestamps = false;

    protected $fillable = [
        'price', 'is_online', 'cmd',
    ];

    public function servers()
    {
        return $this->hasManyThrough(Server::class, ItemServer::class, 'item_id', 'id', 'id', 'server_id')->where('item_servers.type', '=', ItemServer::TYPE_GLOBAL_COMMAND_SERVER);
    }
}
