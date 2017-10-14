<?php

namespace App\Http\Controllers\HR;

use App\Models\HR\Attendance\Attendance;
use App\Models\HR\Attendance\Clock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

class AttendanceController extends Controller
{

    protected $model = 'App\Models\HR\Attendance\Attendance';

    public function showManagePage()
    {
        return view('hr.attendance.attendance');
    }

    public function showDetailPage(Request $request)
    {
        $info = $this->getInfo($request)->toArray();
        $shopSn = $info['shop_sn'];
        $startAt = $info['attendance_date'] . ' 04:00:00';
        $endAt = date('Y-m-d H:i:s', strtotime($startAt) + 24 * 3600);
        foreach ($info['details'] as $k => $detail) {
            $clock = Clock::where('shop_sn', $shopSn)
                ->where('staff_sn', $detail['staff_sn'])
                ->where('is_abandoned', 0)
                ->where('clock_at', '>', $startAt)
                ->where('clock_at', '<=', $endAt)
                ->get()->toArray();
            $info['details'][$k]['clocks'] = $clock;
        }
        return view('hr.attendance.attendance_info')->with($info);
    }

    public function getList(Request $request)
    {
        return app('Plugin')->dataTables($request, Attendance::visible()->where('status', '<>', 0));
    }

    public function getInfo(Request $request)
    {
        $id = $request->id;
        $model = $this->model;
        return $model::with('details', 'shop')->find($id);
    }

    public function pass(Request $request)
    {
        $id = $request->id;
        $attendance = Attendance::find($id);
        if ($attendance->status == 1) {
            $attendance->status = 2;
            $attendance->auditor_sn = app('CurrentUser')->staff_sn;
            $attendance->auditor_name = app('CurrentUser')->realname;
            $attendance->audited_at = date('Y-m-d');
            $attendance->save();
            return ['state' => 1, 'message' => '审核成功'];
        } else {
            return ['state' => -1, 'message' => '该考勤表已被受理'];
        }
    }

    public function reject(Request $request)
    {
        $id = $request->id;
        $attendance = Attendance::find($id);
        if ($attendance->status == 1) {
            $attendance->status = -1;
            $attendance->auditor_sn = app('CurrentUser')->staff_sn;
            $attendance->auditor_name = app('CurrentUser')->realname;
            $attendance->audited_at = date('Y-m-d');
            $attendance->save();
            return ['state' => 1, 'message' => '驳回成功'];
        } else {
            return ['state' => -1, 'message' => '该考勤表已被受理'];
        }
    }

    /**
     * 导出员工考勤数据
     */
    public function exportStaffData(Request $request)
    {

    }

}
