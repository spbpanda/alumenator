<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PromotedItem
 *
 * @property int $id
 * @property int $item_id
 * @property float $price
 * @property int $order
 * @property int $is_featured_offer
 * @property-read string $name
 * @property-read string $old_price
 * @property-read \App\Models\Item $item
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem whereIsFeaturedOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotedItem wherePrice($value)
 * @mixin \Eloquent
 */
class PromotedItem extends Model
{
    use HasFactory;

    protected $with = 'item';

    public $timestamps = false;

    protected $fillable = [
        'item_id', 'price', 'order', 'is_featured_offer'
    ];

    public function setIsFeaturedOfferAttribute(string $value)
    {
        $this->attributes['is_featured_offer'] = $value == 'on';
    }

    public function getNameAttribute(): string
    {
        return $this->item->name;
    }

    public function getOldPriceAttribute(): string
    {
        return $this->item->price;
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
