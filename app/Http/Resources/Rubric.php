<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Rubric extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => 'rubrics',
            'name' => $this->name,
            'ancestors' => $this->ancestors,
        ];
    }
}
