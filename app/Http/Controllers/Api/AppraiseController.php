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
            $errors = implode(array_values($validator->errors()),',');
            return ['status' => 'error', 'message' => $errors];
        }

        $data = $request->only(['staff_sn', 'remark']);
        $data['entry_staff_sn'] = app('CurrentUser')->staff_sn;
        $data['entry_name'] = app('CurrentUser')->realname;
        Appraise::insert($data);
        return ['status' => 'success'];
    }
}
