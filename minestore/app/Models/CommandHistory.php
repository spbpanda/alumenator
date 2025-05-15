<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CommandHistory
 *
 * @property int $id
 * @property int|null $payment_id
 * @property int|null $item_id
 * @property string $cmd
 * @property string $username
 * @property int $server_id
 * @property int $status
 * @property int $is_online_required
 * @property int $execute_once_on_any_server
 * @property int $initiated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $executed_at
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereCmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereExecutedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereIsOnlineRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommandHistory whereUsername($value)
 * @mixin \Eloquent
 */
class CommandHistory extends Model
{
    const STATUS_EXECUTED = 0;
    const STATUS_QUEUE = 1; //DEFAULT
    const STATUS_PENDING = 2;
    const STATUS_FAILED = 3;
    const STATUS_DELETED = 4;

    const TYPE_ITEM = 0; //DEFAULT
    const TYPE_GLOBAL = 1;
    const TYPE_REF = 2;
    const TYPE_VIRTUAL_CURRENCY = 3;
    const TYPE_DONATION_GOAL = 4;
    const TYPE_SALE = 5;

    protected $table = 'commands_history';

    protected $fillable = [
        'payment_id',
        'item_id',
        'type',
        'cmd',
        'username',
        'server_id',
        'status',
        'is_online_required',
        'execute_once_on_any_server',
        'initiated',
        'created_at',
        'updated_at',
        'executed_at',
    ];

    public function quantity()
    {
        $payment = \App\Models\Payment::find($this->payment_id);
        if (!$payment) {
            return 0;
        }

        $cartItem = \App\Models\CartItem::select('count')
            ->where('cart_id', $payment->cart_id)
            ->where('item_id', $this->item_id)
            ->first();

        return $cartItem ? $cartItem->count : 0;
    }
}
