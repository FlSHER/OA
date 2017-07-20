<?php

/* -- 日志管理 -- */
Route::group(['prefix' => 'log', 'namespace' => 'Log', 'as' => 'log'], function() { //日志管理
    Route::group(['prefix' => 'staff', 'as' => '.staff'], function() { //员工管理操作日志
        Route::get('/', ['uses' => 'HRController@showStaffLogPage']);
        Route::post('/list', ['uses' => 'HRController@getStaffLogList']); //获取员工管理操作日志列表
    });
    Route::group(['prefix' => 'violation', 'as' => '.violation'], function() { //大爱修改日志
        Route::get('/', ['uses' => 'HRController@showViolationLogPage']);
        Route::post('/list', ['uses' => 'HRController@getViolationLogList']); //获取大爱修改日志列表
    });
});

