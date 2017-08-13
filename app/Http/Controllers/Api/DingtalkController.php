<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DingtalkController extends Controller
{
   public function getAccessToken(){
       return app('Dingtalk')->getAccessToken();
   }
   
   public function getJsApiTicket(){
       return app('Dingtalk')->getJsApiTicket();
   }
}
