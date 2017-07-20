<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;

class FormClassifyController extends Controller {

    /**
     * 分类列表
     * @return type
     */
    public function classifyList() {
        
        return view('workflow.classify_list');
    }
    
    /**
     * 表单分类列表
     * @param Request $request
     * @return type
     */
    public function getFormClassifyList(Request $request){
        $request = $request->except(['_url']);
         $url = config('api.url.workflow.formClassifyList');
        $data = Curl::setUrl($url)->sendMessageByPost($request); //获取表单分类列表数据
        return $data;
    }

    /**
     * 表单分类修改
     * @param Request $request
     */
    public function formClassifySave(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.formClassifySave');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     * 表单分类提交进行处理
     * @param Request $request
     */
    public function formClassifySubmit(Request $request) {
        $this->verification($request);
        $url = config('api.url.workflow.formClassifySubmit');
        $data = $request->except(['_token', '_url']);
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        if ($result['result'] == 'success') {
            return 'success';
        }elseif($result['result'] == 'saveSuccess'){
            return 'saveSuccess';
        }
        return 'error';
    }
    /**
     * 删除分类
     * @param Request $request
     */
    public function formClassifyDelete(Request $request){
        $data =$request->except(['_url']);
        $url =config('api.url.workflow.formClassifyDelete');
        if(!empty($request->id)){
            $result =  Curl::setUrl($url)->sendMessageByPost($data);
            if($result['deleteClassify'] == 'success'){
                return 'success';
            }
        }
    }
    /**
     * y验证名称是否重复
     * @param Request $request
     */
    public function formClassifyVeridateName(Request $request){
        $request =$request->except(['_url']);
        $url =config('api.url.workflow.formClassifyVeridateName');
        $data =Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /*     * 验证分类字段
     * @param $request
     */

    private function verification($request) {
        $this->validate($request, [
            'classifyname' => 'required|string|max:30',
            'describe'=>'string|max:255',
        ]);
    }

}
