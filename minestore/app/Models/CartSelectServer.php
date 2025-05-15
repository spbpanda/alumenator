<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\CartSelectServer
 *
 * @property int $cart_id
 * @property int $item_id
 * @property int $server_id
 * @method static \Illuminate\Database\Eloquent\Builder|CartSelectServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartSelectServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartSelectServer query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartSelectServer whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartSelectServer whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartSelectServer whereServerId($value)
 * @mixin \Eloquent
 */
class CartSelectServer extends Model
{
    protected $table = 'cart_select_servers';
    protected $primaryKey = ['cart_id', 'item_id', 'server_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'cart_id', 'item_id', 'server_id',
    ];

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'item_servers', 'cart_select_server_id', 'server_id')
            ->where('item_servers.type', '=', ItemServer::TYPE_CMD_SERVER);
    }
}
