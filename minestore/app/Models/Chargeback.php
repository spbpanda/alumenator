<?php

namespace App\Models;

use App\Events\ChargebackCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * App\Models\Chargeback
 *
 * @property int $id
 * @property int $payment_id
 * @property string|null $sid
 * @property int $status
 * @property string $creation_date
 * @property mixed $details
 * @property-read \App\Models\Payment $payment
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback whereCreationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chargeback whereStatus($value)
 * @mixin \Eloquent
 */
class Chargeback extends Model
{
    protected $dispatchesEvents = [
        'saved' => ChargebackCreated::class
    ];

    protected $with = ['user'];

    const PENDING = 0;
    const COMPLETED = 1;
    const CHARGEBACK = 2;

    protected $table = 'chargebacks';
    public $timestamps = false;
    protected $fillable = [
        'payment_id',
        'sid',
        'status',
        'creation_date',
        'details'
    ];

    public function getDetails()
    {
        return json_decode($this->attributes['details']);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Payment::class, 'id', 'id', 'payment_id', 'user_id');
    }
}
