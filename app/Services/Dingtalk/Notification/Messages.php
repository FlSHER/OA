<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13/013
 * Time: 16:53
 */

namespace App\Services\Dingtalk\Notification;


use App\Models\App;
use App\Models\Dingtalk\Message;
use App\Models\HR\Staff;
use Curl;
use Illuminate\Support\Facades\Auth;

class Messages
{
    /**
     * 发送工作通知消息
     * @param $data
     */
    public function sendJobNotification($request)
    {
        try {
            $agentId = App::find($request->input('oa_client_id'))->agent_id;
            $data = $request->except('oa_client_id', 'step_run_id');
            $dingUserId = Staff::find($data['userid_list'])->pluck('dingtalk_number')->all();
            $data['agent_id'] = $agentId;
            $data['userid_list'] = implode(',', $dingUserId);
            $message = $this->messageToDatabase($data);
        } catch (\Exception $e) {
            return 0;
        }
        $this->sendJobNotificationMessage($data, $message);
        return 1;
    }

    /**
     * 发送工作通知消息
     */
    protected function sendJobNotificationMessage(array $data, $message)
    {
        $url = config('dingding.message.jobNotification') . '?access_token=' . app('Dingtalk')->getAccessToken();
        try {
            $result = Curl::setUrl($url)->sendMessageByPost($data);
            $message->errcode = $result['errcode'];
            $message->task_id = array_has($result, 'task_id') ? $result['task_id'] : null;
            $message->request_id = array_has($result, 'request_id') ? $result['request_id'] : null;
            $message->save();
            return $message;
        } catch (\Exception $e) {

        }
    }

    /**
     * 消息存入数据库
     * @param array $request
     * @param array $result
     */
    protected function messageToDatabase(array $data)
    {
        if (request()->has('oa_client_id'))
            $messageData['client_id'] = request()->get('oa_client_id');
        $messageData['agent_id'] = $data['agent_id'];
        $messageData['create_staff'] = Auth::id() ?? '0';
        $messageData['create_realname'] = Auth::user() ? Auth::user()->realname : '系统';
        $messageData['msgtype'] = $data['msg']['msgtype'];
        $messageData['data'] = $data;
        if (request()->has('step_run_id'))
            $messageData['step_run_id'] = request()->get('step_run_id');
        $message = Message::create($messageData);
        return $message;
    }

    /**
     * 工作通知列表
     * @return mixed
     */
    public function index()
    {
        $clientId = request()->has('client_id') ? request('client_id') : false;
        $data = Message::when($clientId, function ($query) use ($clientId) {
            return $query->where('client_id', $clientId);
        })
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
        return $data;
    }

    /**
     * 重发失败的工作通知信息
     * @param $id
     * @return mixed
     */
    public function retraceJob($id)
    {
        $data = Message::where('id', $id)
            ->where(function ($query) {
                $query->where('errcode', '<>', 0)
                    ->orWhereNull('errcode');
            })
            ->firstOrFail();
        $response = $this->sendJobNotificationMessage($data->data, $data);
        return $response;
    }
}