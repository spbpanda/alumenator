<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CmdQueue
 *
 * @property int $id
 * @property int $server_id
 * @property int $commands_history_id
 * @property mixed $command
 * @property int $pending
 * @method static \Illuminate\Database\Eloquent\Builder|CmdQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CmdQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CmdQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder|CmdQueue whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CmdQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CmdQueue whereServerId($value)
 * @mixin \Eloquent
 */
class CmdQueue extends Model
{
    protected $table = 'cmd_queue';

    public $timestamps = false;

    protected $fillable = [
        'server_id', 'commands_history_id', 'command', 'pending',
    ];
}
