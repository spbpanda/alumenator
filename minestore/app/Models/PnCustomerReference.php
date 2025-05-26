<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnCustomerReference extends Model
{
    protected $table = 'pn_customer_references';

    protected $fillable = [
        'internal_user_id',
        'external_user_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'internal_user_id');
    }
}
