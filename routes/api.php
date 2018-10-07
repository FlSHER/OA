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
        Route::delete('staff_delete/{staff}', ['uses' => 'HRMController@deleteStaff']); // 删除员工信息
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
        // Route::apiResource('staff', 'StaffController');
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

Route::group(['namespace' => 'Api', 'middleware' => 'auth:api'], function () {
    Route::namespace('Resources')->group(function () {
        // staff router
        Route::group(['prefix' => 'staff', 'as' => '.staff'], function () {

            // 获取员工列表
            // /api/staff
            Route::get('/', 'StaffController@index');

            // 添加单个员工
            // /api/staff
            Route::post('/', 'StaffController@store');

            // 编辑单个员工
            // /api/staff/:staff
            Route::patch('{staff}', 'StaffController@update')->where(['staff' => '[0-9]+']);

            // 获取单个员工
            // /api/staff/:staff
            Route::get('{staff}', 'StaffController@show')->where(['staff' => '[0-9]+']);

            // 软删除单个员工.
            // /api/staff/:staff
            Route::delete('{staff}', 'StaffController@destroy')->where(['staff' => '[0-9]+']);

            // 员工批量导入
            // /api/staff/import
            Route::post('import', 'StaffController@import');
            
            // 批量导出
            // /api/staff/export
            Route::post('export', 'StaffController@export');

        });

        // department router
        Route::group(['prefix' => 'department', 'as' => '.department'], function () {

            // 获取部门列表
            // get /api/department
            Route::get('/', 'DepartmentController@index');

            // 获取全部部门
            // get /api/department/tree
            Route::get('tree', 'DepartmentController@tree');

            // 部门排序
            // get /api/department/sort
            Route::patch('sort', 'DepartmentController@sortBy');

            // 添加部门
            // post /api/department
            Route::post('/', 'DepartmentController@store');

            // 编辑部门
            // patch /api/department/:department
            Route::patch('{department}', 'DepartmentController@update')->where(['department' => '[0-9]+']);

            // 获取单个部门详情
            // get /api/department/:department
            Route::get('{department}', 'DepartmentController@show')->where(['department' => '[0-9]+']);

            // 删除部门
            // delete /api/department/:department
            Route::delete('{department}', 'DepartmentController@destroy');
        });

        // position router
        Route::group(['prefix' => 'position', 'as' => '.position'], function (){

            // 获取职位列表
            // /api/position
            Route::get('/', 'PositionController@index');

            // 新增职位
            // /api/position
            Route::post('/', 'PositionController@store');

            // 编辑职位
            // /api/position/:position
            Route::patch('{position}', 'PositionController@update')->where(['position' => '[0-9]+']);

            // 删除职位
            // /api/position/:position
            Route::delete('{position}', 'PositionController@destroy')->where(['position' => '[0-9]+']);
        });

        // brand router
        Route::group(['prefix' => 'brand', 'as' => '.brand'], function () {

            // 获取品牌列表
            // /api/brand
            Route::get('/', 'BrandController@index');

            // 添加品牌
            // /api/brand
            Route::post('/', 'BrandController@store');

            // 编辑品牌
            // /api/brand/:brand
            Route::patch('{brand}', 'BrandController@update')->where(['brand' => '[0-9]+']);

            //  删除品牌
            // /api/brand/:brand
            Route::delete('{brand}', 'BrandController@destroy')->where(['brand' => '[0-9]+']);
        });

        // shop router
        Route::group(['prefix' => 'shop', 'as' => '.shop'], function () {

            // 获取店铺列表
            // /api/shop
            Route::get('/', 'ShopController@index');

            // 新增店铺
            // /api/shop
            Route::post('/', 'ShopController@store');

            // 店铺定位
            // /api/shop/position
            Route::post('position', 'ShopController@position');
            
           // 编辑店铺
           // /api/shop/:shop
           Route::patch('{shop}', 'ShopController@update')->where(['shop' => '[0-9]+']);

           // 删除店铺
           // /api/shop/:shop
           Route::delete('{shop}', 'ShopController@destroy')->where(['shop' => '[0-9]+']);
        });

        // rbac router
        Route::group(['prefix' => 'authorities', 'as' => '.authorities'], function () {

            // 权限列表
            // /api/authorities
            Route::get('/', 'RbacController@index');

            // 添加权限
            // /api/authorities
            Route::post('/', 'RbacController@store');

            // 修改权限
            // /api/authorities/:authority
            Route::patch('{authority}', 'RbacController@update')->where(['authority' => '[0-9]+']);

            // 删除权限
            // /api/authorities/:authority
            Route::delete('{authority}', 'RbacController@destroy')->where(['authority' => '[0-9]+']);
        });

        //role router
        Route::group(['prefix' => 'roles', 'as' => '.roles'], function () {

            // 角色列表
            // /api/roles
            Route::get('/', 'RoleController@index');

            // 创建角色
            // /api/roles/:role
            Route::post('/', 'RoleController@store');

            // 编辑角色
            // /api/roles
            Route::patch('{role}', 'RoleController@update')->where(['role' => '[0-9]+']);

            // 删除角色
            // /api/roles/:role
            Route::delete('{role}', 'RoleController@destroy')->where(['role' => '[0-9]+']);
        });
    });
});
