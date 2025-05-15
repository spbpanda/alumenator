<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Item
 *
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property float $price
 * @property float $discount
 * @property float|null $virtual_price
 * @property float $giftcard_price
 * @property string $description
 * @property int $expireAfter
 * @property int $expireUnit
 * @property string|null $publishAt
 * @property string|null $showUntil
 * @property int $category_id
 * @property int $sorting
 * @property int $type
 * @property int $req_type
 * @property array|null $required_items
 * @property int $featured
 * @property int $is_subs
 * @property int $chargePeriodValue
 * @property int $chargePeriodUnit
 * @property int $is_virtual_currency_only
 * @property int $is_any_price
 * @property int $is_server_choice
 * @property int $active
 * @property int $deleted
 * @property string|null $item_id
 * @property string|null $item_lore
 * @property int|null $quantityUserLimit
 * @property int $quantityUserPeriodValue
 * @property int $quantityUserPeriodUnit
 * @property int|null $quantityGlobalLimit
 * @property int $quantityGlobalPeriodUnit
 * @property int $quantityGlobalPeriodValue
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Command|null $commands
 * @property-read \App\Models\Category|null $parentCategory
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereChargePeriodUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereChargePeriodValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereExpireAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereExpireUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereGiftcardPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereIsAnyPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereIsOnce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereIsSubs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereIsVirtualCurrencyOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereItemLore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item wherePublishAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereQuantityGlobalLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereQuantityGlobalPeriodUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereQuantityGlobalPeriodValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereQuantityUserLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereQuantityUserPeriodUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereQuantityUserPeriodValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereReqType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereShowUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereSorting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereVirtualPrice($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
    //req_type
    const AND_REQ_TYPE = 0;
    const NO_REQ_TYPE = 1;
    const OR_REQ_TYPE = 2;

    //type
    const MINECRAFT_PACKAGE = 0;
    const GIFTCARD = 1;
    const MINECRAFT_AND_GIFTCARD = 2;

    protected $fillable = [
        'id',
        'name',
        'image',
        'price',
        'virtual_price',
        'discount',
        'giftcard_price',
        'expireAfter',
        'expireUnit',
        'publishAt',
        'showUntil',
        'description',
        'category_id',
        'sorting',
        'type',
        'req_type',
        'required_items',
        'featured',
        'is_subs',
        'is_virtual_currency_only',
        'is_any_price',
        'is_server_choice',
        'active',
        'deleted',
        'item_id',
        'item_lore',
        'quantityUserLimit',
        'quantityUserPeriodUnit',
        'quantityUserPeriodValue',
        'quantityGlobalLimit',
        'quantityGlobalPeriodUnit',
        'quantityGlobalPeriodValue',
        'chargePeriodUnit',
        'chargePeriodValue',
    ];

    public function commands(): BelongsTo
    {
        return $this->belongsTo(Command::class, 'id', 'item_id');
    }

    public function variables(): HasManyThrough
    {
        return $this->hasManyThrough(Variable::class, ItemVar::class, 'item_id', 'id', 'id', 'var_id');
    }

    public function requires(): hasManyThrough
    {
        return $this->hasManyThrough(Item::class, RequiredItem::class, 'item_id', 'id', 'id', 'required_item_id');
    }

    public function comparison(): hasManyThrough
    {
        return $this->hasManyThrough(Comparison::class, ItemComparison::class, 'item_id', 'id', 'id', 'comparison_id');
    }

    public function parentCategory(): HasOne
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function discordRole(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemRole::class, 'item_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
