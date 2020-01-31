<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class Rubric
 * @package App\Models
 * @property int[] $ancestors
 */
class Rubric extends Model
{
    public function getAncestorsAttribute($value): array
    {
        return json_decode(str_replace(['{', '}'], ['[', ']'], $value));
    }

    public function firms(): BelongsToMany
    {
        return $this->belongsToMany(Firm::class);
    }

    public function descendants(): Collection
    {
        return $this->query()
            ->whereRaw("$this->id = ANY(ancestors)")
            ->get();
    }
}
