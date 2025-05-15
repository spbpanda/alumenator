<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Advert
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $button_name
 * @property string $button_url
 * @property int $is_index
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Advert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Advert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Advert query()
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereButtonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereButtonUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereIsIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advert whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Advert extends Model
{
    protected $fillable = [
        'title', 'content', 'button_name', 'button_url', 'is_index',
    ];
}
