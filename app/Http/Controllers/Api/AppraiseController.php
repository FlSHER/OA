<?php

namespace App\Http\Controllers\Api;

use App\Models\HR\Appraise;
use App\Models\HR\Staff;
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
            return ['status' => 'error', 'message' => $validator->errors()->all()];
        }

        $staff = Staff::find($request->staff_sn);
        $data = $request->only(['staff_sn', 'remark']);
        $data['entry_staff_sn'] = app('CurrentUser')->staff_sn;
        $data['entry_name'] = app('CurrentUser')->realname;
        $data['position'] = $staff->position->name;
        $data['department'] = $staff->department->name;
        $data['shop'] = $staff->shop ? $staff->shop->name : '';
        Appraise::insert($data);
        return ['status' => 'success'];
    }

    /**
     * 当前员工的评价列表
     * @param Request $request
     */
    public function appraiseList(Request $request)
    {
        $page = $this->getPage($request);
        $current_user = app('CurrentUser')->staff_sn;
        $data = Appraise::with('staff')->where('entry_staff_sn', $current_user)->skip($page['start'])->take($page['length'])->orderBy('create_time','desc')->get();
        return ['status' => 'success', 'response' => $data,'page'=>$page];
    }

    private function getPage($request)
    {
        $current_user = app('CurrentUser')->staff_sn;
        $length = 10;
        $start = 0;
        if (isset($request->length) && is_numeric($request->length)) {
            $length = intval($request->length);
        }
        if (isset($request->start) && is_numeric($request->start)) {
            $start = intval($request->start);
        }
        $total = Appraise::where('entry_staff_sn',$current_user)->count();//总条数
        $pages = ceil($total/$length);//总页数
        return ['start' => $start, 'length' => $length,'total'=>$total,'pages'=>$pages];
    }
}
