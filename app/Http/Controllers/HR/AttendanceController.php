<?php

namespace App\Http\Controllers\HR;

use App\Models\HR\Attendance\AttendanceStaff;
use App\Models\HR\Attendance\Attendance;
use App\Models\HR\Attendance\Clock;
use App\Models\HR\Attendance\LeaveRequest;
use App\Models\HR\Attendance\WorkingSchedule;
use App\Models\HR\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use DB;

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
        $ym = date('Ym', strtotime($info['attendance_date']));
        foreach ($info['details'] as $k => $detail) {
            $clockModel = new Clock(['ym' => $ym]);
            $clock = $clockModel->where('shop_sn', $shopSn)
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
        $model = Attendance::visible()->where('status', '<>', 0);
        return app('Plugin')->dataTables($request, $model);
    }

    public function getInfo(Request $request)
    {
        $id = $request->id;
        $model = $this->model;
        $response = $model::with('shop')->find($id);
        $response->details;
        return $response;
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
        $listData = $this->getList($request)['data'];
        $exportData = array_collapse(
            array_map(function ($item) {
                $response = [];
                $ym = date('Ym', strtotime($item['attendance_date']));
                $model = new AttendanceStaff(['ym' => $ym]);
                $details = $model->where('attendance_shop_id', $item['id'])->get();
                $details->load('staff.position', 'staff.department', 'shop_duty');
                $details = $details->toArray();
                foreach ($details as $detail) {
                    $response[] = array_collapse([$detail, [
                        'shop_sn' => $item['shop_sn'],
                        'shop_name' => $item['shop_name'],
                        'sales_performance_total' => sprintf('%.2f', $detail['sales_performance_lisha'] +
                            $detail['sales_performance_go'] +
                            $detail['sales_performance_group'] +
                            $detail['sales_performance_partner']),
                        'status' => [1 => '待审核', 2 => '已通过', -1 => '已驳回'][$item['status']],
                        'auditor_name' => $item['auditor_name'],
                        'attendance_date' => $item['attendance_date'],
                        'is_missing' => $detail['is_missing'] ? '是' : '否',
                        'is_leaving' => $detail['is_leaving'] ? '是' : '否',
                        'is_transferring' => $detail['is_transferring'] ? '是' : '否',
                    ]]);
                }
                return $response;
            }, $listData)
        );
        $columns = [
            '流水号' => 'id',
            '考勤表ID' => 'attendance_shop_id',
            '店铺代码' => 'shop_sn',
            '店铺名称' => 'shop_name',
            '员工编号' => 'staff_sn',
            '员工姓名' => 'staff_name',
            '职位' => 'staff.position.name',
            '部门' => 'staff.department.name',
            '工作时长' => 'working_days',
            '请假时长' => 'leaving_days',
            '调动时长' => 'transferring_days',
            '当日职务' => 'shop_duty.name',
            '利鲨货品' => 'sales_performance_lisha',
            'GO货品' => 'sales_performance_go',
            '公司货品' => 'sales_performance_group',
            '合作方货品' => 'sales_performance_partner',
            '总业绩' => 'sales_performance_total',
            '是否漏签' => 'is_missing',
            '迟到时长' => 'late_time',
            '早退时长' => 'early_out_time',
            '是否有请假' => 'is_leaving',
            '是否有调动' => 'is_transferring',
            '考勤日期' => 'attendance_date',
            '审核状态' => 'status',
            '审核人' => 'auditor_name',
        ];
        $file = app('App\Contracts\ExcelExport')->setPath('hr/attendance/export/')->setBaseName('考勤数据')->setColumns($columns)->export(['sheet1' => $exportData]);
        return ['state' => 1, 'file_name' => $file];
    }

    function getClockRecords(Request $request)
    {
        $date = $request->date;
        $staffSn = $request->staff_sn;
        $startAt = $date . ' 04:00:00';
        $endAt = date('Y-m-d H:i:s', strtotime($date . ' 04:00:00 +1 day'));
        $ym = date('Ym', strtotime($date));
        $clockModel = new Clock(['ym' => $ym]);
        $clocks = $clockModel->where('staff_sn', $staffSn)
            ->where('clock_at', '>', $startAt)
            ->where('clock_at', '<=', $endAt)
            ->where('is_abandoned', 0)
            ->get();
        return view('hr/attendance/clock_records')->with(['clocks' => $clocks]);
    }

    function makeClockRecord(Request $request)
    {
        $this->validate($request, [
            'date' => ['required'],
            'staff_sn' => ['required'],
            'shop_sn' => ['required'],
            'combine_type' => ['required'],
            'clock_at' => ['required'],
        ]);
        $clockData = [
            'staff_sn' => $request->staff_sn,
            'shop_sn' => $request->shop_sn,
            'attendance_type' => substr($request->combine_type, 0, 1),
            'type' => substr($request->combine_type, 1),
            'clock_at' => $request->date . ' ' . $request->clock_at,
            'operator_sn' => app('CurrentUser')->staff_sn,
        ];
        if ($clockData['attendance_type'] == 1) {
            $workingScheduleModel = new WorkingSchedule(['ymd' => date('Ymd', strtotime($request->date))]);
            $workingSchedule = $workingScheduleModel->where(['staff_sn' => $request->staff_sn, 'shop_sn' => $request->shop_sn])->first();
            if (empty($workingSchedule)) {
                return ['status' => -1, 'message' => '对应排班表不存在'];
            }
            $punctualTime = $clockData['type'] == 1 ? $workingSchedule->clock_in : $workingSchedule->clock_out;
            if (empty($punctualTime)) {
                $shop = Shop::where('shop_sn', $request->shop_sn)->first();
                $punctualTime = $clockData['type'] == 1 ? $shop->clock_in : $shop->clock_out;
            }
        } elseif ($clockData['attendance_type'] == 3) {
            $clockData['parent_id'] = $request->leave_request;
            $leaveRequest = LeaveRequest::find($request->leave_request);
            if ($clockData['type'] == 2) {
                $punctualTime = $leaveRequest->start_at;
                $leaveRequest->clock_out_at = $clockData['clock_at'];
            } elseif ($clockData['type'] == 1) {
                $punctualTime = $leaveRequest->end_at;
                $leaveRequest->clock_in_at = $clockData['clock_at'];
            }
            $leaveRequest->save();
        }
        $clockData['punctual_time'] = $request->date . ' ' . $punctualTime;
        $ym = date('Ym', strtotime($request->date));
        $clockModel = new Clock(['ym' => $ym]);
        $clockTable = $clockModel->getTable();
        $clockId = $clockModel->insertGetId($clockData);
        $clockData['clock_table'] = $clockTable;
        $clockData['clock_id'] = $clockId;
        DB::connection('attendance')->table('clock_patch')->insert($clockData);
        return ['status' => 1, 'message' => '补签成功'];
    }

}
