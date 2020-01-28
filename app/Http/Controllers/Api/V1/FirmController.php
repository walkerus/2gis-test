<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiErrorException;
use App\Http\Requests\GetAllFirmsInRadius;
use App\Models\Building;
use App\Models\Firm;
use App\Models\Rubric;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Firm as FirmResources;
use Illuminate\Routing\Controller as BaseController;

class FirmController extends BaseController
{
    public function getAllFirmsInBuilding(Building $building): JsonResponse
    {
        return response()->json(
            [
                'data' => FirmResources::collection($building->firms)
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function index(Firm $firm): JsonResponse
    {
        return response()->json(
            [
                'data' => FirmResources::make($firm)
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function getAllFirmsInCategory(Rubric $rubric): JsonResponse
    {
        $rubricsIds = $rubric->getChildrenIds();
        $rubricsIds[] = $rubric->id;
        $rubricsIds = '{' . implode(',', $rubricsIds) . '}';

        $firms = Firm::query()
            ->where('rubrics', '&&', $rubricsIds)
            ->paginate(1000, ['*'], 'page', (int) ($request->page ?? 1));;

        return response()->json(
            [
                'links' => [
                    'self' => $firms->url($firms->currentPage()),
                    'last' => $firms->url($firms->lastPage()),
                ],
                'data' => FirmResources::collection($firms->items())
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
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
            ->paginate(1000, ['*'], 'page', (int) ($request->page ?? 1));

        $params = $request->request->all();
        unset($params['page']);
        $params = http_build_query($request->request->all());

        return response()->json(
            [
                'links' => [
                    'self' => $firms->url($firms->currentPage()) . '&' . $params,
                    'last' => $firms->url($firms->lastPage()) . '&' . $params,
                ],
                'data' => FirmResources::collection($firms->items())
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
