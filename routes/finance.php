<?php
Route::group(['prefix' => 'finance', 'namespace' => 'Finance', 'as' => 'finance'], function () {
    /*报销审核*/
    Route::group(['prefix' => 'reimburse', 'namespace' => 'Reimburse', 'as' => '.reimburse'], function () {//报销审核
        Route::get('/', ['uses' => 'ReimburseController@showReimbursePage']);
        Route::post('/list', ['uses' => 'ReimburseController@getHandleList'])->name('.list'); //ajax获取待审核报销单
        Route::post('/expenses', ['uses' => 'ReimburseController@getReimburseExpenses'])->name('.expense'); //ajax获取消费明细
        Route::post('/agree', ['uses' => 'ReimburseController@agree'])->name('.agree'); //通过当前报销
        Route::post('/reject', ['uses' => 'ReimburseController@reject'])->name('.reject'); //驳回当前报销
        Route::get('/print/{reim_id}', ['uses' => 'ReimburseController@printReimbursement'])->name('.print'); //打印审核明细
        Route::post('/audited', ['uses' => 'ReimburseController@getAuditedList'])->name('.audited'); //ajax获取会计已审核报销单
        Route::post('/rejected', ['uses' => 'ReimburseController@getRejectedList'])->name('.rejected'); //ajax获取已驳回报销单
        Route::post('/delete', ['uses' => 'ReimburseController@deleteReject'])->name('.delete'); //删除驳回报销单
        Route::post('/excel', ['uses' => 'ReimburseController@exportAsExcel'])->name('.excel'); //导出为excel
    });
    /*查看报销*/
    Route::group(['prefix' => 'check_reimburse', 'as' => '.check_reimburse', 'namespace' => 'Reimburse'], function () {//查看所有报销单
        Route::get('/', ['uses' => 'CheckReimburseController@checkAllAuditedList']);//列表视图
        Route::post('/audited', ['uses' => 'CheckReimburseController@getAllAuditedList']);//ajax获取所有已审核报销单
        Route::post('/expenses', ['uses' => 'CheckReimburseController@getCheckReimburseExpenses']);//ajax获取消费明细报销单
        Route::get('/print/{reim_id}', ['uses' => 'CheckReimburseController@checkReimbursePrint']); //打印审核明细
        Route::post('/restore', ['uses' => 'CheckReimburseController@restore']);//撤回已审核单
    });
    /*报销转账*/
    Route::group(['prefix' => 'pay_reimburse', 'as' => '.pay_reimburse', 'namespace' => 'Reimburse'], function () {//查看转账报销单
        Route::get('/', ['uses' => 'PayReimburseController@showView']);//列表视图
        Route::post('/not-paid', ['uses' => 'PayReimburseController@getNotPaidList']);//ajax获取所有未转账报销单
        Route::post('/paid', ['uses' => 'PayReimburseController@getPaidList']);//ajax获取所有已转账报销单
        Route::post('/pay', ['uses' => 'PayReimburseController@pay']);//转账
        Route::post('/excel', ['uses' => 'PayReimburseController@exportAsExcel'])->name('.excel'); //导出为excel
    });
});