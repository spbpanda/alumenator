<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SiteVisit
 *
 * @property int $id
 * @property int $count
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|SiteVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteVisit whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteVisit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteVisit whereId($value)
 * @mixin \Eloquent
 */
class SiteVisit extends Model
{
    protected $fillable = [
        'created_at',
        'count'
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function visit(){
        $this->count++;
        $this->save();
    }
}
