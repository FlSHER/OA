<?php

namespace App\Http\Controllers\app\WorkFlow;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Curl;
use DB;
use Illuminate\Support\Facades\Validator;

class DatabaseManageController extends Controller {

    /**
     * 管理设置列表
     */
    public function databaseManage() {
        $url = config('api.url.workflow.databaseManageList');
        $data = Curl::setUrl($url)->sendMessageByPost([]);
        return view('workflow.database_manage')->with(['data' => $data]);
    }

    /**
     * 编辑获取数据
     * @param Request $request
     */
    public function databaseManageUpdateBefore(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.databaseManageUpdateBefore');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     * 数据配置保存
     * @param Request $request
     */
    public function databaseManageAdd(Request $request) {

        $data = $request->except(['_url']);
        $validator = $this->validatorData($request); //验证表单
        if ($validator) {
            return ['result' => 'checkError', 'msg' => $validator];
        }
        $url = config('api.url.workflow.databaseManageAdd');
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        return $result;
    }

    /**
     * 数据配置删除
     * @param Request $request
     */
    public function databaseManageDelete(Request $request) {
        $request = $request->except(['_url']);
        $url = config('api.url.workflow.databaseManageDelete');
        $data = Curl::setUrl($url)->sendMessageByPost($request);
        return $data;
    }

    /**
     * 数据库连接测试
     * @param Request $request
     */
    public function databaseTestCheck(Request $request) {
        $data = $request->except(['_url']);
//        $mysql = new \PDO('mysql:host=192.168.1.63:3306;dbname=workflow', 'liuyong', 'liuyong');//mysql
//        $mysql = mysqli_connect('192.168.1.6', 'root', 'root', 'weixin', '3306');//mysql
//        $sqlsrv = new \PDO('odbc:Driver={SQL Server};Server=125.64.17.214;Database=firstframe215', 'sa', 'hjl123456'); //sqlsrv
        if ($data['connection'] == 'mysql') {
            $mysql = new \PDO($data['connection'] . ':host=' . $data['host'] . ':' . $data['port'] . ';dbname=' . $data['database'], $data['username'], $data['password']);
            return 'success';
        } elseif ($data['connection'] == 'sqlsrv') {
            $sqlsrv = new \PDO('odbc:Driver={SQL Server};Server='.$data['host'].';Database='.$data['database'].'', $data['username'], $data['password']); //sqlsrv
            return 'success';
        } 
//        elseif ($data['connection'] == 'oracle') {
//            dd('请配置oracle数据库');
//        }
        return 'error';
    }

    /**
     * 验证表单
     * @param type $request
     */
    private function validatorData($request) {
        $validator = Validator::make($request->all(), [
                    'database' => 'required|string|max:20',
                    'connection' => 'required|string|max:20',
                    'host' => 'required|max:30|ip',
                    'port' => 'numeric|max:65536',
                    'username' => 'required|string|max:20',
                    'password' => 'string|max:30',
                        ], [], trans('fields.database_manage'));
        if ($validator->fails()) {
            return $validator->errors()->all();
        }
    }

}
