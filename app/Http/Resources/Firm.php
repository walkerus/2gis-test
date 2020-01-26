<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Firm extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => 'firms',
            'name' => $this->name,
            'phones' => $this->phones,
            'relationships' => Rubric::collection($this->rubricsModels()),
        ];
    }
}
