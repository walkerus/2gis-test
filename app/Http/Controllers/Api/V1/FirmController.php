<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\GetAllFirmsInRadius;
use App\Http\Responses\StandardJsonResponseFactory;
use App\Models\Building;
use App\Models\Firm;
use App\Models\Rubric;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Firm as FirmResources;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class FirmController extends BaseController
{
    protected StandardJsonResponseFactory $standardJsonResponseFactory;

    public function __construct(StandardJsonResponseFactory $standardJsonResponseFactory)
    {
        $this->standardJsonResponseFactory = $standardJsonResponseFactory;
    }

    public function getAllFirmsInBuilding(Building $building, Request $request): JsonResponse
    {
        return $this->standardJsonResponseFactory->createJsonResponse(FirmResources::collection($building->firms));
    }

    public function index(Firm $firm): JsonResponse
    {
        return $this->standardJsonResponseFactory->createJsonResponse(FirmResources::make($firm));
    }

    public function getAllFirmsInCategory(Rubric $rubric, Request $request): JsonResponse
    {
        $rubricsIds = $rubric->descendants()->pluck('id');
        $rubricsIds[] = $rubric->id;

        $firms = Firm::query()
            ->join('firm_rubric', function(JoinClause $join) use ($rubricsIds) {
                $join->on('firm_rubric.firm_id', '=', 'firms.id')
                    ->whereIn('firm_rubric.rubric_id', $rubricsIds);
            })
            ->with('rubrics')
            ->paginate(1000, ['*'], 'page', (int) ($request->page ?? 1));

        return $this->standardJsonResponseFactory->createJsonResponse(
            FirmResources::collection($firms->items()),
            [
                'self' => $firms->url($firms->currentPage()),
                'last' => $firms->url($firms->lastPage()),
            ],
        );
    }

    public function getAllFirmsInRadius(GetAllFirmsInRadius $request): JsonResponse
    {
        $latitude = (float) $request->latitude;
        $longitude = (float) $request->longitude;
        $radius = ((int) $request->radius) / 1000;
        $minLatitude = $latitude - $radius / 111.0;
        $maxLatitude = $latitude + $radius / 111.0;
        $minLongitude = $longitude - ($radius / abs(cos(deg2rad($latitude)) * 111.0));
        $maxLongitude = $longitude + ($radius / abs(cos(deg2rad($latitude)) * 111.0));

        $firms = Firm::query()
            ->join('buildings', function (JoinClause $joinClause) use ($minLatitude, $maxLatitude, $minLongitude, $maxLongitude) {
                $joinClause->on('buildings.id', '=', 'firms.id')
                    ->whereBetween('latitude', [$minLatitude, $maxLatitude])
                    ->whereBetween('longitude', [$minLongitude, $maxLongitude]);
            })
            ->with('building')
            ->with('rubrics')
            ->paginate(1000, ['*'], 'page', (int) ($request->page ?? 1));

        $params = $request->request->all();
        unset($params['page']);
        $params = http_build_query($params);

        return $this->standardJsonResponseFactory->createJsonResponse(
            FirmResources::collection($firms->items()),
            [
                'self' => $firms->url($firms->currentPage()) . '&' . $params,
                'last' => $firms->url($firms->lastPage()) . '&' . $params,
            ]
        );
    }

    public function tree()
    {
        $firms = Rubric::all();
        $root = $firms->filter(fn (Rubric $firm) => empty($firm->ancestors));

        dd($root);
    }
}
