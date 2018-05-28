<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;

class HomeController extends Controller {

    public function home() {
        return view('workflow.home');
    }

    /**
     * 流程运行table列表
     * @param Request $request
     */
    public function flowRunTableList(Request $request) {
        $request = $request->except(['_url', '_token']);
        $url = config('api.url.workflow.flowRunTableList');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

}
