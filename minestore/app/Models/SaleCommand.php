<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemServer;
use App\Models\Server;

class SaleCommand extends Model
{
    protected $table = 'sales_commands';
    public $timestamps = false;

    protected $fillable = [
        'sale_id', 'item_id', 'command',
    ];

    public function servers()
    {
        return $this->hasManyThrough(Server::class, ItemServer::class, 'item_id', 'id', 'id', 'server_id')->where('item_servers.type', '=', ItemServer::TYPE_SALE_COMMAND_SERVER);
    }
}
