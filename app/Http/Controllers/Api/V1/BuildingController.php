<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Responses\StandardJsonResponseFactory;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Building as BuildingResources;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class BuildingController extends BaseController
{
    protected StandardJsonResponseFactory $standardJsonResponseFactory;

    public function __construct(StandardJsonResponseFactory $standardJsonResponseFactory)
    {
        $this->standardJsonResponseFactory = $standardJsonResponseFactory;
    }

    public function index(Request $request): JsonResponse
    {
        $buildings = Building::query()
            ->orderBy('id')
            ->with('firms')
            ->paginate(1000, ['*'], 'page', (int) ($request->page ?? 1));

        return $this->standardJsonResponseFactory->createJsonResponse(
            BuildingResources::collection($buildings->items()),
            [
                'self' => $buildings->url($buildings->currentPage()),
                'last' => $buildings->url($buildings->lastPage()),
            ],
        );
    }
}
