<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscordRole extends Model
{
    protected $table = 'discord_roles';
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'name',
        'deleted'
    ];
}
