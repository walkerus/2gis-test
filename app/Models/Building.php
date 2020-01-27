<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Building
 * @package App\Models
 * @property HasMany $firms
 */
class Building extends Model
{
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function firms(): HasMany
    {
        return $this->hasMany(Firm::class);
    }
}
