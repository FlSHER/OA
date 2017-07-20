<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;

class FlowTypeController extends Controller {

    /**
     * 流程管理列表
     * @param Request $request
     * @return type
     */
    public function flowTypeList(Request $request) {
        return view('workflow.flow_type');
    }

    /**
     * 流程管理table列表
     * @param Request $request
     */
    public function flowTypeTableList(Request $request) {
        $request = $request->except(['_url', '_token']);
        $url = config('api.url.workflow.flowTypeTableList');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     * 流程管理 流程启用
     * @param Request $request
     */
    public function flowTypeRelease(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowTypeRelease');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data['result'];
    }

    /**
     * 流程管理 流程停用
     * @param Request $request
     */
    public function flowTypeStop(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowTypeStop');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data['result'];
    }
    
    /**
     * 流程管理 流程删除
     * @param Request $request
     */
    public function flowTypeDelete(Request $request){
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowTypeDelete');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data['result'];
    }

}
