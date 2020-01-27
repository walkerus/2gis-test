<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/firms')->group(function () {
    Route::get('building/{id}', 'Api\V1\FirmController@getAllFirmsInBuilding');
    Route::get('rubric/{id}', 'Api\V1\FirmController@getAllFirmsInCategory');
    Route::get('radius', 'Api\V1\FirmController@getAllFirmsInRadius');
    Route::get('{firm}', 'Api\V1\FirmController@index');
});

Route::prefix('v1/buildings')->group(function () {
    Route::get('', 'Api\V1\BuildingController@index');
});
