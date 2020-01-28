<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Building;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Building as BuildingResources;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class BuildingController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $buildings = Building::query()
            ->orderBy('id')
            ->with('firms')
            ->paginate(1000, ['*'], 'page', (int) ($request->page ?? 1));

        return response()->json(
            [
                'links' => [
                    'self' => $buildings->url($buildings->currentPage()),
                    'last' => $buildings->url($buildings->lastPage()),
                ],
                'data' => BuildingResources::collection($buildings->items())
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
