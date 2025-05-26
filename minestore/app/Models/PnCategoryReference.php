<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PnCategoryReference extends Model
{
    protected $table = 'pn_category_references';

    protected $fillable = [
        'internal_category_id',
        'external_category_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id', 'internal_category_id');
    }
}
