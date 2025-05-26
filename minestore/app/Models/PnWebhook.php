<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnWebhook extends Model
{
    protected $table = 'pn_webhooks';

    protected $fillable = [
        'webhook_id',
        'url',
        'secret',
    ];
}
