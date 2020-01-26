<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiErrorException;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Firm as FirmResources;
use Illuminate\Routing\Controller as BaseController;

class FirmController extends BaseController
{
    public function building(int $buildingId): JsonResponse
    {
        /** @var Building $building */
        $building = Building::find($buildingId);

        if ($building === null) {
            throw new ApiErrorException('building_not_found');
        }

        return response()->json(
            [
                'data' => FirmResources::collection($building->firms)
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
