<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Gift
 *
 * @property int $id
 * @property string $name
 * @property float $start_balance
 * @property float $end_balance
 * @property string|null $expire_at
 * @property int $deleted
 * @property string $note
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Gift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gift query()
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereEndBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereStartBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gift whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Gift extends Model
{
    protected $table = 'gifts';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'start_balance',
        'end_balance',
        'expire_at',
        'deleted',
        'note',
        'user_id',
        'payment_id'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class, 'id', 'payment_id');
    }
}
