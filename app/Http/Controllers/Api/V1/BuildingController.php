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
        $limit = 1000;
        $page = (int) ($request->page ?? 1);
        $count = Building::count();
        $lastPage = (int) ceil($count / $limit);
        $url = env('APP_URL') . '/api/v1/buildings';
        $buildings = Building::query()
            ->limit($limit)
            ->offset($limit * ($page - 1))
            ->orderBy('id')
            ->with('firms')
            ->get();

        return response()->json(
            [
                'links' => [
                    'self' => $url . "?page=$page",
                    'last' => $url . "?page=$lastPage",
                ],
                'data' => BuildingResources::collection($buildings)
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
