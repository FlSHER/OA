<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 *  数据统计
 */
class StatisticController extends Controller
{
    //
    public function list(){

    }

    //员工考勤/业绩
    public function getlist(Request $request){
    	return $month = date('Y-m-d',strtotime(date('Y-m-01',time()).'+1month -1day'));
    	return $request->all();
    }

    //员工 迟到信息
    public function getStaffDetail(Request $request){
    	$input = $request->except('_url');

    	$url = config('api.url.statistic.getstaffdetail');
    	$input = http_build_query($input);
    	$ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL,$url);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
    	curl_setopt($ch, CURLOPT_POST,true);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    	curl_setopt($ch,CURLOPT_HEADER,0);
    	$output = curl_exec($ch);
    	curl_close($ch);
    	return $output;
    }
}
