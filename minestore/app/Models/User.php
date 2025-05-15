<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\GeoHelper;
use App\Models;
use GeoIp2\Record\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property string $avatar
 * @property string $system
 * @property string $identificator
 * @property string|null $uuid
 * @property string|null $country
 * @property string|null $country_code
 * @property string|null $ip_address
 * @property string|null $api_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Payment|null $payments
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIdentificator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUuid($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username', 'avatar', 'system', 'identificator', 'discord_id', 'uuid', 'country', 'country_code', 'ip_address', 'api_token',
    ];

    protected $hidden = [
        'api_token',
    ];

    protected static function booted()
    {
        parent::booted();

        // Set country by ip
        static::creating(function ($model) {
            $model->ip_address = request()->ip() ?? null;
            $model->country = GeoHelper::getCountryNameByIp(request()->ip()) ?? null;
            $model->country_code = GeoHelper::getCountryCodeByIp(request()->ip()) ?? null;
        });
    }

    public function payments(): BelongsTo
    {
        return $this->belongsTo(Models\Payment::class, 'id', 'user_id');
    }
}
