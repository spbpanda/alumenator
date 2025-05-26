<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnVariableReference extends Model
{
    protected $table = 'pn_variable_references';
    public $timestamps = true;

    protected $fillable = [
        'variable_id',
        'value',
        'external_product_id',
        'external_product_price',
    ];

    public function variable()
    {
        return $this->belongsTo(Variable::class, 'variable_id');
    }
}
