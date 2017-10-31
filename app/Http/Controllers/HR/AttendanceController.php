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

    public function getList(Request $request, $export = false)
    {
        $model = Attendance::visible()->where('status', '<>', 0);
        if ($export) {
            $model->with('details.staff.position', 'details.staff.department');
        }
        return app('Plugin')->dataTables($request, $model);
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
        $listData = $this->getList($request, true)['data'];
        $exportData = array_collapse(
            array_map(function ($item) {
                $response = [];
                foreach ($item['details'] as $detail) {
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

}
