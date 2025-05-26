<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PnProductReference extends Model
{
    protected $table = 'pn_product_references';

    protected $fillable = [
        'internal_package_id',
        'external_package_id',
        'external_package_price',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'internal_package_id', 'id');
    }
}
