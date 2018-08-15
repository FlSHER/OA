<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13/013
 * Time: 16:18
 */
Route::middleware('auth:api')->namespace('Api\Dingtalk')->group(function(){
   Route::post('/message','MessageController@sendJobNotificationMessage');
});