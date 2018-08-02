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

Route::group(['namespace' => 'Api', 'middleware' => 'auth:api'], function () {
    Route::any('/get_current_user', ['uses' => 'HRMController@getCurrentUserInfo']); // 获取当前用户信息
    Route::any('/get_user', ['uses' => 'HRMController@getUserInfo']); // 获取用户信息
    Route::any('/get_department', ['uses' => 'HRMController@getDepartmentInfo']); // 获取部门信息
    Route::any('/get_shop', ['uses' => 'HRMController@getShopInfo']); // 获取店铺信息
    Route::post('set_shop', ['uses' => 'HRMController@setShopInfo']); // 配置店铺信息
    Route::any('/get_brand', ['uses' => 'HRMController@getBrandInfo']); // 获取品牌信息
    Route::any('/get_position', ['uses' => 'HRMController@getPositionInfo']); // 获取职位信息
    Route::any('/get_dingtalk_access_token', ['uses' => 'DingtalkController@getAccessToken']); //获取钉钉的accessToken
    Route::any('/get_dingtalk_js_api_ticket', ['uses' => 'DingtalkController@getJsApiTicket']); //获取钉钉的jsApiTicket
    Route::any('/get_dingtalk_js_config', ['uses' => 'DingtalkController@getDingtalkConfig']); //获取钉钉的jsConfig
    Route::group(['prefix' => 'dingtalk'], function () {
        Route::any('/start_approval', ['uses' => 'DingtalkController@startApproval'])->middleware('client'); //发起钉钉审批
    });
    Route::group(['prefix' => 'hr'], function () {
        Route::post('staff_update', ['uses' => 'HRMController@changeStaffInfo']); //修改员工信息
        Route::post('shop_update', ['uses' => 'HRMController@changeShopInfo']); //修改店铺信息
    });
    //评价路由
    Route::group(['prefix' => 'appraise'], function () {
        Route::post('selectedUserRemark', ['uses' => 'AppraiseController@selectedUserRemark']);//选中员工的所有评价数据
        Route::post('appraiseFromSubmit', ['uses' => 'AppraiseController@appraiseFromSubmit']); //评价表单提交处理
        Route::post('appraiseList', ['uses' => 'AppraiseController@appraiseList']); //当前员工的评价列表
        Route::post('delete', 'AppraiseController@delete');//删除
        Route::post('update', 'AppraiseController@update');//修改
    });
});
Route::get('/get_auth_code', ['uses' => 'Api\OAuthController@getAuthCode'])->middleware('auth');
Route::any('/get_token', ['uses' => 'Api\OAuthController@getAppToken']);
Route::any('/refresh_token', ['uses' => 'Api\OAuthController@refreshAppToken']);

Route::get('/dingtalk/register_approval_callback', ['uses' => 'Api\DingtalkController@registerApprovalCallback']); //注册钉钉审批回调
Route::any('/dingtalk/approval_callback', ['uses' => 'Api\DingtalkController@approvalCallback']); //钉钉审批回调

Route::group(['namespace' => 'Api', 'middleware' => 'auth:api'], function () {
    Route::namespace('Resources')->group(function () {
        Route::apiResource('staff', 'StaffController');
        Route::apiResource('departments', 'DepartmentController');
        Route::group(['prefix' => 'departments/{department}'], function () {
            Route::get('children-and-staff', 'DepartmentController@getChildrenAndStaff');
            Route::get('staff', 'DepartmentController@getStaff');
        });
        Route::apiResource('brands', 'BrandController');
        Route::apiResource('positions', 'PositionController');
        Route::apiResource('shops', 'ShopController');
        Route::apiResource('roles', 'RoleController');
        Route::get('educations', 'BasicInfoController@indexEducation');
    });
    Route::get('current-user', 'Resources\StaffController@getCurrentUser');
    Route::prefix('table')->group(function () {
        Route::post('staff', 'Resources\StaffController@index');
        Route::post('shop', 'TableController@getShop');
    });
});