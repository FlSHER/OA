<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;

class FlowClassifyController extends Controller {

    /**
     * 流程分类保存
     * @param Request $request
     * @return string
     */
    public function flowClassifySubmit(Request $request) {
        $this->flow_verification($request);
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowClassifySubmit');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        if ($data['result'] == 'success') {
            return 'success';
        }elseif ($data['result'] == 'saveFlowClassify') {
            return 'saveFlowClassify';
        }
        return 'error';
    }

    /**
     * 流程分类列表
     * @param Request $request
     */
    public function flowClassifyList(Request $request) {
        $request = $request->except(['_url','_token']);
        $url = config('api.url.workflow.flowClassifyList');
        $data = Curl::setUrl($url)->sendMessageByPost($request); //获取表流程分类列表数据
        return $data;
    }
    /**
     * 流程分类修改
     * @param Request $request
     */
    public function flowClassifySave(Request $request) {
        $request = $request->except(['_url']);
        $url =config('api.url.workflow.flowClassifySave');
        $data =Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }
    /**
     * 流程分类删除
     * @param Request $request
     */
    public function flowClassifyDelete(Request $request){
        $request =$request->except(['_url']);
        $url =config('api.url.workflow.flowClassifyDelete');
        if(!empty($request['id'])){
            $data =Curl::setUrl($url)->sendMessageByPost($request);
            if($data['result'] == 'deleteFormClassifySuccess'){
                return 'success';
            }
        }
    }
    /**
     * 验证流程分类名称是否重复
     * @param Request $request
     */
    public function flowClassifyValidateName(Request $request){
        $request =$request->except(['_url']);
        $url =config('api.url.workflow.flowClassifyVeridateName');
        $data =Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     *  验证分类字段
     * @param $request
     */
    private function flow_verification($request) {
        $this->validate($request, [
            'flow_classifyname' => 'required|string|max:30',
            'flow_describe' => 'string|max:255',
        ]);
    }

}
