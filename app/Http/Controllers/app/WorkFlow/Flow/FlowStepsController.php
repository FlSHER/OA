<?php

namespace App\Http\Controllers\app\WorkFlow\Flow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;
use App\Services\WorkFlowService;

class FlowStepsController extends Controller {

    /**
     * 设计流程步骤
     * @param Request $request
     */
    public function deviseFlowSteps(Request $request) {
        $tmp = $request->all();
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.deviseFlowSteps');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return view('workflow.flow.steps.flow_steps_new', ['tmp' => $tmp, 'data' => $data]);
    }

    /**
     * 新增 流程步骤
     * @param Request $request
     * 
     */
    public function AddFlowStepsList(Request $request) {
        $tmp = $request->all();
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.AddFlowStepsList');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        $flow_type_url = config('api.url.workflow.flowAttribute');
        $flow_data = Curl::setUrl($flow_type_url)->sendMessageByPost(['flow_id' => $tmp['flow_id']]);
        $flow_type = $flow_data['flow_type']; //获取流程的类型  流程类型(1-固定流程,2-自由流程)

        $stepsAll_url = config('api.url.workflow.deviseFlowSteps'); //新增获取所有步骤
        $stepsAll = Curl::setUrl($stepsAll_url)->sendMessageByPost(['flow_id' => $tmp['flow_id']]);
        $allSteps['alternative_steps'] = $stepsAll; //备选步骤
        $prcs_id = isset($data[0]['prcs_id']) ? (null == $data[0]['prcs_id'] ? 1 : $data[0]['prcs_id'] + 1) : 1;
        $field = !empty($this->paserTemplate(['flow_id' => $tmp['flow_id']])) ? $this->paserTemplate(['flow_id' => $tmp['flow_id']]) : ''; //t条件设置所有字段数
        return view('workflow.flow.steps.flow_steps_list')->with(
                        ['prcs_id' => $prcs_id, 'flow_id' => $tmp['flow_id'], 'allSteps' => $allSteps, 'flow_type' => $flow_type, 'view_priv' => $flow_data['view_priv'], 'field' => $field]);
    }

    /**
     * 保存 流程步骤
     * @param Request $request
     */
    public function submitFlowSteps(Request $request) {
        $this->check_validate($request); //验证数据
        //验证转入条件的正确性
        $checkSign = 'true';
        if (!empty($request->prcs_in_set)) {
            $catOutDigit = WorkFlowService::catOutDigit($request->prcs_in_set);
            $checkSign = WorkFlowService::existTbody(['str' => $request->prcs_in, 'arr' => $catOutDigit]);
            if ('false' != $checkSign) {
                $checkSign = WorkFlowService::rollInOut($request->prcs_in_set);
                if ('false' == $checkSign) {
                    return "msg_error";
                }
            } else {
                return "msg_error";
            }
        }
        //验证转出条件的正确性
        if (!empty($request->prcs_out_set)) {
            $catOutDigit = WorkFlowService::catOutDigit($request->prcs_out_set);
            $checkSign = WorkFlowService::existTbody(['str' => $request->prcs_in, 'arr' => $catOutDigit]);
            if ('false' != $checkSign) {
                $checkSign = WorkFlowService::rollInOut($request->prcs_out_set);
                if ('false' == $checkSign) {
                    return "msg_error";
                }
            } else {
                return "msg_error";
            }
        }
        $flow_type = $this->getFlowType($request); //得到该流程的 流程类型(1-固定流程,2-自由流程)
        if ($flow_type == 1) {
            $url = config('api.url.workflow.checkMinFlowSteps');
            $minPrcsId = Curl::setUrl($url)->sendMessageByPost(['flow_id' => $request->flow_id]);
            if ($minPrcsId != $request->prcs_id && !empty($minPrcsId)) {
                $auto_type = $this->check_auto_type($request); //自动选人规则 
                if (empty($auto_type)) {
                    //固定流程 必需有经办授权范围其中一个
                    if (empty($request->prcs_user) && empty($request->prcs_dept) && empty($request->prcs_priv)) {
                        return 'flow_type_error';
                    }
                }
            }
        }

        $array = $request->except(['_url', 'department_list_length', 'position_list_length']);
        $validate_prcs_id_arr = array_only($array, ['flow_id', 'prcs_id', 'id']); //获取验证步骤id的字段
        $result = $this->validate_prcs_id($validate_prcs_id_arr); //进行步骤id字段验证是否重复
        if (!empty($result)) {
            return 'error'; //步骤id重复
        }
        $url = config('api.url.workflow.submitFlowSteps');
        $data = Curl::setUrl($url)->sendMessageByPost($array);
        if ($data['result'] == 'insertSuccess') {
            return 'insertSuccess';
        } elseif ($data['result'] == 'saveSuccess') {
            return 'saveSuccess';
        } else {
            return "msg_error";
        }
    }

    /**
     * 得到流程类型  流程类型(1-固定流程,2-自由流程)
     * @param type $request
     */
    private function getFlowType($request) {
        $flow_type_url = config('api.url.workflow.flowAttribute');
        $flow_data = Curl::setUrl($flow_type_url)->sendMessageByPost(['flow_id' => $request->flow_id]);
        return $flow_data['flow_type']; //获取流程的类型  流程类型(1-固定流程,2-自由流程)
    }

    /**
     * 验证提交字段
     * @param type $request
     */
    private function check_validate($request) {
        $this->validate($request, [
            'prcs_id' => 'required|integer',
            'prcs_type' => 'required|between:0,2',
            'prcs_name' => 'required|string',]
        );
    }

    /**
     * 验证后台服务器 流程步骤ID是否重复 并进行相应处理
     */
    private function validate_prcs_id($request) {
        $url = config('api.url.workflow.prcsIdRepetition');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     * 编辑 流程步骤
     * @param Request $request
     * id_name  点击流程步骤列表这几个按钮时（ 基本属性  经办权限  可写字段  保密字段  必填字段  条件设置 ） 传过去对应的id
     *  
     */
    public function updateFlowStepsList(Request $request) {
        $id = $request->id; //当前的步骤主键id
        $url = config('api.url.workflow.updateFlowSteps');
        $data = Curl::setUrl($url)->sendMessageByPost(['id' => $id]);

        $flow_type_url = config('api.url.workflow.flowAttribute');
        $flow_data = Curl::setUrl($flow_type_url)->sendMessageByPost(['flow_id' => $data['flow_id']]);
        $data['flow_type'] = $flow_data['flow_type']; //获取流程的类型  流程类型(1-固定流程,2-自由流程)
        $data['flow_type_view_priv'] = $flow_data['view_priv']; //获取流程是否允许传阅 ((0-不允许,1-允许))

        if (!empty($data['prcs_user'])) {//授权范围人员
            $temp_arr = json_decode($data['prcs_user'], true);
            $str = '';
            foreach ($temp_arr as $k => $v) {
                $str .= $v['realname'] . ',';
            }
            $data['copy_prcs_user_name'] = $str;
        }
        if (!empty($data['prcs_dept'])) {
            $data['copy_prcs_dept_name'] = $this->getAuthorizationInfo($data['prcs_dept']); //授权范围（部门）字符串
        }
        if (!empty($data['prcs_priv'])) {
            $data['copy_prcs_priv_name'] = $this->getAuthorizationInfo($data['prcs_priv']); //授权范围（角色）字符串
        }

        $allSteps = $this->getAllSteps($data); //当前流程 全部步骤包含（下一步骤和备选步骤）
        //可写字段all   start-----------------------------------

        if (!empty($data['prcs_item'])) {
            $data['prcs_item'] = json_decode($data['prcs_item'], true); //可写子段
        }
        $data['prcs_item_optional_field'] = $this->prcs_item_optional_field($data, $data['prcs_item']); //可写备选字段
        //可写字段all   end-----------------------------------
        //保密字段all   start-----------------------------------
        if (!empty($data['hidden_item'])) {
            $data['hidden_item'] = json_decode($data['hidden_item'], true); //保密子段
        }
        $data['hidden_item_optional_field'] = $this->prcs_item_optional_field($data, $data['hidden_item']); //保密备选字段
        //保密字段all   end-----------------------------------
        //必填字段all   start-----------------------------------
        if (!empty($data['required_item'])) {
            $data['required_item'] = json_decode($data['required_item'], true); //必填子段
        }
        $data['required_item_optional_field'] = $this->prcs_item_optional_field($data, $data['required_item']); //必填 备选字段
        //必填字段all   end-----------------------------------

        $data['flow_prcs_all_field'] = !empty($this->paserTemplate(['flow_id' => $data['flow_id']])) ? $this->paserTemplate(['flow_id' => $data['flow_id']]) : ''; //t条件设置所有字段数
        $data = $this->getConditionMsg($data);
        return view('workflow.flow.steps.flow_steps_list')->with(['id_name' => $request->id_name, 'data' => $data, 'allSteps' => $allSteps]);
    }

    /**
     * 获取条件设置
     */
    private function getConditionMsg($data) {
        $data['prcs_in_hide'] = $data['prcs_in'];
        $data['prcs_out_hide'] = $data['prcs_out'];
        if (!empty($data['prcs_in']) && 'null' != $data['prcs_in']) {
            $data['prcs_in'] = explode(",", $data['prcs_in']);
            $prcs_in = [];
            foreach ($data['prcs_in'] as $inKey => $inValue) {
                if (!empty($inValue)) {
                    $prcs_in[] = explode("/", $inValue);
                }
            }
            $data['prcs_in'] = $prcs_in;
        }
        if (!empty($data['prcs_out']) && 'null' != $data['prcs_out']) {
            $data['prcs_out'] = explode(",", $data['prcs_out']);
            $prcs_out = [];
            foreach ($data['prcs_out'] as $outKey => $outValue) {
                if (!empty($outValue)) {
                    $prcs_out[] = explode("/", $outValue);
                }
            }
            $data['prcs_out'] = $prcs_out;
        }
        return $data;
    }

    /**
     * 解析授权范围 数据
     */
    private function getAuthorizationInfo($prcs) {
        $prcs_arr = json_decode($prcs, true);
        $str = '';
        foreach ($prcs_arr as $k => $v) {
            $str .= $v['name'] . ',';
        }
        return $str;
    }

    /**
     * 可写字段、保密字段、必填字段的  备选字段数据
     * @param type $data
     * @return type
     */
    private function prcs_item_optional_field($data, $this_field) {
        $prcs_item_data = $this->paserTemplate(['flow_id' => $data['flow_id']]); //所有字段数
        return $this->getPrcsItemOptionalFfield($this_field, $prcs_item_data); //备选字段
    }

    /**
     * 编辑时得到 可写字段、保密字段、必填字段 的备选字段数据
     *  $this_field  传入可写字段、保密字段、必填字段  3个 去匹配
     * 返回备选字段
     */
    private function getPrcsItemOptionalFfield($this_field, $all_field_data) {
        if (!empty($this_field)) {
            foreach ($all_field_data as $k => $v) {
                foreach ($this_field as $key => $val) {
                    if (isset($v['checkboxs'])) {
                        foreach ($v['checkboxs'] as $vk => $vv) {
                            if ($vv['name'] == $key) {
                                unset($all_field_data[$k]['checkboxs'][$vk]);
                            }
                        }
                    } else {
                        if ($v['name'] == $key || $v['title'] == $val) {
                            unset($all_field_data[$k]);
                        }
                    }
                }
            }
            return $all_field_data;
        }
        return $all_field_data;
    }

    /**
     * 得到该流程全部步骤
     * @param type $data
     * 返回 下一步步骤 和备选步骤 数据
     */
    private function getAllSteps($data) {
        $prcs_to_str = $data['prcs_to']; //当前流程 下一步骤id字符串
        $prcs_to_array = explode(',', $prcs_to_str);
        $stepsAll_url = config('api.url.workflow.deviseFlowSteps');
        $stepsAll = Curl::setUrl($stepsAll_url)->sendMessageByPost(['flow_id' => $data['flow_id']]);
        $next_prcs_to_all = []; //下一步骤数据
        $alternative_steps = []; //备选步骤 数据
        foreach ($stepsAll as $k => $v) {
            if (in_array($v['prcs_id'], $prcs_to_array)) {
                $next_prcs_to_all[] = $stepsAll[$k];
            } else {
                $alternative_steps[] = $stepsAll[$k];
            }
        }
        return $allSteps = [
            'next_prcs_to_all' => $next_prcs_to_all,
            'alternative_steps' => $alternative_steps
        ];
    }

    /**
     * 验证流程步骤ID是否重复
     */
    public function prcsIdRepetition(Request $request) {
        $tmp = $request->all();
        if (!empty($tmp['flow_id']) && !empty($tmp['prcs_id'])) {
            $request = $request->except(['_url']);
            $url = config('api.url.workflow.prcsIdRepetition');
            $data = Curl::setUrl($url)->sendMessageByPost($request);
            if (!empty($data)) {
                $msg = ['status' => 1, 'msg' => '不能重复'];
            } else {
                $msg = ['status' => 2, 'msg' => '正确'];
            }
            echo json_encode($msg);
        } else if (empty($tmp['prcs_id'])) {
            $msg = ['status' => 0, 'msg' => '不能为空'];
            echo json_encode($msg);
        }
    }

    /**
     * 可写字段读取模板html内容
     */
    public function stepsWritableTemplate(Request $request) {
        $request = $request->except(['_url']);
        echo json_encode($this->paserTemplate($request));
    }

    /**
     * 解析模板
     * @$request:是json数组flow_id;如['flow_id'=>4]
     */
    public function paserTemplate($request) {
        $url = config('api.url.workflow.stepsWritableTemplate');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        $preg = "/(\|-<span(((?!<span).)*leipiplugins=\"(radios|checkboxs|select)\".*?)>(.*?)<\/span>-\||<(img|input|textarea|select).*?(<\/select>|<\/textarea>|\/>))/s";
        $result = [];
        if (!empty($data) && isset($data[0])) {
            preg_match_all($preg, $data[0]['template'], $arr);
            $tag_preg = '/(?<=<)[\x00-\xff]+?(?=\s)/'; //匹配标签
            $type_preg = '/(?<=type=\")[\x00-\xff]+?(?=\")/'; //匹配类型
            $name_preg = '/(?<=name=\")data_\d+(?=\")/'; //匹配name属性
            $title_preg = '/(?<=title=\")[\x00-\xff]+?(?=\")/'; //匹配title属性
            $value_preg = '/(?<=value=\")[\x00-\xff]+?(?=\")/'; //匹配value属性
            $checkboxs_preg = '/(?<=[\'"])checkboxs?(?=[\'"])/'; //匹配checkboxs标签
            $checkboxs_input_preg = '/(?:<input)[\x00-\xff]+(?=<\/)/'; //匹配checkboxs_input标签
            foreach ($arr[0] as $k => $v) {
                preg_match($checkboxs_preg, $v, $checkboxs);
                preg_match($tag_preg, $v, $tag);
                preg_match($type_preg, $v, $type);
                preg_match($name_preg, $v, $name);
                preg_match($title_preg, $v, $title);
                if (!empty($checkboxs) && 'checkboxs' == $checkboxs[0]) {
                    preg_match_all($checkboxs_input_preg, $v, $checkboxs_input);
                    if (!empty($checkboxs_input) && isset($checkboxs_input[0]) && isset($checkboxs_input[0][0])) {
                        preg_match_all($preg, $checkboxs_input[0][0], $checkboxs_input_arr);
                        foreach ($checkboxs_input_arr[0] as $key => $value) {
                            preg_match($tag_preg, $value, $tag);
                            preg_match($type_preg, $value, $type);
                            preg_match($name_preg, $value, $name);
                            preg_match($value_preg, $value, $val);
                            $result[$k]['checkboxs'][$key]['tag'] = isset($tag[0]) ? $tag[0] : "";
                            $result[$k]['checkboxs'][$key]['type'] = isset($type[0]) ? $type[0] : "";
                            $result[$k]['checkboxs'][$key]['name'] = isset($name[0]) ? $name[0] : "";
                            $result[$k]['checkboxs'][$key]['value'] = isset($val[0]) ? $val[0] : "";
                        }
                        $result[$k]['title'] = isset($title[0]) ? $title[0] : "";
                    }
                } else {
                    $result[$k]['tag'] = isset($tag[0]) ? $tag[0] : "";
                    $result[$k]['type'] = isset($type[0]) ? $type[0] : "";
                    $result[$k]['name'] = isset($name[0]) ? $name[0] : "";
                    $result[$k]['title'] = isset($title[0]) ? $title[0] : "";
                }
            }
        }
        return $result;
    }

    /**
     * 流程步骤删除
     * @param Request $request
     */
    public function deleteFlowSteps(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.deleteFlowSteps');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        if ($data['result'] == 'success') {
            return "success";
        }
        return "error";
    }

    /**
     * 克隆流程步骤
     * @param Request $request
     */
    public function cloneFlowSteps(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.cloneFlowSteps');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        if ($data['result'] == 'success') {
            return 'success';
        }
        return 'error';
    }

    /**
     * 智能选人验证自动选人规则
     * @param Request $request
     */
    public function check_auto_type(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.check_auto_type');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

}
