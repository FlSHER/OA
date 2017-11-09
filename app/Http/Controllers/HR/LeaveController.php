<?php

namespace App\Http\Controllers\HR;

use App\Models\HR\Attendance\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LeaveController extends Controller
{

    protected $model = 'App\Models\HR\Attendance\LeaveRequest';

    public function showManagePage()
    {
        return view('hr.attendance.leave');
    }

    public function getList(Request $request)
    {
        return app('Plugin')->dataTables($request, $this->model);
    }

    public function getInfo(Request $request)
    {
        $id = $request->id;
        $model = $this->model;
        return $model::find($id);
    }

    /**
     * 获取某一员工的待执行请假条
     * @param Request $request
     */
    public function getByPerson(Request $request)
    {
        $staffSn = $request->staff_sn;
        $leaves = LeaveRequest::where('staff_sn', $staffSn)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('clock_out_at')->orWhereNull('clock_in_at');
            })->get();
        return $leaves;
    }
}
