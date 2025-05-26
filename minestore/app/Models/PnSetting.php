<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnSetting extends Model
{
    protected $table = 'pn_settings';
    public $timestamps = true;

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    const TAX_MODE_EXCLUSIVE = 0;
    const TAX_MODE_INCLUSIVE = 1;

    protected $fillable = [
        'enabled',
        'store_id',
        'api_key',
        'tax_mode',
        'variable_tag_id',
        'store_currency',
    ];
}
