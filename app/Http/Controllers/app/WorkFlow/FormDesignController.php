<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;
use Response;
use DB;
use App\Services\DatabaseManageService;

class FormDesignController extends Controller {

    /**
     * 表单设计列表
     * @param Request $request
     */
    public function formDesignList(Request $request) {
        $form_id = $request->id;
        $url = config('api.url.workflow.formDesignUpdate');
        $data = Curl::setUrl($url)->sendMessageByPost(['form_id' => $form_id]);
//        $data['template'] = preg_replace("/DATA_\d+/", "leipiNewField", $data['template']);
//        $data['template'] = preg_replace("/data_\d+/", "leipiNewField", $data['template']);
        return view('workflow.form_design_list', ['form_id' => $form_id, 'data' => $data]);
    }

    /**
     * 表单设计保存
     * @param Request $request
     */
    public function formDesignSave(Request $request) {
        $this->form_design_validation($request);
        $data = $request->parse_form;
        $data = json_decode($data, true);
        $data = array_except($data, ['parse', 'data', 'add_fields']);
        //验证是否含有title   start
        $preg = "/(\|-<span(((?!<span).)*leipiplugins=\"(radios|checkboxs|select)\".*?)>(.*?)<\/span>-\||<(img|input|textarea|select).*?(<\/select>|<\/textarea>|\/>))/s";
        preg_match_all($preg, $data['template'], $temparr);
        $preg_attr = "/title=[\"\'][\w\x{4e00}-\x{9fa5}]+[\"\']/u";
        foreach ($temparr[0] as $k => $v) {
            if (!preg_match($preg_attr, $v)) {
                return 'titleError';
            }
        }
        //验证是否含有title   end
//        $data['add_fields'] =json_encode($data['add_fields']);
        $data['form_id'] = $request->form_id;
        $data['create_time'] = time();
//        $data['mobile_template'] =$this->getMobilePhone($data['template']);//得到手机端模板
        $url = config('api.url.workflow.formDesignSave');
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        if ($result['result'] == 'success') {
            return 'success';
        } elseif ($result['result'] == 'updateError') {
            return 'updateError';
        }
    }

    /*     * *
     * 验证模板是否含有 title属性
     */

    private function validateTemplateTitle($template) {
        $preg = "/(\|-<span(((?!<span).)*leipiplugins=\"(radios|checkboxs|select)\".*?)>(.*?)<\/span>-\||<(img|input|textarea|select).*?(<\/select>|<\/textarea>|\/>))/s";
        preg_match_all($preg, $template, $temparr);
        $preg_attr = "/title=[\"\'][\w\x{4e00}-\x{9fa5}]+[\"\']/u";
        foreach ($temparr[0] as $k => $v) {
//            echo($v.'<br>'."\r\n");
            if (!preg_match($preg_attr, $v)) {
                return false;
            }
        }
//        die;
        return true;
    }

    /**
     * 表单预览
     * @param Request $request
     */
    public function formDesignPreview(Request $request) {
        if ($request->id) {
            //列表预览
            $url = config('api.url.workflow.formDesignUpdate');
            $result = Curl::setUrl($url)->sendMessageByPost(['form_id' => $request->id]);
            $data = $result['template'];
            $data = preg_replace("/{\|-/", " ", $data);
            $data = preg_replace("/-\|}/", " ", $data);
            $data = \App\Services\FormDesignService::getDataValue($data);
        } else {
            //设计时预览
            $data = $request->design_content; //设计时预览
//            dump($data);
            $data = preg_replace("/{\|-/", " ", $data);
            $data = preg_replace("/-\|}/", " ", $data);

            $data = \App\Services\FormDesignService::getDataValue($data);
        }
//        dump($data);
        return Response::view('workflow.preview', ['data' => $data])->header('X-XSS-Protection', 0);
    }

    /**
     * 移动设计
     * @param Request $request
     */
    public function formDesignPhonePreview(Request $request) {
        $id = $request->id;
        $url = config('api.url.workflow.formDesignPhonePreview');
        $data = Curl::setUrl($url)->sendMessageByPost(['id' => $id]);
        return view('workflow.phone_preview', ['data' => $data]);
    }

    /**
     * 表单设计提交验证
     */
    private function form_design_validation($request) {
        $this->validate($request, [
            'form_id' => 'required|string',
            'parse_form' => 'required|string',
//            'formeditor'=>'required|string',
                ]
        );
    }

    /**
     * 列表控件 数据来源  数据库数据类型数据
     * @param Request $request
     */
    public function dbConnectionInfo(Request $request) {
        $url = config('api.url.workflow.databaseManageList');
        $data = Curl::setUrl($url)->sendMessageByPost([]);
        return $data;
    }

    /**
     * 列表控件  获取数据来源表
     * @param Request $request
     */
    public function getInternalDataTable(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.databaseManageUpdateBefore');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return DatabaseManageService::getDataTable($data);
    }

    /**
     * 列表控件 获取数据的表的字段
     * @param Request $request
     */
    public function getInternalDataField(Request $request) {
        $url = config('api.url.workflow.databaseManageUpdateBefore');
        $data = Curl::setUrl($url)->sendMessageByPost(['id' => $request->id]);
        return DatabaseManageService::getInternalDataField($data, $request->table); //$data数据['id'=>1,'connection'=>'mysql','host'=>'127.0.0.1'].   $request->table 表名
    }

    /**
     * 表单预览操作点击选择按钮
     * @param Request $request
     */
    public function optionalFieldList(Request $request) {
        $data = $request->except(['_url']);
        $data['fields_arr'] = explode(',', $data['fields_arr']);
        $url = config('api.url.workflow.databaseManageUpdateBefore');
        $result = Curl::setUrl($url)->sendMessageByPost(['id' => $data['id']]);
        $count = DatabaseManageService::getfieldsCount($result, $data['table'], $data['fields_arr']);
        $page = DatabaseManageService::getPage($request, $count);
        $info = DatabaseManageService::getDataFieldsInfo($result, $data['table'], $data['fields_arr'], $page['start'], $page['length']);
        return view('workflow.optionalField')->with(['data' => $info, 'fields' => $data['fields_arr'], 'index' => $request->index, 'page' => $page]);
    }

    /**
     * 分页数据处理
     * @param Request $request
     */
    public function fieldsPage(Request $request) {
        $data = $request->except(['_url']);
        $data['fields_arr'] = explode(',', $data['fields_arr']);
        $url = config('api.url.workflow.databaseManageUpdateBefore');
        $result = Curl::setUrl($url)->sendMessageByPost(['id' => $data['id']]);
        $count = DatabaseManageService::getfieldsCount($result, $data['table'], $data['fields_arr']);
        $page = DatabaseManageService::getPage($request, $count);
        $info = DatabaseManageService::getDataFieldsInfo($result, $data['table'], $data['fields_arr'], $page['start'], $page['length']);
        return $info;
    }

}
