<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Building extends BuildingBase
{
    public function toArray($request): array
    {
        $data = [
            'attributes' => [
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'relationships' => [
                'firms' => [
                    'data' => FirmBase::collection($this->firms)
                ]
            ]
        ];

        return parent::toArray($request) + $data;
    }
}
