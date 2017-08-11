<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | This file is where you may define all of the routes that are handled
  | by your application. Just tell Laravel the URIs it should respond
  | to using a Closure or controller method. Build something great!
  |
 */
Route::group(['middleware' => 'admin'], function() {
    Route::get('/', ['uses' => 'HomeController@showDashBoard'])->name('home'); //首页仪表盘
    Route::get('/entrance', ['uses' => 'HomeController@showAppEntrance'])->name('entrance'); //应用端入口
    Route::get('/reset_password', ['uses' => 'LoginController@showResetPage'])->name('reset'); //重置密码
    Route::post('/reset_password', ['uses' => 'LoginController@resetPassword']);
    /* -- 财务系统 -- */
    Route::group(['prefix' => 'finance', 'namespace' => 'Finance', 'as' => 'finance'], function() {
        Route::group(['prefix' => 'reimburse', 'as' => '.reimburse'], function() {//报销系统
            Route::get('/', ['uses' => 'ReimburseController@showReimbursePage']);
            Route::post('/list', ['uses' => 'ReimburseController@getHandleList'])->name('.list'); //ajax获取待审核报销单
            Route::post('/expenses', ['uses' => 'ReimburseController@getExpensesByReimId'])->name('.expense'); //ajax获取消费明细
            Route::post('/agree', ['uses' => 'ReimburseController@agree'])->name('.agree'); //通过当前报销
            Route::post('/reject', ['uses' => 'ReimburseController@reject'])->name('.reject'); //驳回当前报销
            Route::get('/print/{reim_id}', ['uses' => 'ReimburseController@printReimbursement'])->name('.print'); //打印审核明细
            Route::post('/audited', ['uses' => 'ReimburseController@getAuditedList'])->name('.audited'); //ajax获取会计已审核报销单
            Route::post('/rejected', ['uses' => 'ReimburseController@getRejectedList'])->name('.rejected'); //ajax获取已驳回报销单
            Route::post('/delete', ['uses' => 'ReimburseController@delete'])->name('.delete'); //删除驳回报销单

            Route::post('/excel', ['uses' => 'ReimburseController@exportAsExcel'])->name('.excel'); //导出为excel
        });
        Route::group(['prefix' => 'check_reimburse', 'as' => '.check_reimburse'], function() {//查看所有报销单
            Route::get('/', ['uses' => 'ReimburseController@checkAllReimbursements']);
            Route::post('/audited', ['uses' => 'ReimburseController@getAllAuditedList'])->name('.audited'); //ajax获取所有已审核报销单
        });
    });
    /* -- 人事系统 -- */
    include('hr.php');

    /* -- 工作流 -- */
    include('workflow.php');

    /* -- 应用管理 -- */
    Route::group(['prefix' => 'app', 'namespace' => 'app', 'as' => 'app'], function() {
        /* --报销系统-- */
        include('reimburse.php');

        Route::get('/crm', function() {//客户CRM
            return redirect(config('api.url.crm.admin'));
        });
    });

    Route::group(['prefix' => 'statistic'], function() {//数据统计
        // Route::get('attendance','StatisticController@list');
        Route::get('attendance', function() {
            return view('hr.statistic');
        });
        Route::any('getstaffdetail', 'StatisticController@getStaffdetail');
        Route::any('getlist', 'StatisticController@getlist');
    });

    /* -- 日志管理 -- */
    include('log.php');

    /* -- 系统设置 -- */
    include('system.php');

    Route::group(['prefix' => 'personal', 'as' => 'personal'], function() { //个人中心
        Route::get('/refresh_authority', ['uses' => 'System\RbacController@refreshAuthority'])->name('.refresh_authority'); //更新权限
    });
});

Route::post('/login-dingtalk', ['uses' => 'LoginController@loginByDingtalkAuthCode']); //检测钉钉登录
Route::get('/login/{url?}', ['uses' => 'LoginController@showLoginPage'])->name('login'); //登录界面
Route::post('/login/{url?}', ['uses' => 'LoginController@loginCheck']);
Route::get('/logout', ['uses' => 'LoginController@logout'])->name('logout');
Route::get('/error', ['uses' => 'ResponseController@showErrorPage'])->name('error'); //报错界面
Route::get('/blank', ['uses' => 'HomeController@showBlankPage'])->name('blank'); //空白页面
/* -- API相关 -- */
Route::get('/get_user_token/{url?}', ['uses' => 'Api\UserController@getUserToken']); //获取当前用户的user_token

/* -- Excel数据源 -- */
Route::group(['prefix' => 'excel'], function() {
    Route::get('staff', ['uses' => 'HR\StaffController@showDataForExcel']); //部门
});
