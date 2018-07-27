<?php

use Illuminate\Support\Facades\Route;
use Fisher\Amap\API\Controllers as API;
use Illuminate\Contracts\Routing\Registrar as RouteRegisterContract;

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

Route::group(['prefix' => 'api/amap'], function (RouteRegisterContract $api) {
    
    // 获取附近的店铺(暂时不需要)
    // @get /api/amap
    $api->get('/', API\HomeController::class.'@getArounds');

    // 地址换取经纬度
    // @get /api/amap/geo
    $api->get('/geo', API\HomeController::class.'@getgeo');

    // 授权员工更新地图信息
    // $api->group(['middleware' => 'auth:api'], function (RouteRegisterContract $api) {
        $api->post('/', API\HomeController::class.'@index');
        $api->patch('/', API\HomeController::class.'@update');
        $api->delete('/', API\HomeController::class.'@delete');
    // });
});
