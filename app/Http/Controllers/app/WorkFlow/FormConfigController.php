<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl;
use Illuminate\Support\Facades\Storage;

class FormConfigController extends Controller {

    /**
     * 表单设置列表
     * @return type
     */
    public function formConfigList() {
        return view('workflow.form_config_list');
    }

    /**
     * 表单设置table列表
     * @param Request $request
     * @return type
     */
    public function formConfigTableList(Request $request) {
        $request = $request->except(['_url', '_token']);
        $url = config('api.url.workflow.formConfigList');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     * 表单设置创建
     * @param Request $request
     */
    public function formConfigCreate(Request $request) {
        $url = config('api.url.workflow.formConfigCreateFormClassifyList');
        $data = Curl::setUrl($url)->sendMessageByPost([]); //获取分类列表数据
        return $data;
    }

    /**
     * 表单设置提交
     * @param Request $request
     */
    public function formConfigSubmit(Request $request) {
        if ($request->hasFile('excelForm') && $request->file('excelForm')->isValid() && $request->file('excelForm')->extension() == 'html' || $request->hasFile('excelForm') && $request->file('excelForm')->isValid() &&$request->file('excelForm')->extension() == 'txt') {
            $filePath = $request->excelForm->path();
            $html = file_get_contents($filePath); //HTML模板文件
            $encode = mb_detect_encoding($html, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
            if ($encode != 'UTF-8') {
                $html = iconv($encode, 'utf-8', $html);
            }
            $html = preg_replace("/DATA_/", "data_", $html);
             $preg =  "/(\|-<span(((?!<span).)*leipiplugins=\"(radios|checkboxs|select)\".*?)>(.*?)<\/span>-\||<(img|input|textarea|select).*?(<\/select>|<\/textarea>|\/>))/s";
              preg_match_all($preg,$html,$temparr);
              $preg_attr ="/title=[\"\'][\w\x{4e00}-\x{9fa5}]+[\"\']/u";
              $name_preg = "/data_\d+/";
              $fields = [];//获取最大字段数
              foreach($temparr[0] as $k=>$v){
                 if(!preg_match($preg_attr,$v)){
                     return 'titleError';
                 }
                preg_match($name_preg, $v,$a);//匹配name的值
                $c = str_replace('data_'," ", $a[0]);
                $fields[]=$c;
              }
              rsort($fields);
        }
        $this->form_config_validation($request);
        $result = $request->except(['_url']);
        $result['staff_sn'] = session('admin')['staff_sn'];
        if (isset($html)) {
            $result['template'] = $html;
            $result['fields'] = $fields[0];//子段数
        }
        if (empty($request['id'])) {
            $result['create_time'] = time();
        }
        $result['form_name'] = trim($result['form_name']);
        $result['form_name'] = str_replace(' ', '', $result['form_name']);
        $url = config('api.url.workflow.formConfigSubmit');
        $data = Curl::setUrl($url)->sendMessageByPost($result);
        if ($data['result'] == 'success') {//添加成功
            return 'success';
        } elseif ($data['result'] == 'saveSuccess') {//编辑成功
            return 'saveSuccess';
        } elseif ($data['result'] == 'excelError') {//流程正在使用不允许导入
            return 'excelError';
        }
    }

    /**
     * 表单设置修改
     * @param Request $request
     */
    public function formConfigSave(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.formConfigSave');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        $classify_url = config('api.url.workflow.formConfigCreateFormClassifyList');
        $classify = Curl::setUrl($classify_url)->sendMessageByPost([]); //获取分类列表数据
        $result = json_encode(['data' => $data, 'classify' => $classify]);
        return $result;
    }

    /**
     * 表单设置删除
     * @param Request $request
     */
    public function formConfigDelete(Request $request) {
        $request = $request->except(['_url']);
        if (!empty($request['id'])) {
            $url = config('api.url.workflow.formConfigDelete');
            $data = Curl::setUrl($url)->sendMessageByPost($request);
            if ($data['result'] == 'success') {
                return 'success';
            }
        }
    }

    /**
     * 表单设置流程模板导出
     * @param Request $request
     */
    public function formConfigExcelBlade(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.formConfigExcelBlade');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        $folder = './template_html';
        $this->delFile($folder); //删除文件夹下的所有文件
        if (!file_exists($folder)) {//创建文件夹
            mkdir($folder);
        }
        $file_name = $folder . '/' . $data['form_name'] . '.html';
        $file = fopen($file_name, "w"); //创建文件
        fwrite($file, $data['template']); //写入内容
        fclose($file); //关闭
        return response()->download($file_name);
    }

    /**
     * 表单设置验证名称是否重复
     * @param Request $request
     */
    public function formConfigValidateName(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.formConfigValidateName');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     * 表单设置提交验证
     */
    private function form_config_validation($request) {
        $this->validate($request, [
            'form_name' => 'required|string|max:30',
            'form_describe' => 'string|max:255',
            'form_classify_id' => 'required|max:11',
            'form_classify_department_id' => 'required|integer',
            'sort' => 'string|max:5',
                ]
        );
    }

    /* 表单设置流程模板导出时
     * 删除指定目录下的文件，不删除目录文件夹 */

    private function delFile($dirName) {
        if (file_exists($dirName) && $handle = opendir($dirName)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (file_exists($dirName . '/' . $item) && is_dir($dirName . '/' . $item)) {
                        delFile($dirName . '/' . $item);
                    } else {
                        if (unlink($dirName . '/' . $item)) {
                            return true;
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

}
