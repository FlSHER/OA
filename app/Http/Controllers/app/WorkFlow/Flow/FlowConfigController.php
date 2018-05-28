<?php

namespace App\Http\Controllers\app\WorkFlow\Flow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;

const VERIFY_IN_FIELD = ['flow_name','form_name'];

class FlowConfigController extends Controller
{
    /**
     * 工作流设置列表
     * @return type
     */
    public function flowConfigList() {
        return view('workflow.flow.flow_config_list');
    }
    /**
     * 流程设置创建
     * @param Request $request
     */
    public function flowConfigCreate(Request $request) {
        $url = config('api.url.workflow.flowConfigCreateFormClassifyList');
        $data = Curl::setUrl($url)->sendMessageByPost([]); //获取分类列表数据
        return $data;
    }
    /**
     * 流程选择表单
     * @param Request $request
     */
    public function flowFormSet(Request $request) {
        $url = config('api.url.workflow.flowFormSet');
        $data = Curl::setUrl($url)->sendMessageByPost([]); //获取分类列表数据
        //return $data;
        echo json_encode($data);
    }
    /**
     * 流程设置验证名称是否重复
     * @param Request $request
     */
    public function flowConfigValidateName(Request $request){
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowConfigValidateName');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }
    /**
     * 流程设置提交
     * @param Request $request
     */
    public function flowConfigSubmit(Request $request) {
        $msg = $this->flow_config_validation($request);
        if(empty($msg))
        {
            $result = $request->except(['_url']);
            //$result['staff_sn'] = session('admin')['staff_sn'];
            $url = config('api.url.workflow.flowConfigSubmit');
            $data = Curl::setUrl($url)->sendMessageByPost($result);
            if ($data['result'] == 'success') {
                return 'success';
            } elseif ($data['result'] == 'saveSuccess') {
                return 'saveSuccess';
            }
        }
        else
        {
            echo json_encode($msg);
        }
    }
    /**
     * 流程设置定义属性修改
     * @param Request $request
     */
    public function flowConfigUpdateSave(Request $request) {
        $msg = $this->flow_config_validation($request);
        if(empty($msg))
        {
            $result = $request->except(['_url']);
            //$result['staff_sn'] = session('admin')['staff_sn'];
            $url = config('api.url.workflow.flowConfigUpdateSave');
            $data = Curl::setUrl($url)->sendMessageByPost($result);
            if ($data == 'success') {
                echo 'success';
            } elseif ($data == 'saveSuccess') {
                echo 'saveSuccess';
            }
        }
        else
        {
            echo json_encode($msg);
        }
    }
    /**
     * 表单设置提交验证
     */
    private function flow_config_validation($request) {
        $tmp = $request->all();
        $msg = [];
        foreach ($tmp as $tmpKey => $tmpValue) {
            if(in_array($tmpKey,VERIFY_IN_FIELD))
            {
                if(empty($tmpValue))
                {
                    $msg[$tmpKey] = "不能为空";
                }
            }
        }
        return $msg;
    }
    /**
     * 获取流程左侧树形菜单列表
     * @param Request $request
     */
    public function flowLeftMenu(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowLeftMenu');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }
    /**
     * 查询流程左侧树形菜单列表
     * @param Request $request
     */
    public function flowLeftMenuSerach(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.flowLeftMenuSerach');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }
    /**
     * 传阅设置人员页面
     * @param Request $request
     */
    public function passReadPerson(Request $request) {
        return view('workflow.flow.pass_read_person');
    }
    /**
     * 传阅设置部门页面
     * @param Request $request
     */
    public function passReadDept(Request $request) {
        return view('workflow.flow.pass_read_dept');
    }
    /**
     * 传阅设置角色页面
     * @param Request $request
     */
    public function passReadRole(Request $request) {
        return view('workflow.flow.pass_read_role');
    }
}
