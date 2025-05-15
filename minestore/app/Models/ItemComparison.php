<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;

class ItemComparison extends Model
{
    protected $table = 'item_comparison';
    public $timestamps = false;
    protected $primaryKey = ['item_id', 'comparison_id'];
    public $incrementing = false;
    protected $fillable = [
        'item_id', 'comparison_id', 'value',
    ];
}
