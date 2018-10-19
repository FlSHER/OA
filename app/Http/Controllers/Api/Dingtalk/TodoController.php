<?php

namespace App\Http\Controllers\Api\Dingtalk;

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
        $data = $request->except(['step_run_id']);
        $data['userid'] = Staff::find($request->input('userid'))->dingding;
        $response = $this->todo->addTodo($data);
        return $this->response->post($response);
    }

    /**
     * 更新待办
     * @param Request $request
     */
    public function update(Request $request)
    {

    }
}
