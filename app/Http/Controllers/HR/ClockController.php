<?php

namespace App\Http\Controllers\HR;

use App\Models\HR\Attendance\Clock;
use App\Models\HR\Attendance\ClockLog;
use App\Models\HR\Attendance\LeaveRequest;
use App\Models\HR\Attendance\StaffTransfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClockController extends Controller
{
    protected $model = 'App\Models\HR\Attendance\Clock';

    public function showManagePage()
    {
        return view('hr.attendance.clock');
    }

    public function getList(Request $request)
    {
        $month = $request->has('clock_month') ? $request->clock_month : date('Y-m');
        $ym = app('AttendanceService')->getAttendanceDate('Ym', $month . '-5');
        $clockModel = new Clock(['ym' => $ym]);
        return app('Plugin')->dataTables($request, $clockModel);
    }

    public function getInfo(Request $request)
    {
        $month = $request->has('month') ? $request->month : date('Y-m');
        $ym = app('AttendanceService')->getAttendanceDate('Ym', $month . '-5');
        $clockModel = new Clock(['ym' => $ym]);
        $clock = $clockModel->find($request->id);
        $clock->setAttribute('combine_type', $clock->attendance_type . $clock->type);
        $clock->setAttribute('ym', $ym);
        $clock->staff;
        $clock->operator;
        return $clock;
    }

    public function editByOne(Request $request)
    {
        $this->validate($request, ['shop_sn' => 'exists:shops,shop_sn,deleted_at,NULL']);
        $id = $request->id;
        $ym = $request->ym;
        $shopSn = strtolower($request->shop_sn);
        ClockLog::create([
            'clock_id' => $id,
            'ym' => $ym,
            'action' => 3,
            'operator_sn' => app('CurrentUser')->staff_sn,
        ]);
        $clockModel = new Clock(['ym' => $ym]);
        $clock = $clockModel->find($id);
        $clock->setMonth($ym)->fill(['shop_sn' => $shopSn])->save();
        return ['status' => 1, 'message' => '编辑成功'];
    }

    public function abandon(Request $request)
    {
        $id = $request->id;
        $ym = str_replace('-', '', $request->month);
        $clockModel = new Clock(['ym' => $ym]);
        $clockRecord = $clockModel->find($id);

        ClockLog::create([
            'clock_id' => $id,
            'ym' => $ym,
            'action' => -1,
            'operator_sn' => app('CurrentUser')->staff_sn,
        ]);

        $attendanceType = $clockRecord->attendance_type;
        $type = $clockRecord->type;
        $parentId = $clockRecord->parent_id;
        if ($attendanceType == 2) {
            $transfer = StaffTransfer::find($parentId);
            if ($type == 2) {
                $transfer->left_at = null;
                if (empty($transfer->arrived_at)) {
                    $transfer->status = 0;
                }
            } elseif ($type == 1) {
                $transfer->arrived_at = null;
                if (empty($transfer->left_at)) {
                    $transfer->status = 0;
                } else {
                    $transfer->status = 1;
                }
            }
            $transfer->save();
        } elseif ($attendanceType == 3) {
            $leave = LeaveRequest::find($parentId);
            if ($type == 2) {
                $leave->clock_out_at = null;
            } elseif ($type == 1) {
                $leave->clock_in_at = null;
            }
            $leave->save();
        }
        $clockModel->where(['id' => $id])->update(['is_abandoned' => 1]);
        return ['status' => 1, 'message' => '作废成功'];
    }
}
