<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13/013
 * Time: 16:18
 */
Route::middleware('auth:api')->namespace('Api\Dingtalk')->group(function () {
    //消息通知 （工作通知消息）
    Route::post('/message', 'MessageController@sendJobNotificationMessage');
    //发起待办事项
    Route::post('/todo/add', 'TodoController@add');//发起待办
    //更新待办事项
    Route::post('/todo/update','TodoController@update');

    //获取待办消息列表
    Route::get('todo','TodoController@index');
    //重发待办失败的消息
    Route::post('todo/{id}','TodoController@retrace');

    //获取工作通知消息列表
    Route::get('job','MessageController@index');
    //重发工作通知失败的信息
    Route::post('job/{id}','MessageController@retrace');
});