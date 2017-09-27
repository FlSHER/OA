<?php

namespace App\Http\Controllers\Api;

use App\Models\HR\Appraise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class AppraiseController extends Controller
{
    /**
     * 评价提交处理
     * @param Request $request
     */
    public function appraiseFromSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_sn' => 'required|exists:staff,staff_sn',
            'remark' => 'required|string',
        ], [], trans('fields.appraise'));

        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()];
//            return app('ApiResponse')->makeErrorResponse('error', 500);
        }

        $appraise = new Appraise();
        $appraise->create($request);
        $appraise->entry_staff_sn = app('CurrentUser')->staff_sn;
        $appraise->entry_name = app('CurrentUser')->realname;
        $appraise->save();
        return ['status' => 'success'];
//        return app('ApiResponse')->makeSuccessResponse('success', 200);
    }
}
