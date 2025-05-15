<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string|null $img
 * @property string|null $url
 * @property string|null $description
 * @property int|null $sorting
 * @property int $is_enable
 * @property int $deleted
 * @property string|null $gui_item_id
 * @property int $is_cumulative
 * @property int $is_listing
 * @property int $is_comparison
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $childrenItems
 * @property-read int|null $children_items_count
 * @property-read Category|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereGuiItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsComparison($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsCumulative($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsListing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSorting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUrl($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'url',
        'description',
        'sorting',
        'gui_item_id',
        'is_cumulative',
        'is_comparison',
        'img',
        'is_listing',
        'is_enable',
        'deleted'
    ];

    public function parent(): HasOne
    {
        return $this->hasOne('App\Models\Category', 'id', 'parent_id')->orderBy('sorting');
    }

    public function children(): HasMany
    {
        return $this->hasMany('App\Models\Category', 'parent_id', 'id')->orderBy('sorting');
    }

    public function comparison(): HasMany
    {
        return $this->hasMany('App\Models\Comparison', 'category_id', 'id')->orderBy('sorting');
    }

    public function packages(): HasMany
    {
        return $this->hasMany('App\Models\Item', 'category_id', 'id')->orderBy('sorting');
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function tree()
    {
        return static::with(implode('.', array_fill(0, 100, 'children')))
            ->where('parent_id', '=', '0')
            ->orderBy('sorting')
            ->get();
    }

    public function childrenItems(): HasMany
    {
        return $this->hasMany('App\Models\Item', 'category_id', 'id')->orderBy('sorting');
    }
}
