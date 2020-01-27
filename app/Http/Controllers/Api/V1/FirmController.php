<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiErrorException;
use App\Models\Building;
use App\Models\Firm;
use App\Models\Rubric;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Firm as FirmResources;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FirmController extends BaseController
{
    public function getAllFirmsInBuilding(int $buildingId): JsonResponse
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

    public function getAllFirmsInCategory(int $rubricId): JsonResponse
    {
        $rubric = Rubric::find($rubricId);

        if ($rubric === null) {
            throw new ApiErrorException('rubric_not_found');
        }

        $limit = 1000;
        $page = (int) ($request->page ?? 1);
        $url = env('APP_URL') . "/api/v1/firms/rubric/$rubricId";

        $rubricsIds = Rubric::query()
            ->where('id', '=', $rubricId)
            ->orWhereRaw("$rubricId = ANY(ancestors)")
            ->pluck('id')
            ->toArray();
        $rubricsIds = '{' . implode(',', $rubricsIds) . '}';

        $count = Firm::query()->where('rubrics', '&&', $rubricsIds)->count();
        $lastPage = (int) ceil($count / $limit);

        $firms = Firm::query()
            ->where('rubrics', '&&', $rubricsIds)
            ->limit($limit)
            ->offset($limit * ($page - 1))
            ->get();

        return response()->json(
            [
                'links' => [
                    'self' => $url . "?page=$page",
                    'last' => $url . "?page=$lastPage",
                ],
                'data' => FirmResources::collection($firms)
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    public function getAllFirmsInRadius(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'radius' => 'required|integer',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);
        } catch (ValidationException $ex) {
            throw new ApiErrorException('bad_params');
        }

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
