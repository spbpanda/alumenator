<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnAlert extends Model
{
    protected $table = 'pn_alerts';
    public $incrementing = false;

    protected $fillable = [
        'alert_id',
        'store_id',
        'entity_id',
        'status',
        'type',
        'custom_title',
        'custom_message',
        'action_required_at',
        'action_link',
        'store_visible',
        'admin_visible',
        'resolved_by',
        'created_at',
        'updated_at',
        'expires_at',
        'resolved_at',
    ];

    protected $casts = [
        'store_visible' => 'boolean',
        'admin_visible' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'action_required_at' => 'datetime',
        'expires_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected $primaryKey = 'alert_id';
    protected $keyType = 'string';
}
