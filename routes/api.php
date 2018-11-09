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
        // api 其他应用使用接口
        Route::group(['prefix' => 'staff'], function () {
            // 获取员工列表 /api/staff
            Route::get('/', 'StaffController@index');

            // 获取单个员工 /api/staff/:staff
            Route::get('{staff}', 'StaffController@show')->where(['staff' => '[0-9]+']);

            // 员工入职 /api/staff/entrant
            Route::post('/entrant', 'StaffController@entrant');

             // 员工转正 /api/staff/process
            Route::post('/process', 'StaffController@process');

            // 人事变动 /api/staff/transfer
            Route::post('/transfer', 'StaffController@transfer');

            // 离职 /api/staff/leave
            Route::post('/leave', 'StaffController@leave');

            //  再入职 /api/staff/again-entry
            Route::post('/again-entry', 'StaffController@againEntry');

            // 获取员工状态列表 /api/staff/status
            Route::get('/status', 'StaffRelationController@status');

            // 获取员工状态列表 /api/staff/property
            Route::get('/property', 'StaffRelationController@property');

            // 获取全部民族列表 /api/staff/national
            Route::get('/national', 'StaffRelationController@national');

            // 获取学历信息 /api/staff/education
            Route::get('/education', 'StaffRelationController@education');

            // 获取政治面貌信息 /api/staff/politics
            Route::get('/politics', 'StaffRelationController@politics');

            // 获取婚姻状态选项 /api/staff/marital
            Route::get('/marital', 'StaffRelationController@marital');

            // 关系类型选项 /api/staff/relative_type
            Route::get('/relative_type', 'StaffRelationController@relativeType');

            // 上传员工头像
            Route::post('/avatar', 'StaffAvatarController@update');
        });

        // department router
        Route::group(['prefix' => 'department'], function () {

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
        Route::group(['prefix' => 'position'], function (){

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
        Route::group(['prefix' => 'brand'], function () {

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

        // cost brand router
        Route::group(['prefix' => 'cost_brand'], function () {

            // 获取品牌列表 /api/cost_brand
            Route::get('/', 'CostBrandController@index');

            // 添加品牌 /api/cost_brand
            Route::post('/', 'CostBrandController@store');

            // 编辑品牌 /api/cost_brand/:brand
            Route::patch('{brand}', 'CostBrandController@update')->where(['brand' => '[0-9]+']);

            //  删除品牌 /api/cost_brand/:brand
            Route::delete('{brand}', 'CostBrandController@destroy')->where(['brand' => '[0-9]+']);
        });

        // shop router
        Route::group(['prefix' => 'shop'], function () {

            // 获取店铺列表  /api/shop
            Route::get('/', 'ShopController@index');

            // 新增店铺 /api/shop
            Route::post('/', 'ShopController@store');

            // 店铺定位 /api/shop/position
            Route::post('position', 'ShopController@position');
            
           // 编辑店铺 /api/shop/:shop
           Route::patch('{shop}', 'ShopController@update')->where(['shop' => '[0-9]+']);

           // 删除店铺 /api/shop/:shop
           Route::delete('{shop}', 'ShopController@destroy')->where(['shop' => '[0-9]+']);

           // 获取店铺状态列表 /api/shop/status
           Route::get('/status', 'ShopController@status');

           // 工作流创建店铺档案. /api/shop/create
           Route::post('/create', 'ShopController@storeProcess');

           // 修改店铺状态 /api/shop/:shop/state
           Route::post('/{shop}/state', 'ShopController@changeState');
        });

        // rbac router
        Route::group(['prefix' => 'authorities'], function () {

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
        Route::group(['prefix' => 'roles'], function () {

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

    // hr 后台专用接口
    Route::group(['namespace' => 'HR', 'prefix' => 'hr'], function () {
        // staff router
        Route::group(['prefix' => 'staff'], function () {

            // 获取员工列表 /api/hr/staff
            Route::get('/', 'StaffController@index');

            // 添加单个员工 /api/hr/staff
            Route::post('/', 'StaffController@store');

            // 编辑单个员工 /api/hr/staff/:staff
            Route::patch('/{staff}', 'StaffController@update')->where(['staff' => '[0-9]+']);

            // 获取单个员工 /api/hr/staff/:staff
            Route::get('/{staff}', 'StaffController@show')->where(['staff' => '[0-9]+']);

            // 软删除单个员工. /api/hr/staff/:staff
            Route::delete('/{staff}', 'StaffController@destroy')->where(['staff' => '[0-9]+']);

            // 重置密码 /api/hr/staff/:staff/reset
            Route::post('/{staff}/reset', 'StaffController@resetPass')->where(['staff' => '[0-9]+']);

            // 激活员工 /api/hr/staff/:staff/unlock
            Route::patch('/{staff}/unlock', 'StaffController@unlock')->where(['staff' => '[0-9]+']);
            
            // 员工转正 /api/hr/staff/process
            Route::patch('/process', 'StaffController@process');

            // 人事变动 /api/hr/staff/transfer
            Route::patch('/transfer', 'StaffController@transfer');

            // 离职 /api/hr/staff/leave
            Route::patch('/leave', 'StaffController@leave');

            //  再入职 /api/hr/staff/again-entry
            Route::patch('/again-entry', 'StaffController@againEntry');

            // 员工批量导入 /api/hr/staff/import
            Route::post('/import', 'StaffController@import');
            
            // 批量导出 /api/hr/staff/export
            Route::post('/export', 'StaffController@export');

            // 变动记录 /api/hr/staff/:staff/logs
            Route::get('/{staff}/logs', 'StaffController@logs');

            // 预约记录 /api/hr/staff/:staff/reserver
            Route::get('/{staff}/reserver', 'StaffTmpController@index');

            // 撤销预约记录 /api/hr/staff/reserve/:tmp
            Route::delete('/reserve/{tmp}', 'StaffTmpController@restore');
        });

        // department router
        Route::group(['prefix' => 'department'], function () {

            // 获取部门列表 get /api/department
            Route::get('/', 'DepartmentController@index');

            // 获取全部部门 get /api/department/tree
            Route::get('tree', 'DepartmentController@tree');

            // 部门排序 get /api/department/sort
            Route::patch('sort', 'DepartmentController@sortBy');

            // 添加部门 post /api/department
            Route::post('/', 'DepartmentController@store');

            // 编辑部门 patch /api/department/:department
            Route::patch('{department}', 'DepartmentController@update')->where(['department' => '[0-9]+']);

            // 获取单个部门详情 get /api/department/:department
            Route::get('{department}', 'DepartmentController@show')->where(['department' => '[0-9]+']);

            // 删除部门 delete /api/department/:department
            Route::delete('{department}', 'DepartmentController@destroy');
        });

        // position router
        Route::group(['prefix' => 'position'], function (){

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
        Route::group(['prefix' => 'brand'], function () {

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

        // cost brand router
        Route::group(['prefix' => 'cost_brand'], function () {

            // 获取品牌列表 /api/cost_brand
            Route::get('/', 'CostBrandController@index');

            // 添加品牌 /api/cost_brand
            Route::post('/', 'CostBrandController@store');

            // 编辑品牌 /api/cost_brand/:brand
            Route::patch('{brand}', 'CostBrandController@update')->where(['brand' => '[0-9]+']);

            //  删除品牌 /api/cost_brand/:brand
            Route::delete('{brand}', 'CostBrandController@destroy')->where(['brand' => '[0-9]+']);
        });

        // shop router
        Route::group(['prefix' => 'shop'], function () {

            // 获取店铺列表  /api/shop
            Route::get('/', 'ShopController@index');

            // 新增店铺 /api/shop
            Route::post('/', 'ShopController@store');

            // 店铺定位 /api/shop/position
            Route::post('position', 'ShopController@position');
            
           // 编辑店铺 /api/shop/:shop
           Route::patch('{shop}', 'ShopController@update')->where(['shop' => '[0-9]+']);

           // 删除店铺 /api/shop/:shop
           Route::delete('{shop}', 'ShopController@destroy')->where(['shop' => '[0-9]+']);

           // 开闭店流程 /api/shop/:shop
           Route::patch('{shop}/process', 'ShopController@process')->where(['shop' => '[0-9]+']);
        });

        // rbac router
        Route::group(['prefix' => 'authorities'], function () {

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
        Route::group(['prefix' => 'roles'], function () {

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

        // tag router
        Route::group(['prefix' => 'tags'], function () {

            // 标签列表 /api/hr/tags
            Route::get('/', 'TagController@index');

            // 创建标签 /api/hr/tags
            Route::post('/', 'TagController@store');

            // 更新标签 /api/hr/tags/:tag
            Route::patch('{tag}', 'TagController@update');

            // 删除标签 /api/hr/tags/:tag
            Route::delete('{tag}', 'TagController@delete');

            // 标签分类列表 /api/hr/tags/categories
            Route::get('categories', 'TagController@categories');

            // 创建标签分类 /api/hr/tags/categories
            Route::post('categories', 'TagController@storeCate');

            // 更新标签分类 /api/hr/tags/categories/:cate
            Route::patch('categories/{cate}', 'TagController@updateCate');

            // 删除标签分类 /api/hr/tags/categories/:cate
            Route::delete('categories/{cate}', 'TagController@deleteCate');
        });
    });
});
