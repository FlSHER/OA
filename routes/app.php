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

    /**
     * 工作任务
     */
    Route::group(['prefix'=>'work_mission','namespace'=>'WorkMission'],function(){
        /**
         * 分配人员配置
         */
        Route::get('/allotment_user','AllotmentUserController@index');//分配人员配置视图
        Route::post('/allotment_user/list','AllotmentUserController@listData');//获取列表数据
        Route::post('/allotment_user/save','AllotmentUserController@save');//保存
        Route::post('/allotment_user/update','AllotmentUserController@update');//修改
        Route::post('/allotment_user/delete','AllotmentUserController@delete');//删除
    });

    /*CRM*/
    Route::get('/crm', function () {//客户CRM
        return redirect(config('api.url.crm.admin'));
    });
});
