<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnVatRate extends Model
{
    protected $table = 'pn_vat_rates';
    public $timestamps = true;
    protected $primaryKey = 'country_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'country_code',
        'country_name',
        'vat_rate',
    ];

    protected $casts = [
        'vat_rate' => 'decimal:2',
    ];
}
