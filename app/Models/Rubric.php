<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
