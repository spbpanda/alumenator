<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnServerReference extends Model
{
    protected $table = 'pn_server_references';

    protected $fillable = [
        'internal_server_id',
        'external_server_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'internal_server_id', 'id');
    }
}
