<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Sale
 *
 * @property int $id
 * @property string $name
 * @property float $discount
 * @property int $apply_type
 * @property array $promoted_items
 * @property float $min_basket
 * @property string $start_at
 * @property string $expire_at
 * @property int $is_enable
 * @property int $is_advert
 * @property int $processed
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SaleApply> $applies
 * @property-read int|null $applies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $applyCategories
 * @property-read int|null $apply_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $applyItems
 * @property-read int|null $apply_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereApplyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereIsEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereMinBasket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePromotedItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereStartAt($value)
 * @mixin \Eloquent
 */
class Sale extends Model
{
    protected $table = 'sales';

    protected $with = 'applies';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'discount',
        'min_basket',
        'apply_type',
        'is_enable',
        'processed',
        'packages_commands',
        'sales',
        'start_at',
        'expire_at',
        'is_advert',
        'advert_title',
        'advert_description',
        'button_name',
        'button_url',
    ];

    public function applies(): HasMany
    {
        return $this->hasMany(SaleApply::class);
    }

    public function applyItems(): HasManyThrough
    {
        return $this->hasManyThrough(Item::class, SaleApply::class, 'sale_id', 'id', 'id', 'apply_id');
    }

    public function applyCategories(): HasManyThrough
    {
        return $this->hasManyThrough(Category::class, SaleApply::class, 'sale_id', 'id', 'id', 'apply_id');
    }

    public function saleCommands(): HasMany
    {
        return $this->hasMany(SaleCommand::class);
    }
}
