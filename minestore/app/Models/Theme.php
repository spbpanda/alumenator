<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Theme
 *
 * @property int $id
 * @property int $theme
 * @property string $name
 * @property string $description
 * @property string $img
 * @property string $url
 * @property string $author
 * @property int $is_custom
 * @property string $version
 * @method static \Illuminate\Database\Eloquent\Builder|Theme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme query()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereIsCustom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereVersion($value)
 * @mixin \Eloquent
 */
class Theme extends Model
{
    protected $table = 'themes';

    public $timestamps = false;

    protected $fillable = [
        'theme',
        'name',
        'description',
        'img',
        'url',
        'author',
        'is_custom',
        'version'
    ];
}
