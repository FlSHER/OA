<?php

namespace App\Http\Controllers\Api\Resources;

use Validator;
use App\Models\HR\Staff;
use Illuminate\Http\Request;
use App\Services\StaffService;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;
use App\Http\Resources\CurrentUserResource;
use App\Services\Workflow\Process\StaffEntry;

class WorkflowController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * 🐟员工入职流程.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function entry(Request $request)
    {
        $data = $request->input('data', []);
    	$service = new StaffEntry();
        $params = $service->makeFillData($data);
        $validator = $service->validator($params);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status' => 0, 'msg' => $errors->toJson()], 422);
        }
        if ($request->type === 'finish') {

            $result = $this->staffService->create($params);
            if ($result['status'] === 1) {
                return response()->json(['status' => 1, 'msg' => '操作成功']);
            }
            return response()->json(['status' => 0, 'msg' => '操作失败'], 500);
        }

        return response()->json(['status' => 1, 'msg' => '验证成功']);
    }

}