<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13/013
 * Time: 15:46
 * 工作通知消息
 */

namespace App\Services\Dingtalk\Notification;

use App\Models\Dingtalk\Message;
use Curl;
use Illuminate\Support\Facades\Auth;

class JobNotificationMessage
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = app('Dingtalk')->getAccessToken();
    }

    /**
     * 发送工作通知消息
     */
    public function sendMessage($data)
    {
        $url = config('dingding.message.jobNotification') . '?access_token=' . $this->accessToken;
        $result = Curl::setUrl($url)->sendMessageByPost($data);
        $this->messageToDatabase($data,$result);
        if($result['errcode'] == 0)
            return 1;//发送成功
        return 0;//发送失败
    }

    /**
     * 消息存入数据库
     * @param array $request
     * @param array $result
     */
    protected function messageToDatabase(array $request,array $result){
        $data = $request;
        if(request()->has('oa_client_id'))
            $data['client_id'] = request()->get('oa_client_id');
        $data['create_staff'] = Auth::id() ?? '0';
        $data['create_realname'] = Auth::user() ? Auth::user()->realname : '系统';
        if(array_has($data,'to_all_user') && $data['to_all_user'])
            $data['to_all_user'] = 1;
        $data['to_all_user'] = 0;
        $data['msgtype'] = $data['msg']['msgtype'];
        $data['data'] = $data['msg'];
        try{
            Message::create(array_collapse([$data,$result]));
        }catch(\Exception $exception){
            //新增数据库失败
        }
    }
}