<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscordRoleQueue extends Model
{
    protected $table = 'discord_role_queue';
    public $timestamps = true;

    const GIVE_ROLE = 0;
    const REMOVE_ROLE = 1;

    protected $fillable = [
        'discord_id',
        'action',
        'role_id',
        'user_id',
        'payment_id',
        'internal_role_id',
        'processed',
        'attempts',
        'error',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class, 'id', 'payment_id');
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DiscordRole::class, 'internal_role_id', 'id');
    }
}
