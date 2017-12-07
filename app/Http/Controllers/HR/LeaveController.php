<?php

namespace App\Http\Controllers\HR;

use App\Models\HR\Attendance\Clock;
use App\Models\HR\Attendance\LeaveLog;
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

    public function export(Request $request)
    {
        $exportData = $this->getList($request)['data'];
        foreach ($request->columns as $v) {
            if (empty($v['name'])) {
                $columns[$v['data']] = $v['data'];
            } else {
                $columns[$v['name']] = $v['data'];
            }
        }
        $file = app('App\Contracts\ExcelExport')->setPath('hr/leave/export/')
            ->setBaseName('请假条')->setColumns($columns)->trans('fields.leave')
            ->export(['sheet1' => $exportData]);
        return ['state' => 1, 'file_name' => $file];
    }

    /**
     * 撤销假条
     * @param Request $request
     * @return array
     */
    public function cancel(Request $request)
    {
        $id = $request->id;
        $leaveRequest = LeaveRequest::find($id);
        $leaveRequest->status = -2;

        if (!empty($leaveRequest->clock_out_at)) {
            $ymStart = app('AttendanceService')->getAttendanceDate('Ym', $leaveRequest->clock_out_at);
            $clockModelStart = new Clock(['ym' => $ymStart]);
            $clockModelStart->where(['parent_id' => $id, 'attendance_type' => 3])->delete();
        }
        if (!empty($leaveRequest->clock_in_at)) {
            $ymEnd = app('AttendanceService')->getAttendanceDate('Ym', $leaveRequest->clock_in_at);
            $clockModelEnd = new Clock(['ym' => $ymEnd]);
            $clockModelEnd->where(['parent_id' => $id, 'attendance_type' => 3])->delete();
        }

        LeaveLog::create(['leave_request_id' => $id, 'action' => -1, 'operator_sn' => app('CurrentUser')->staff_sn]);
        $leaveRequest->save();
        return ['status' => 1, 'message' => '撤销成功'];
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
