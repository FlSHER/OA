<?php

/* -- 系统设置 -- */
Route::group(['prefix' => 'system', 'namespace' => 'System', 'as' => 'system'], function() {
    Route::group(['prefix' => 'role', 'as' => '.role'], function() { //角色管理
        Route::get('/', ['uses' => 'RbacController@showRolePage']);
        Route::post('/list', ['uses' => 'RbacController@getRoleList'])->name('.list'); //获取角色列表
        Route::post('/add', ['uses' => 'RbacController@addRoleByOne'])->name('.add'); //添加角色
        Route::post('/edit', ['uses' => 'RbacController@editRoleByOne'])->name('.edit'); //编辑角色
        Route::post('/delete', ['uses' => 'RbacController@deleteRoleByOne'])->name('.delete'); //删除角色
    });
    Route::group(['prefix' => 'authority', 'as' => '.authority'], function() { //权限管理
        Route::get('/', ['uses' => 'RbacController@showAuthorityPage']);
        Route::post('/list', ['uses' => 'RbacController@getAuthorityList'])->name('.list'); //获取权限列表
        Route::post('/treeview', ['uses' => 'RbacController@getAuthorityTreeView'])->name('.treeview'); //获取权限树形图
        Route::post('/set', ['uses' => 'RbacController@setAuthority'])->name('.set'); //设置权限（中间表）
        Route::post('/order', ['uses' => 'RbacController@reOrderAuthority'])->name('.order'); //权限排序
    });
    Route::get('/flush_cache', ['uses' => 'CacheController@flushCache'])->name('.flush_cache'); //清除缓存
});

