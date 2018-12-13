<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9/009
 * Time: 16:56
 */
Route::namespace('Api\Sign')->group(function(){

    //获取当前用户 (并签到)
    Route::get('get-user','SignController@getUser');

    // 点击答题开始
    Route::get('start','SignController@start');

    //提交答题
    Route::post('submit','SignController@submit');

    // 获取当前排名
    Route::get('top','SignController@getTop');

    //获取全部排行
    Route::get('all-top','SignController@getAllTop');

    //清除缓存
    Route::get('clear-cache','SignController@clearCache');

    //清除签到数据
    Route::get('clear-sign','SignController@clearSign');

    //开启16点签到
    Route::get('up-sign','SignController@upSign');

    //关闭16点签到
    Route::get('close-sign','SignController@closeSign');
});