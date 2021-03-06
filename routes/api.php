<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['web.auth.api'], 'namespace' => 'Api'], function () {
    Route::post('demo/vehicles/get', 'ApiController@getDemoVehicles');

    Route::post('vehicles/get', 'ApiController@getVehicles');

    Route::post('vehicles/check', 'ApiController@checkVehicles');

    Route::post('user/register', 'ApiController@userRegister');

    Route::post('user/info/get', 'ApiController@getUserInfo');

    Route::post('user/activity', 'ApiController@userActivity');

    Route::get('user/download/complete', 'ApiController@userDownloadComplete');

    Route::get('user/download/incomplete', 'ApiController@userDownloadIncomplete');
});
