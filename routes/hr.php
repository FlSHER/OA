<?php

/* -- 人事系统 -- */
Route::group(['prefix' => 'hr', 'namespace' => 'HR', 'as' => 'hr'], function() {
    Route::group(['prefix' => 'staff', 'as' => '.staff'], function() { //员工管理
        Route::get('/', ['uses' => 'StaffController@showManagePage']);
        Route::post('/list', ['uses' => 'StaffController@getStaffList'])->name('.list'); //获取员工列表
        Route::post('/export', ['uses' => 'StaffController@exportStaff'])->name('.export'); //导出员工信息
        Route::post('/show_info', ['uses' => 'StaffController@showPersonalInfo'])->name('.personal'); //查看详细信息
        Route::post('/info', ['uses' => 'StaffController@getInfo'])->name('.info'); //获取员工信息
        Route::post('/submit', ['uses' => 'StaffController@addOrEditStaff'])->name('.submit'); //添加或编辑单个员工
        Route::post('/import', ['uses' => 'StaffController@importStaff'])->name('.import'); //批量导入员工
        Route::post('/delete', ['uses' => 'StaffController@deleteStaff'])->name('.delete'); //删除员工
        Route::group(['prefix' => 'leaving', 'as' => '.leaving'], function() {// 离职交接（未启用）
            Route::get('/', ['uses' => 'StaffController@showLeavingPage']);
            Route::post('/submit', ['uses' => 'StaffController@leaving'])->name('.submit'); //处理离职交接提交数据
        });
        /* 插件部分 start */
        Route::post('/search', ['uses' => 'StaffController@searchResult'])->name('.search'); //搜索员工
        Route::post('/multi_set_modal', ['uses' => 'StaffController@getMultiSetModal'])->name('.multi_set_modal'); //获取设置员工（中间表）的弹窗
        Route::post('/multi_set', ['uses' => 'StaffController@multiSetStaff'])->name('.multi_set'); //设置员工（中间表）
        /* 插件部分 end */
    });
    Route::group(['prefix' => 'department', 'as' => '.department'], function() { //部门管理
        Route::get('/', ['uses' => 'DepartmentController@showManagePage']);
        Route::post('/list', ['uses' => 'DepartmentController@getDepartmentList'])->name('.list'); //获取部门列表
        Route::post('/tree', ['uses' => 'DepartmentController@getTreeView'])->name('.tree'); //获取部门树形图
        Route::post('/info', ['uses' => 'DepartmentController@getInfo'])->name('.info'); //获取部门信息
        Route::post('/add', ['uses' => 'DepartmentController@addDepartmentByOne'])->name('.add'); //添加部门
        Route::post('/edit', ['uses' => 'DepartmentController@editDepartmentByOne'])->name('.edit'); //编辑部门
        Route::post('/delete', ['uses' => 'DepartmentController@deleteDepartment'])->name('.delete'); //删除部门
        Route::post('/order', ['uses' => 'DepartmentController@reOrder'])->name('.order'); //处理部门排序
        Route::post('/options', ['uses' => 'DepartmentController@getOptionsById'])->name('.options'); //获取部门选项
    });
    Route::group(['prefix' => 'position', 'as' => '.position'], function() { //职位管理
        Route::get('/', ['uses' => 'PositionController@showManagePage']);
        Route::post('/list', ['uses' => 'PositionController@getPositionList']); //获取职位列表
        Route::post('/add', ['uses' => 'PositionController@addPositionByOne'])->name('.add'); //添加职位
        Route::post('/edit', ['uses' => 'PositionController@editPositionByOne'])->name('.edit'); //编辑职位
        Route::post('/delete', ['uses' => 'PositionController@deletePosition'])->name('.delete'); //删除职位
        Route::post('/options', ['uses' => 'PositionController@getOptionsById'])->name('.options'); //获取职位选项
        Route::post('/department_tree', ['uses' => 'PositionController@getDepartmentTreeView'])->name('.department_tree'); //获取部门树形图
    });
    Route::group(['prefix' => 'shop', 'as' => '.shop'], function() { //店铺管理
        Route::get('/', ['uses' => 'ShopController@showManagePage']);
        Route::post('/list', ['uses' => 'ShopController@getList'])->name('.list'); //获取店铺列表
        Route::post('/info', ['uses' => 'ShopController@getInfo'])->name('.info'); //获取店铺信息
        Route::post('/submit', ['uses' => 'ShopController@addOrEdit'])->name('.submit'); //添加店铺
        Route::post('/delete', ['uses' => 'ShopController@deleteByOne'])->name('.delete'); //删除店铺
        Route::post('/search', ['uses' => 'ShopController@showSearchResult'])->name('.search'); //搜索店铺
        Route::post('/validate', ['uses' => 'ShopController@validateColumn'])->name('.validate'); //检查字段
    });
    Route::group(['prefix' => 'violation', 'as' => '.violation'], function() { //大爱管理
        Route::get('/', ['uses' => 'ViolationController@showManagePage']);
        Route::post('/list', ['uses' => 'ViolationController@getList'])->name('.list'); //获取大爱列表
        Route::post('/export', ['uses' => 'ViolationController@export'])->name('.export'); //导出大爱单
        Route::post('/info', ['uses' => 'ViolationController@getInfo'])->name('.info'); //获取大爱信息
        Route::post('/deliver', ['uses' => 'ViolationController@delivery'])->name('.deliver'); //提交已交钱的大爱单
        Route::group(['prefix' => 'enter', 'as' => '.enter'], function() { //录入大爱
            Route::get('/', ['uses' => 'ViolationController@showEnterPage']);
            Route::post('/list', ['uses' => 'ViolationController@getEnterList'])->name('.list'); //获取录入大爱列表
            Route::post('/import', ['uses' => 'ViolationController@import'])->name('.import'); //批量导入大爱
            Route::post('/add', ['uses' => 'ViolationController@addByOne'])->name('.add'); //添加大爱
            Route::post('/edit', ['uses' => 'ViolationController@editByOne'])->name('.edit'); //编辑大爱
            Route::post('/delete', ['uses' => 'ViolationController@deleteByOne'])->name('.delete'); //删除大爱
            Route::post('/submit', ['uses' => 'ViolationController@submit'])->name('.submit'); //提交大爱单
        });
        Route::post('/amend', ['uses' => 'ViolationController@amend'])->name('.amend'); //提交调整
        Route::group(['prefix' => 'category', 'as' => '.category'], function() { //大爱类型
            Route::get('/', ['uses' => 'ViolationController@showCategoryPage']);
            Route::post('/list', ['uses' => 'ViolationController@getCategoryList'])->name('.list'); //获取大爱类型列表
            Route::post('/info', ['uses' => 'ViolationController@getCategoryInfo'])->name('.info'); //获取大爱类型信息
            Route::post('/add', ['uses' => 'ViolationController@addCategoryByOne'])->name('.add'); //添加大爱类型
            Route::post('/edit', ['uses' => 'ViolationController@editCategoryByOne'])->name('.edit'); //编辑大爱类型
            Route::post('/delete', ['uses' => 'ViolationController@deleteCategoryByOne'])->name('.delete'); //删除大爱类型
        });
        Route::group(['prefix' => 'reason', 'as' => '.reason'], function() { //大爱原因
            Route::get('/', ['uses' => 'ViolationController@showReasonPage']);
            Route::post('/list', ['uses' => 'ViolationController@getReasonList'])->name('.list'); //获取大爱原因列表
            Route::post('/info', ['uses' => 'ViolationController@getReasonInfo'])->name('.info'); //获取大爱原因信息
            Route::post('/add', ['uses' => 'ViolationController@addReasonByOne'])->name('.add'); //添加大爱原因
            Route::post('/edit', ['uses' => 'ViolationController@editReasonByOne'])->name('.edit'); //编辑大爱原因
            Route::post('/delete', ['uses' => 'ViolationController@deleteReasonByOne'])->name('.delete'); //删除大爱原因
        });
    });
    Route::group(['prefix' => 'transfer', 'as' => '.transfer'], function() { //人事调动
        Route::get('/', ['uses' => 'TransferController@showManagePage']);
        Route::post('/list', ['uses' => 'TransferController@getList'])->name('.list');
        Route::post('/info', ['uses' => 'TransferController@getInfo'])->name('.info');
        Route::post('/import', ['uses' => 'TransferController@import'])->name('.import');
        Route::post('/export', ['uses' => 'TransferController@export'])->name('.export');
        Route::post('/submit', ['uses' => 'TransferController@addOrEdit'])->name('.submit');
    });
    Route::group(['prefix' => 'leave', 'as' => '.leave'], function() {//人事请假
        Route::get('/', ['uses' => 'LeaveController@showManagePage']);
        Route::post('/excelhandel', ['uses' => 'LeaveController@excelHandel']);
        Route::get('/excelhandel', ['uses' => 'LeaveController@excelHandel']);
    });
    Route::group(['prefix' => 'attendance', 'as' => '.attendance'], function() {//考勤
        Route::get('/', ['uses' => 'AttendanceController@showManagePage']);
        Route::post('/staffinfo', 'AttendanceController@showStaffInfo');
        Route::any('/export', 'AttendanceController@exportStaffData');
    });
});

