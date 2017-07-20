<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DingtalkController extends Controller
{
   public function getDingTalkAccessToken(){
       return app('Dingtalk')->getAccessToken();
   }
}
