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
use App\Models\HR\Staff;
use Curl;
use Illuminate\Support\Facades\Auth;

class TodoService
{
    /**
     * 发送待办
     * @param $request
     * @return int
     */
    public function sendAddTodo($request)
    {
        //待办通知数据存入数据库
        try{
            $data = $request->except(['step_run_id']);
            $data['userid'] = Staff::find($request->input('userid'))->dingtalk_number;
            //待办数据存入数据库
            dd($data);
            $todo = $this->makeTodoDataToDatabase($data);
        }catch(\Exception $e){
            return 0;
        }

        //发送待办通知
        try{
            SendTodo::dispatch($this->addTodo($data,$todo));
        }catch (\Exception $e){

        }
        return 1;
    }

    /**
     * 更新待办通知
     * @param $request
     * @return int
     */
    public function sendUpdateTodo($request)
    {
        try{
            $todoData = Todo::where('step_run_id',$request->input('step_run_id'))->select('record_id','todo_userid')->first();
            $data = [
                'userid' => $todoData->todo_userid,
                'record_id'=>$todoData->record_id,
            ];
        }catch(\Exception $e){
            return 0;
        }

        try {
            SendTodo::dispatch($this->updateTodo($data));
        } catch (\Exception $e) {

        }
        return 1;
    }

    /**
     * 发送钉钉待办事项
     * @param $data
     */
    public function addTodo(array $data,$todo)
    {
        $url = config('dingding.todo.add') . '?access_token=' . app('Dingtalk')->getAccessToken();
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        //发送信息结果存入数据库
        return $this->todoResultToDatabase($todo,$result);
    }

    /**
     * 更新待办
     * @param array $data
     */
    public function updateTodo(array $data)
    {
        $url = config('dingding.todo.update') . '?access_token=' . app('Dingtalk')->getAccessToken();
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        if($result['errcode'] == 0 && $result['result'] == true){
            //更新待办
            Todo::where('record_id',$data['record_id'])->update(['is_finish'=>1]);
        }
        return $result;
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