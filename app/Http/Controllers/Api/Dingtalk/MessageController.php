<?php

namespace App\Http\Controllers\Api\Dingtalk;

use App\Models\App;
use App\Models\HR\Staff;
use App\Services\Dingtalk\Notification\Messages;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    protected $message;//钉钉消息
    protected $response;

    public function __construct(Messages $messages,ResponseService $responseService)
    {
        $this->message = $messages;
        $this->response = $responseService;
    }

    /**
     * 发送钉钉工作通知消息
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function sendJobNotificationMessage(Request $request)
    {
        $data = $this->message->sendJobNotification($request);
        return $this->response->post($data);
    }

    /**
     * 工作通知列表消息
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $data = $this->message->index();
        return $this->response->get($data);
    }

    /**
     * 重发工作通知失败信息
     * @param $id
     */
    public function retrace($id){
        $data = $this->message->retraceJob($id);
        return $this->response->post($data);
    }
}
