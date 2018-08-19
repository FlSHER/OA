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
    //待办事项
    Route::post('/todo/add', 'TodoController@add');//发起待办
});