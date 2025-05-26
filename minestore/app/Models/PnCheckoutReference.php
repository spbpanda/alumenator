<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnCheckoutReference extends Model
{
    protected $table = 'pn_checkout_references';
    public $timestamps = true;

    protected $fillable = [
        'payment_id',
        'checkout_id',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
