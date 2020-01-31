<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Rubric
 * @package App\Models
 * @property string[] $phones
 * @property Rubric[] $rubrics
 * @property Building $building
 */
class Firm extends Model
{
    public function getPhonesAttribute($value): ?array
    {
        return explode(',', str_replace(['{', '}', '"'], '', $value));
    }

    public function rubrics(): BelongsToMany
    {
        return $this->belongsToMany(Rubric::class);
    }

    public function building(): HasOne
    {
        return $this->hasOne(Building::class, 'id', 'building_id');
    }
}
