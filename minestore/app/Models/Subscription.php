<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;


/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $payment_id
 * @property string|null $sid
 * @property int $status
 * @property int $count
 * @property int $interval_days
 * @property string $renewal
 * @property string $creation_date
 * @property-read \App\Models\Payment $payment
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereIntervalDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereRenewal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStatus($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    const ACTIVE = 0;

    const CANCELLED = 1;

    const PENDING = 2;

    protected $table = 'subscriptions';

    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'sid',
        'payment_method',
        'customer_id',
        'status',
        'count',
        'interval_days',
        'renewal',
        'creation_date',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Models\Payment::class, 'payment_id', 'id');
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(Models\User::class, Models\Payment::class, 'id', 'id', 'payment_id', 'user_id');
    }
}
