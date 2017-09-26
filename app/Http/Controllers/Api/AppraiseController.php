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
            return ['status'=>'error','response'=>$validator->errors()];
//            return app('ApiResponse')->makeErrorResponse(['msg' => 'error', 'result' => $validator->errors()], 500);
        }
        $data = $request->except(['_url']);
        $data['entry_staff_sn'] = app('CurrentUser')->staff_sn;
        $data['entry_name'] = app('CurrentUser')->realname;
        Appraise::insert($data);
        return ['status'=>'success'];
//        return app('ApiResponse')->makeSuccessResponse(['msg'=>'success'], 200);
    }
}
