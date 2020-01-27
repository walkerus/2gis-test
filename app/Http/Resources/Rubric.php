<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Rubric extends JsonResource
{
    public function toArray($request): array
    {
        $data = [
            'attributes' => [
                'name' => $this->name,
                'ancestors' => $this->ancestors,
            ]
        ];

        return parent::toArray($request) + $data;
    }
}
