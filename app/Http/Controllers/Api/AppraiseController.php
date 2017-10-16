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
     * 选中员工的所有评价数据
     * @param Request $request
     */
    public function selectedUserRemark(Request $request)
    {
        $start = ($request->pageIndex - 1) * $request->length;
        $data = Appraise::with('staff')->where('staff_sn', $request->searchName)->skip($start)->take($request->length)->orderBy('create_time', 'desc')->get();
        return ['status' => 'success', 'response' => $data];
    }

    /**
     * 当前员工的评价列表
     * @param Request $request
     */
    public function appraiseList(Request $request)
    {
        $page = $this->getPage($request);
        $current_user = app('CurrentUser')->staff_sn;
        $data = Appraise::with('staff')->where('entry_staff_sn', $current_user)->skip($page['start'])->take($page['length'])->orderBy('create_time', 'desc')->get();
        return ['status' => 'success', 'response' => $data, 'page' => $page];
    }

    private function getPage($request)
    {
        $current_user = app('CurrentUser')->staff_sn;
        $length = 10;
        $pageIndex = 1;//当前页
        if (isset($request->length) && is_numeric($request->length)) {
            $length = intval($request->length);
        }
        if (isset($request->pageIndex) && is_numeric($request->pageIndex)) {
            $pageIndex = intval($request->pageIndex);
        }
        $total = Appraise::where('entry_staff_sn', $current_user)->count();//总条数
        $pages = ceil($total / $length);//总页数
        $start = ($pageIndex - 1) * $length;
        return ['pageIndex' => $pageIndex, 'length' => $length, 'total' => $total, 'pages' => $pages, 'start' => $start];
    }

    /**
     * 删除
     * @param Request $request
     */
    public function delete(Request $request){
        Appraise::where(['id'=>$request->id,'entry_staff_sn'=>app('CurrentUser')->staff_sn])->delete();
        return ['status'=>'success','response'=>app('CurrentUser')->staff_sn];
    }
}
