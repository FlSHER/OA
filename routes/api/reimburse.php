<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16/016
 * Time: 17:44
 */
Route::middleware('auth:api')->namespace('Api\Reimburse')->group(function(){
    Route::get('/audit','AuditController@index');//审核列表
    Route::get('/audit/{id}','AuditController@show');//审核详情
    Route::get('/export-audit','AuditController@exportIndex');//导出
    Route::patch('/agree','AuditController@agree');//通过
    Route::patch('/reject','AuditController@reject');//驳回
    Route::delete('/delete','AuditController@destroy');//删除驳回的单
    Route::get('/reim-department','AuditController@getReimDepartment');//获取所有归属数据
    Route::get('/status','AuditController@getStatus');//获取所有状态
    Route::get('/types','AuditController@getTypes');//获取所有明细类型

    Route::get('/deliver','DeliverController@index');//转交列表（已审核的单）
    Route::post('/deliver','DeliverController@store');//转交处理

    Route::get('/audited','CheckAuditedController@index');//全部已审核单列表
    Route::get('/audited/{id}','CheckAuditedController@show');//全部已审核单详情
    Route::patch('/withdraw','CheckAuditedController@withdraw');//撤回已审核单

    Route::get('/pay','PayController@index');//转账列表
    Route::patch('/pay','PayController@pay');//转账
    Route::patch('/pay/reject','PayController@reject');//驳回 转账

    Route::get('/print/{id}','PrintController@index');//获取报销打印数据
    Route::get('/export','ExportController@export');//导出
});
