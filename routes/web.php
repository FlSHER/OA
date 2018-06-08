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

Auth::routes();
Route::get('/logout/{url?}', ['uses' => 'Auth\LoginController@logout'])->name('logout');

Route::group(['middleware' => 'admin'], function () {
    Route::get('/', ['uses' => 'HomeController@showDashBoard'])->name('home'); //首页仪表盘
    Route::get('/entrance', ['uses' => 'HomeController@showAppEntrance'])->name('entrance'); //应用端入口
    Route::get('/reset_password', ['uses' => 'Auth\LoginController@showResetPage'])->name('reset'); //重置密码
    Route::post('/reset_password', ['uses' => 'Auth\LoginController@resetPassword']);
    /* -- 财务系统 -- */
    include('finance.php');
    /* -- 人事系统 -- */
    include('hr.php');

    /* -- 工作流 -- */
    include('workflow.php');

    /* -- 应用管理 -- */
    include('app.php');

    Route::group(['prefix' => 'statistic'], function () {//数据统计
        // Route::get('attendance','StatisticController@list');
        Route::get('attendance', function () {
            return view('hr.statistic');
        });
        Route::any('getstaffdetail', 'StatisticController@getStaffdetail');
        Route::any('getlist', 'StatisticController@getlist');
    });

    /* -- 日志管理 -- */
    include('log.php');

    /* -- 系统设置 -- */
    include('system.php');

    Route::group(['prefix' => 'personal', 'as' => 'personal'], function () { //个人中心
        Route::get('/refresh_authority', ['uses' => 'System\RbacController@refreshAuthority'])->name('.refresh_authority'); //更新权限
    });
});

Route::post('/login-dingtalk', ['uses' => 'Auth\LoginController@loginByDingtalkAuthCode']); //检测钉钉登录

Route::get('/error', ['uses' => 'ResponseController@showErrorPage'])->name('error'); //报错界面
Route::get('/blank', ['uses' => 'HomeController@showBlankPage'])->name('blank'); //空白页面

/* -- Excel数据源 -- */
Route::group(['prefix' => 'excel'], function () {
    Route::get('staff', ['uses' => 'HR\StaffController@showDataForExcel']); //部门
});

Route::get('weather_image', function () {
    app('ApiResponse')->getWeatherImage(request()->input('city'));
    return response('')
        ->header('Content-Type', 'image/png');
});

Route::get('/opcache_clear', function () {
    return opcache_reset() ? 'success' : 'error';
});