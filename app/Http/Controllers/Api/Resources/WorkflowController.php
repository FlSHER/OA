<?php

namespace App\Http\Controllers\Api\Resources;

use Validator;
use App\Models\HR\Staff;
use Illuminate\Http\Request;
use App\Services\StaffService;
use App\Services\RelationService;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;
use App\Http\Resources\CurrentUserResource;
use Illuminate\Support\Facades\Log;

class WorkflowController extends \App\Http\Controllers\Controller
{
    protected $staffService;
    protected $relationService;

    public function __construct(
    	StaffService $staffService, 
    	RelationService $relationService)
    {
        $this->staffService = $staffService;
        $this->relationService = $relationService;
    }

    /**
     * 🐟员工入职流程.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function entrant(Request $request)
    {
        $data = $request->input('data', []);
        $params = $this->relationService->makeFillStaffData($data);
        $validator = $this->entrantStaffValidator($params);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status' => 0, 'msg' => $errors->json()], 422);
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