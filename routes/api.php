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
    // OA后台退出前 清空缓存
    Route::post('login/clear', function () { return (string) session()->invalidate(); });
    // 通用 api 资源路由
    Route::namespace('Resources')->group(function () {
        // 员工入职流程 /api/staff/entrant
        Route::post('staff/entrant', 'StaffController@entrant');

         // 员工转正流程 /api/staff/process
        Route::post('staff/process', 'StaffController@process');

        // 人事变动流程 /api/staff/transfer
        Route::post('staff/transfer', 'StaffController@transfer');

        // 离职流程 /api/staff/leave
        Route::post('staff/leave', 'StaffController@leave');

        //  再入职流程 /api/staff/again-entry
        Route::post('staff/again-entry', 'StaffController@againEntry');

        // 晋升流程  /api/staff/promotion
        Route::post('staff/promotion', 'StaffController@promotion');

        // 获取员工状态列表 /api/staff/status
        Route::get('staff/status', 'StaffRelationController@status');

        // 获取员工状态列表 /api/staff/property
        Route::get('staff/property', 'StaffRelationController@property');

        // 获取全部民族列表 /api/staff/national
        Route::get('staff/national', 'StaffRelationController@national');

        // 获取学历信息 /api/staff/education
        Route::get('staff/education', 'StaffRelationController@education');

        // 获取政治面貌信息 /api/staff/politics
        Route::get('staff/politics', 'StaffRelationController@politics');

        // 获取婚姻状态选项 /api/staff/marital
        Route::get('staff/marital', 'StaffRelationController@marital');

        // 关系类型选项 /api/staff/relative_type
        Route::get('staff/relative_type', 'StaffRelationController@relativeType');

        // 上传员工头像
        Route::post('staff/avatar', 'StaffAvatarController@update');

        // 获取全部部门 get /api/departments/tree
        Route::get('departments/tree', 'DepartmentController@tree');

        // 部门排序 get /api/departments/sort
        Route::patch('departments/sort', 'DepartmentController@sortBy');

        // get /api/departments/get_tree/:department
        Route::get('departments/get_tree/{department}', 'DepartmentController@getTreeById');

        // 获取店铺状态列表 /api/shops/status
        Route::get('shops/status', 'ShopController@status');

        // 工作流创建店铺档案. /api/shops/create
        Route::post('shops/create', 'ShopController@storeProcess');

        // 修改店铺状态 /api/shop/:shops/state
        Route::post('shops/{shop}/state', 'ShopController@changeState');

        Route::apiResource('shops', 'ShopController');// 店铺 api 资源路由
        Route::apiResource('roles', 'RoleController');// 角色 api 资源路由
        Route::apiResource('staff', 'StaffController');// 员工 api 资源路由
        Route::apiResource('brands', 'BrandController');// 品牌 api 资源路由
        Route::apiResource('authorities', 'RbacController');// 权限 api 资源路由
        Route::apiResource('positions', 'PositionController');// 职位 api 资源路由
        Route::apiResource('cost_brands', 'CostBrandController');// 费用品牌 api 资源路由
        Route::apiResource('departments', 'DepartmentController');// 部门 api 资源路由
        Route::get('current-user', 'StaffController@getCurrentUser');// 当前登录员工
        Route::group(['prefix' => 'departments/{department}'], function () {
            Route::get('children-and-staff', 'DepartmentController@getChildrenAndStaff');
            Route::get('staff', 'DepartmentController@getStaff');
        });
        Route::get('educations', 'BasicInfoController@indexEducation');
    });
    // 后台 api 资源路由
    Route::namespace('HR')->group(function () {
        // 重置密码 /api/hr/staff/:staff/reset
        Route::post('hr/staff/{staff}/reset', 'StaffController@resetPass');

        // 激活员工 /api/hr/staff/:staff/unlock
        Route::patch('hr/staff/{staff}/unlock', 'StaffController@unlock');

        // 锁定员工 /api/hr/staff/:staff/locked
        Route::patch('hr/staff/{staff}/locked', 'StaffController@locked');
        
        // 员工转正 /api/hr/staff/process
        Route::patch('hr/staff/process', 'StaffController@process');

        // 人事变动 /api/hr/staff/transfer
        Route::patch('hr/staff/transfer', 'StaffController@transfer');

        // 离职 /api/hr/staff/leave
        Route::patch('hr/staff/leave', 'StaffController@leave');

        // 离职交接 /api/hr/staff/leaving
        Route::patch('hr/staff/leaving', 'StaffController@leaving');

        //  再入职 /api/hr/staff/again-entry
        Route::patch('hr/staff/again-entry', 'StaffController@againEntry');

        // 员工批量导入 /api/hr/staff/import
        Route::post('hr/staff/import', 'ExcelStaffController@import');
        
        // 批量导出 /api/hr/staff/export
        Route::post('hr/staff/export', 'ExcelStaffController@export');

        // 变动记录 /api/hr/staff/:staff/logs
        Route::get('hr/staff/{staff}/logs', 'StaffController@logs');

        // 格式化变动记录 /api/hr/staff/:staff/format-logs
        Route::get('hr/staff/{staff}/format-logs', 'StaffController@formatLog');

        // 预约记录 /api/hr/staff/:staff/reserve
        Route::get('hr/staff/{staff}/reserve', 'StaffTmpController@index');

        // 撤销预约记录 /api/hr/staff/reserve/:tmp
        Route::delete('hr/staff/reserve/{tmp}', 'StaffTmpController@restore');

        // 获取全部部门 get /api/hr/departments/tree
        Route::get('hr/departments/tree', 'DepartmentController@tree');

        // 部门排序 get /api/hr/departments/sort
        Route::patch('hr/departments/sort', 'DepartmentController@sortBy');

        // 店铺定位 /api/hr/shops/position
        Route::post('hr/shops/position', 'ShopController@position');

        // 导出店铺资料 /api/hr/shops/export
        Route::post('hr/shops/export', 'ShopController@export');

        Route::apiResource('hr/tags', 'TagController');// 员工标签
        Route::apiResource('hr/shops', 'ShopController');// 店铺
        Route::apiResource('hr/roles', 'RoleController');// 角色
        Route::apiResource('hr/staff', 'StaffController');// 员工        
        Route::apiResource('hr/brands', 'BrandController');// 品牌
        Route::apiResource('hr/authorities', 'RbacController');// 权限
        Route::apiResource('hr/positions', 'PositionController');// 职位
        Route::apiResource('hr/cost_brands', 'CostBrandController');// 费用品牌
        Route::apiResource('hr/departments', 'DepartmentController');// 部门
        Route::apiResource('hr/tag/categories', 'TagCateController');// 标签分类
        Route::apiResource('hr/department/cates', 'DepartmentCategoryController');// 部门分类
        Route::apiResource('hr/hr_roles', 'HrRoleController');// hr员工品牌、部门角色
    });
    Route::prefix('table')->group(function () {
        Route::post('staff', 'Resources\StaffController@index');
        Route::post('shop', 'TableController@getShop');
    });
});