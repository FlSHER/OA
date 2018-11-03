<?php

namespace App\Http\Controllers\Api\Dingtalk;

use App\Models\Dingtalk\Todo;
use App\Models\HR\Staff;
use App\Services\Dingtalk\todo\TodoService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    protected $todo;
    protected $response;

    /**
     * @return mixed
     */
    public function __construct(TodoService $todo, ResponseService $responseService)
    {
        $this->todo = $todo;
        $this->response = $responseService;
    }

    /**
     * 发起待办
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request)
    {
        $response = $this->todo->sendAddTodo($request);
        return $this->response->post($response);
    }

    /**
     * 更新待办
     * @param Request $request
     */
    public function update(Request $request)
    {
        $response = $this->todo->sendUpdateTodo($request);
        return $this->response->post($response);
    }

    /**
     * 获取待办消息列表
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $data = $this->todo->index();
        return $this->response->get($data);
    }

    /**
     * 重发失败的待办通知
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function retrace(Request $request,$id)
    {
        $data = $this->todo->retraceTodo($id);
        return $this->response->post($data);
    }
}
