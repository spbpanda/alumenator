<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
/**
 * App\Models\ItemServer
 *
 * @property int $type 0
 * @property int $item_id
 * @property int $server_id
 * @method static \Illuminate\Database\Eloquent\Builder|ItemServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemServer query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemServer whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemServer whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemServer whereType($value)
 * @mixin \Eloquent
 */
class ItemServer extends Model
{
    const TYPE_CMD_SERVER = 0;
    const TYPE_ECONOMY_SERVER = 1;
    const TYPE_GLOBAL_COMMAND_SERVER = 2;
    const TYPE_REF_COMMAND_SERVER = 3;
    const TYPE_SALE_COMMAND_SERVER = 4;

    protected $table = 'item_servers';
    protected $primaryKey = ['type', 'item_id', 'server_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'type',
        'item_id',
        'cmd_id',
        'server_id'
    ];

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class, 'server_id', 'id');
    }
}
