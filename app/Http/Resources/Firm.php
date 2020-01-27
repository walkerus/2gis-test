<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Firm extends FirmBase
{
    public function toArray($request): array
    {
        $data = [
            'attributes' => [
                'name' => $this->name,
                'phones' => $this->phones,
            ],
            'relationships' => [
                'rubrics' => [
                    'data' => RubricBase::collection($this->rubricsModels())
                ],
                'building' => [
                    'data' => BuildingBase::make($this->building)
                ]
            ]
        ];

        return parent::toArray($request) + $data;
    }
}
