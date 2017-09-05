<?php
Route::group(['prefix' => 'app', 'namespace' => 'app', 'as' => 'app'], function () {
    /* --报销系统-- */
    Route::group(['prefix' => 'reimburse', 'namespace' => 'Reimburse'], function () {
        Route::get('/approver', ['uses' => 'ReimburseController@approverList']); //配置审批人列表 视图
        Route::post('/approver', ['uses' => 'ReimburseController@approverInfo']); //获取配置审批人列表
        Route::post('/saveApprover', ['uses' => 'ReimburseController@saveApprover']); //保存审批人
        Route::post('/delApprover', ['uses' => 'ReimburseController@delApprover']); //删除审批人
        Route::post('/editApprover', ['uses' => 'ReimburseController@editApprover']); //删除审批人

        Route::get('/auditor', ['uses' => 'ReimburseController@auditorList']); //审核人列表视图
        Route::post('/auditor', ['uses' => 'ReimburseController@auditorInfo']); //审核人列表
        Route::post('/saveAuditor', ['uses' => 'ReimburseController@saveAuditor']); //审核保存
        Route::post('/delAuditor', ['uses' => 'ReimburseController@delAuditor']); //审核数据删除
        Route::post('/editAuditor', ['uses' => 'ReimburseController@editAuditor']); //审核数据修改
    });

    /*CRM*/
    Route::get('/crm', function () {//客户CRM
        return redirect(config('api.url.crm.admin'));
    });
});
