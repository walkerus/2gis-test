<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Rubric
 * @package App\Models
 * @property string[] $phones
 * @property int[] $rubrics
 */
class Firm extends Model
{
    public function getRubricsAttribute($value): ?array
    {
        return json_decode(str_replace(['{', '}'], ['', ''], $value));
    }

    public function getPhonesAttribute($value): ?array
    {
        return explode(',', str_replace(['{', '}', '"'], '', $value));
    }

    public function rubricsModels(): Collection
    {
        $rubrics = $this->attributes['rubrics'];

        return Rubric::query()->whereRaw("id = ANY ('$rubrics')")->get();
    }
}
