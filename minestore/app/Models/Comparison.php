<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    protected $table = 'comparisons';
    public $timestamps = false;
    protected $fillable = [
        'category_id', 'type', 'name', 'description'
    ];
}
