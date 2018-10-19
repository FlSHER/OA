<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/18/018
 * Time: 9:22
 */

namespace App\Services\Dingtalk\todo;

use App\Jobs\Dingtalk\SendTodo;
use App\Models\Dingtalk\Todo;
use Curl;
use Illuminate\Support\Facades\Auth;

class TodoService
{
    public function addTodo($data)
    {
        try {
            SendTodo::dispatch($this->sendAddTodo($data));
            return 1;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 发送钉钉待办事项
     * @param $data
     */
    public function sendAddTodo(array $data)
    {
        //待办数据存入数据库
        $todo = $this->makeTodoDataToDatabase($data);
        $url = config('dingding.todo.add') . '?access_token=' . app('Dingtalk')->getAccessToken();
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        //发送信息结果存入数据库
        return $this->todoResultToDatabase($todo,$result);
    }

    /**
     *待办数据存入数据库
     * @param array $data
     * @return mixed
     */
    protected function makeTodoDataToDatabase(array $data)
    {
        $createStaff = Auth::id() ?? 0;
        $createRealname = Auth::user() ? Auth::user()->realname : '系统';
        $todoStaff = request()->has('userid') ? request()->get('userid') : 0;
        $stepRunId = request()->has('step_run_id') ? request()->get('step_run_id') : 0;
        $data['create_staff'] = $createStaff;
        $data['create_realname'] = $createRealname;
        $data['todo_staff'] = $todoStaff;
        $data['todo_userid'] = $data['userid'];
        $data['form_item_list'] = $data['formItemList'];
        $data['data'] = $data;
        $data['step_run_id'] = $stepRunId;
        $todo = Todo::create($data);
        return $todo;
    }

    /**
     * 发送信息结果存入数据库
     * @param $todo
     * @param $result
     * @return mixed
     */
    protected function todoResultToDatabase($todo,$result)
    {
        $todo->errcode = $result['errcode'];
        $todo->errmsg = array_has($result,'errmsg')?$result['errmsg']:null;
        $todo->record_id = array_has($result,'record_id')?$result['record_id']:null;
        $todo->request_id = $result['request_id'];
        $todo->save();
        return $todo;
    }

}