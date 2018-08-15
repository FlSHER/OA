<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13/013
 * Time: 16:53
 */

namespace App\Services\Dingtalk\Notification;


use App\Jobs\Dingtalk\SendMessage;

class Messages
{
    protected $jobNotificationMessage;

    public function __construct(JobNotificationMessage $jobNotificationMessage)
    {
        $this->jobNotificationMessage = $jobNotificationMessage;
    }

    /**
     * 发送工作通知消息
     * @param $data
     */
    public function sendJobNotificationMessage($data){
        try{
            SendMessage::dispatch($data,$this->jobNotificationMessage);
            return 1;
        }catch(\Exception $e){
            return 0;
        }
    }
}