<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnSyncLog extends Model
{
    protected $table = 'pn_sync_logs';

    protected $fillable = [
        'component',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];
}
