<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\belongsTo;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property string $internal_id
 * @property int $user_id
 * @property int $cart_id
 * @property float $price
 * @property int $status
 * @property string $currency
 * @property int|null $ref
 * @property array $details
 * @property string|null $ip
 * @property string $gateway
 * @property string $transaction
 * @property int|null $internal_subscription_id
 * @property string|null $note
 * @property string|null $discord_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Chargeback|null $chargeback
 * @property-read \App\Models\RefCode|null $ref_code
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTransaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    protected $with = ['user'];

    const PROCESSED = 0;
    const PAID = 1;
    const ERROR = 2;
    const COMPLETED = 3;
    const CHARGEBACK = 4;
    const REFUNDED = 5;

    protected $fillable = [
        'internal_id',
        'user_id',
        'cart_id',
        'price',
        'status',
        'currency',
        'ref',
        'details',
        'ip',
        'gateway',
        'transaction',
        'internal_subscription_id',
        'note',
        'discord_id',
        'tax_inclusive',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cart(): belongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }

    public function ref_code(): belongsTo
    {
        return $this->belongsTo(RefCode::class, 'ref', 'id');
    }

    public function сommand_history(): belongsTo
    {
        return $this->belongsTo(CommandHistory::class, 'id', 'payment_id');
    }

    public function chargeback(): hasOne
    {
        return $this->hasOne(Chargeback::class);
    }
}
