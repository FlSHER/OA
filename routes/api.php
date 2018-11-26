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

// 获取头像
Route::get('/staff/{staff}/avatar', 'Api\Resources\StaffAvatarController@show');
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
    Route::group(['namespace' => 'Resources'], function () {

        // 员工 api 资源路由
        Route::apiResource('staff', 'StaffController');

        // 员工入职流程 /api/staff/entrant
        Route::post('/staff/entrant', 'StaffController@entrant');

         // 员工转正流程 /api/staff/process
        Route::post('/staff/process', 'StaffController@process');

        // 人事变动流程 /api/staff/transfer
        Route::post('/staff/transfer', 'StaffController@transfer');

        // 离职流程 /api/staff/leave
        Route::post('/staff/leave', 'StaffController@leave');

        //  再入职流程 /api/staff/again-entry
        Route::post('/staff/again-entry', 'StaffController@againEntry');

        // 晋升流程  /api/staff/promotion
        Route::post('/staff/promotion', 'StaffController@promotion');

        // 获取员工状态列表 /api/staff/status
        Route::get('/staff/status', 'StaffRelationController@status');

        // 获取员工状态列表 /api/staff/property
        Route::get('/staff/property', 'StaffRelationController@property');

        // 获取全部民族列表 /api/staff/national
        Route::get('/staff/national', 'StaffRelationController@national');

        // 获取学历信息 /api/staff/education
        Route::get('/staff/education', 'StaffRelationController@education');

        // 获取政治面貌信息 /api/staff/politics
        Route::get('/staff/politics', 'StaffRelationController@politics');

        // 获取婚姻状态选项 /api/staff/marital
        Route::get('/staff/marital', 'StaffRelationController@marital');

        // 关系类型选项 /api/staff/relative_type
        Route::get('/staff/relative_type', 'StaffRelationController@relativeType');

        // 上传员工头像
        Route::post('/staff/avatar', 'StaffAvatarController@update');

        
        // 部门 api 资源路由
        Route::apiResource('departments', 'DepartmentController');

        // 获取全部部门 get /api/departments/tree
        Route::get('departments/tree', 'DepartmentController@tree');

        // 部门排序 get /api/departments/sort
        Route::patch('departments/sort', 'DepartmentController@sortBy');

        // get /api/departments/get_tree/:department
        Route::get('departments/get_tree/{department}', 'DepartmentController@getTreeById');

        // 职位 api 资源路由
        Route::apiResource('positions', 'PositionController');

        // 品牌 api 资源路由
        Route::apiResource('brands', 'BrandController');

        // 费用品牌 api 资源路由
        Route::apiResource('cost_brands', 'CostBrandController');

        // 店铺 api 资源路由
        Route::apiResource('shops', 'ShopController');

        // 店铺定位 /api/shops/position
        Route::post('shops/position', 'ShopController@position');

        // 获取店铺状态列表 /api/shops/status
        Route::get('shops/status', 'ShopController@status');

        // 工作流创建店铺档案. /api/shops/create
        Route::post('shops/create', 'ShopController@storeProcess');

        // 修改店铺状态 /api/shop/:shops/state
        Route::post('shops/{shop}/state', 'ShopController@changeState');

        // 权限 api 资源路由
        Route::apiResource('authorities', 'RbacController');

        // 角色 api 资源路由
        Route::apiResource('roles', 'RoleController');
    });

    // hr 后台专用接口
    Route::group(['namespace' => 'HR', 'prefix' => 'hr'], function () {

        // 员工 api 资源路由
        Route::apiResource('staff', 'StaffController');

        // 重置密码 /api/hr/staff/:staff/reset
        Route::post('/staff/{staff}/reset', 'StaffController@resetPass');

        // 激活员工 /api/hr/staff/:staff/unlock
        Route::patch('/staff/{staff}/unlock', 'StaffController@unlock');

        // 锁定员工 /api/hr/staff/:staff/locked
        Route::patch('/staff/{staff}/locked', 'StaffController@locked');
        
        // 员工转正 /api/hr/staff/process
        Route::patch('/staff/process', 'StaffController@process');

        // 人事变动 /api/hr/staff/transfer
        Route::patch('/staff/transfer', 'StaffController@transfer');

        // 离职 /api/hr/staff/leave
        Route::patch('/staff/leave', 'StaffController@leave');

        // 离职交接 /api/hr/staff/leaving
        Route::patch('/staff/leaving', 'StaffController@leaving');

        //  再入职 /api/hr/staff/again-entry
        Route::patch('/staff/again-entry', 'StaffController@againEntry');

        // 员工批量导入 /api/hr/staff/import
        Route::post('/staff/import', 'ExcelStaffController@import');
        
        // 批量导出 /api/hr/staff/export
        Route::post('/staff/export', 'ExcelStaffController@export');

        // 变动记录 /api/hr/staff/:staff/logs
        Route::get('/staff/{staff}/logs', 'StaffController@logs');

        // 预约记录 /api/hr/staff/:staff/reserve
        Route::get('/staff/{staff}/reserve', 'StaffTmpController@index');

        // 撤销预约记录 /api/hr/staff/reserve/:tmp
        Route::delete('/staff/reserve/{tmp}', 'StaffTmpController@restore');


        // 部门 api 资源路由
        Route::apiResource('departments', 'DepartmentController');

        // 获取全部部门 get /api/hr/departments/tree
        Route::get('departments/tree', 'DepartmentController@tree');

        // 部门排序 get /api/hr/departments/sort
        Route::patch('departments/sort', 'DepartmentController@sortBy');

        // 职位 api 资源路由
        Route::apiResource('positions', 'PositionController');

        // 品牌 api 资源路由
        Route::apiResource('brands', 'BrandController');

        // 费用品牌 api 资源路由
        Route::apiResource('cost_brands', 'CostBrandController');

        // 店铺 api 资源路由
        Route::apiResource('shops', 'ShopController');

        // 店铺定位 /api/hr/shops/position
        Route::post('shops/position', 'ShopController@position');

        // 权限 api 资源路由
        Route::apiResource('authorities', 'RbacController');

        // 角色 api 资源路由
        Route::apiResource('roles', 'RoleController');

        // 标签 api 资源路由
        Route::apiResource('tags', 'TagController');

        // 标签分类 api 资源路由
        Route::apiResource('tag/categories', 'TagCateController');
    });
});
