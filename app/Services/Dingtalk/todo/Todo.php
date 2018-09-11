<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/18/018
 * Time: 9:22
 */

namespace App\Services\Dingtalk\todo;

use App\Jobs\Dingtalk\SendTodo;
use Curl;
use Illuminate\Support\Facades\Auth;

class Todo
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
    public function sendAddTodo($data)
    {
        $url = config('dingding.todo.add') . '?access_token=' . app('Dingtalk')->getAccessToken();
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        $todoData = $this->addTodoToDatabase($data, $result);
        return $todoData;
    }

    /**
     * 待办事项数据添加到数据库
     * @param $data
     * @param $result
     */
    protected function addTodoToDatabase($data, $result)
    {
        $newData = $data;
        $createStaff = Auth::id() ?? 0;
        $createRealname = Auth::user() ? Auth::user()->realname : '系统';
        $todoStaff = request()->has('userid') ? request()->get('userid') : 0;
        $stepRunId = request()->has('step_run_id') ? request()->get('step_run_id') : 0;
        $callback = request()->has('callback') ? request()->get('callback') : null;
        $newData['create_staff'] = $createStaff;
        $newData['create_realname'] = $createRealname;
        $newData['todo_staff'] = $todoStaff;
        $newData['todo_userid'] = $newData['userid'];
        $newData['form_item_list'] = $newData['formItemList'];
        $newData['data'] = $data;
        $newData['step_run_id'] = $stepRunId;
        $newData['callback'] = $callback;
        try {
            $todoData = \App\Models\Dingtalk\Todo::create(array_collapse([$newData, $result]));
        } catch (\Exception $exception) {
            $todoData = [];
        }
        return $todoData;
    }
}