<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tax
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property float $percent
 * @property int $is_included
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereIsIncluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax wherePercent($value)
 * @mixin \Eloquent
 */
class Tax extends Model
{
    protected $table = 'taxes';

    public $timestamps = false;

    protected $fillable = [
        'name', 'country', 'percent', 'is_included',
    ];
}
