<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Variable
 *
 * @property int $id
 * @property string $name
 * @property string $identifier
 * @property string $description
 * @property int $type
 * @property array $variables
 * @property int $deleted
 * @method static \Illuminate\Database\Eloquent\Builder|Variable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Variable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Variable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereVariables($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereType($value)
 * @mixin \Eloquent
 */
class Variable extends Model
{
    const DROPDOWN_TYPE = 0;
    const INPUT_TEXT_TYPE = 1;
    const INPUT_DIGIT_TYPE = 2;

    protected $table = 'vars';

    public $timestamps = true;

    protected $fillable = [
        'identifier', 'name', 'description', 'type', 'variables', 'deleted'
    ];

    public function setVariablesAttribute(array $lines)
    {
        $this->attributes['variables'] = json_encode($lines);
    }

    public function getVariablesAttribute(): array
    {
        return json_decode($this->attributes['variables'], true) ?? [];
    }

    protected $casts = [
        'variables' => 'array',
    ];
}
