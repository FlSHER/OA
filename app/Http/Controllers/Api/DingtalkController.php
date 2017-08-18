<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ApiResponse;

class DingtalkController extends Controller {

    public function getAccessToken() {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getAccessToken(), 200);
    }

    public function getJsApiTicket() {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getJsApiTicket(), 200);
    }

}
