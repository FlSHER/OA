<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;

class ReleaseFlowController extends Controller
{
    /**
     * 已发布流程列表
     * @param Request $request
     * @return type
     */
    public function releaseFlow(Request $request){
        return view('workflow.releaseFlow');
    }
    /**
     * 已发布流程table列表
     * @param Request $request
     */
    public function releaseFlowTable(Request $request) {
        $request = $request->except(['_url', '_token']);
        $url = config('api.url.workflow.releaseFlowTable');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }
}
