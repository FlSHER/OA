<?php

namespace App\Http\Controllers\HR;

use App\Models\App;
use App\Models\HR\Attendance\Attendance;
use App\Models\HR\Attendance\Clock;
use App\Models\HR\Attendance\LeaveRequest;
use App\Models\HR\Attendance\StaffTransfer;
use App\Models\HR\Attendance\WorkingSchedule;
use App\Models\HR\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use DB;
use Curl;
use Artisan;

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
        $startAt = $info['attendance_date'] . ' 04:00:00';
        $endAt = date('Y-m-d H:i:s', strtotime($startAt) + 24 * 3600);
        $ym = date('Ym', strtotime($info['attendance_date']));
        foreach ($info['details'] as $k => $detail) {
            $clockModel = new Clock(['ym' => $ym]);
            $clock = $clockModel->where('staff_sn', $detail['staff_sn'])
                ->where('is_abandoned', 0)
                ->where('clock_at', '>', $startAt)
                ->where('clock_at', '<=', $endAt)
                ->orderBy('clock_at', 'asc')
                ->get()->toArray();
            $info['details'][$k]['clocks'] = $clock;
        }
        return view('hr.attendance.attendance_info')->with($info);
    }

    public function getList(Request $request)
    {
        $idGroup = [];

        if (array_has($request->filter, 'staff_sn.is')) {
            DB::connection('attendance')->table('information_schema.TABLES')
                ->where('table_name', 'like', 'attendance_staff_%')
                ->get()->each(function ($model) use (&$idGroup, $request) {
                    $idTmpGroup = DB::connection('attendance')->table($model->TABLE_NAME)
                        ->where('staff_sn', $request->filter['staff_sn.is'])
                        ->get()->pluck('attendance_shop_id')->toArray();
                    $idGroup = array_collapse([$idGroup, $idTmpGroup]);
                });
            $value = array_except($request->filter, ['staff_sn.is']);
            $request->offsetSet('filter', $value);
        }

        $model = Attendance::visible()->when(!empty($idGroup), function ($query) use ($idGroup) {
            return $query->whereIn('id', $idGroup);
        })->where('status', '<>', 0);
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

    /**
     * 通过
     * @param Request $request
     * @return array
     */
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

    /**
     * 驳回
     * @param Request $request
     * @return array
     */
    public function reject(Request $request)
    {
        $id = $request->id;
        $auditorRemark = $request->auditor_remark;
        $attendance = Attendance::find($id);
        if ($attendance->status == 1) {
            $attendance->status = -1;
            $attendance->auditor_sn = app('CurrentUser')->staff_sn;
            $attendance->auditor_name = app('CurrentUser')->realname;
            $attendance->audited_at = date('Y-m-d');
            $attendance->auditor_remark = $auditorRemark;
            $attendance->save();
            return ['state' => 1, 'message' => '驳回成功'];
        } else {
            return ['state' => -1, 'message' => '该考勤表已被受理'];
        }
    }

    /**
     * 通过后撤回
     * @param Request $request
     * @return array
     */
    public function revert(Request $request)
    {
        $id = $request->id;
        $attendance = Attendance::find($id);
        if ($attendance->status == 2) {
            $attendance->status = 1;
            $attendance->auditor_sn = app('CurrentUser')->staff_sn;
            $attendance->auditor_name = app('CurrentUser')->realname;
            $attendance->audited_at = date('Y-m-d');
            $attendance->save();
            return ['state' => 1, 'message' => '反审核成功'];
        } else {
            return ['state' => -1, 'message' => '该考勤表未通过审核'];
        }
    }

    /**
     * 刷新考勤时间
     * @param Request $request
     * @return array
     */
    public function refresh(Request $request)
    {
        $id = $request->id;
        $attendanceAppHost = App::find(5)->url;
        $response = Curl::setUrl($attendanceAppHost . '/api/attendance/refresh')->sendMessageByPost(['id' => $id]);
        if ($response['status'] == 1) {
            return ['state' => 1, 'message' => $response['message']];
        } elseif ($response['status'] != 1) {
            return ['state' => -1, 'message' => $response['message']];
        } else {
            return ['state' => -1, 'message' => '刷新失败，未知原因'];
        }
    }

    /**
     * 导出员工考勤数据
     */
    public function exportStaffData(Request $request)
    {
        $exportData = [];
        $where = [];
        $whereRaw = '';
        if (!empty($request->filter)) {
            foreach ($request->filter as $filter => $value) {
                list($column, $type) = explode('.', $filter);
                if ($type == 'in') {
                    $whereRaw .= $column . ' in (' . $value . ')';
                } else {
                    $subWhere = [$column];
                    switch ($type) {
                        case 'is':
                            $subWhere[] = '=';
                            break;
                        case 'min':
                            $subWhere[] = '>=';
                            break;
                        case 'max':
                            $subWhere[] = '<=';
                            break;
                    }
                    $subWhere[] = $value;
                    $where[] = $subWhere;
                }

            }
        }
        DB::connection('attendance')->table('information_schema.TABLES')
            ->where('table_name', 'like', 'attendance_staff_%')
            ->get()->each(function ($model) use ($request, &$exportData, $where, $whereRaw) {
                $tableName = $model->TABLE_NAME;
                $columns = 'a.id AS "流水号", 
	a.attendance_shop_id AS "考勤表ID",
	a.shop_sn AS "店铺代码",
	a.shop_name AS "店铺名称",
	a.staff_sn AS "员工编号",
	a.staff_name AS "员工姓名",
	a.staff_position AS "职位",
	a.staff_department AS "部门",
	a.working_days AS "工作时长",
	a.leaving_days AS "请假时长",
	a.transferring_days AS "调动时长",
	a.working_days + a.transferring_days AS "有效时长",
	a.working_days + a.transferring_days + a.leaving_days AS "总时长",
	shop_duty.`name` AS "当日职务",
	a.sales_performance_lisha AS "利鲨货品",
	a.sales_performance_go AS "GO货品",
	a.sales_performance_group AS "公司货品",
	a.sales_performance_partner AS "合作方货品",
	a.sales_performance_lisha + a.sales_performance_go + a.sales_performance_group + a.sales_performance_partner AS "总业绩",
    IF (a.is_missing, "是", "否") AS "漏签",
    late_time AS "迟到时长",
    early_out_time AS "早退时长",
    IF (a.is_leaving, "是", "否") AS "请假",
    IF (a.is_transferring, "是", "否") AS "调动",
    IF (a.is_assistor, "是", "否") AS "协助",
    IF (a.is_shift, "是", "否") AS "倒班",
    a.attendance_date AS "考勤日期",
    CASE a.`status` WHEN 0 THEN	"未提交" WHEN 1 THEN "待审核" WHEN 2 THEN "已通过" WHEN -1 THEN "已驳回" END AS "审核状态",
    a.auditor_name AS "审核人"';
                $response = DB::connection('attendance')
                    ->table($tableName . ' AS a')
                    ->select(DB::raw($columns))
                    ->leftJoin('shop_duty', 'a.shop_duty_id', '=', 'shop_duty.id')
                    ->where($where)
                    ->when(!empty($whereRaw), function ($query) use ($whereRaw) {
                        return $query->whereRaw($whereRaw);
                    })
                    ->get()->toArray();
                $exportData = array_collapse([$exportData, $response]);
            });

        $exportData = array_prepend($exportData, array_keys((array)$exportData[0]));
        $rowNumber = 0;
        $file = 'hr/attendance/export/考勤数据-' . date('YmdHis') . '-' . app('CurrentUser')->staff_sn;
        $phpExcel = new \PHPExcel();
        $phpExcel->setActiveSheetIndex(0);
        foreach ($exportData as $row) {
            $rowNumber++;
            $preColNumber = '';
            $colNumber = 'A';
            foreach ((array)$row as $cell) {
                $phpExcel->getActiveSheet()->setCellValue($preColNumber . $colNumber . $rowNumber, $cell);
                $ord = ord($colNumber);
                if ($ord == 90) {
                    $preColNumber = 'A';
                    $colNumber = 'A';
                } else {
                    $colNumber = chr($ord + 1);
                }

            }
        }
        $phpWriter = new \PHPExcel_Writer_Excel2007($phpExcel);
        $phpWriter->save(storage_path('exports/' . $file) . '.xlsx');
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
        $validator = [
            'date' => ['required'],
            'staff_sn' => ['required'],
            'shop_sn' => [],
            'combine_type' => ['required'],
            'clock_at' => ['required', 'regex:/^\d{2}:\d{2}$/'],
        ];
        if (empty($request->combine_type) || substr($request->combine_type, 0, 1) != 2) {
            $validator['shop_sn'][] = 'required';
        }
        $this->validate($request, $validator);
        $clockData = [
            'staff_sn' => $request->staff_sn,
            'shop_sn' => $request->shop_sn,
            'attendance_type' => substr($request->combine_type, 0, 1),
            'type' => substr($request->combine_type, 1),
            'clock_at' => $request->date . ' ' . $request->clock_at,
            'operator_sn' => app('CurrentUser')->staff_sn,
        ];
        if (!empty($request->shop_sn)) {
            $workingScheduleModel = new WorkingSchedule(['ymd' => date('Ymd', strtotime($request->date))]);
            $workingSchedule = $workingScheduleModel->where(['staff_sn' => $request->staff_sn, 'shop_sn' => $request->shop_sn])->first();
            if (empty($workingSchedule)) {
                return ['status' => -1, 'message' => '对应排班表不存在'];
            }
        }
        if ($clockData['attendance_type'] == 1) {
            $punctualTime = $clockData['type'] == 1 ? $workingSchedule->clock_in : $workingSchedule->clock_out;
            if (empty($punctualTime)) {
                $shop = Shop::where('shop_sn', $request->shop_sn)->first();
                $punctualTime = $clockData['type'] == 1 ? $shop->clock_in : $shop->clock_out;
            }
            $clockData['punctual_time'] = $request->date . ' ' . $punctualTime;
        } elseif ($clockData['attendance_type'] == 2) {
            $clockData['parent_id'] = $request->transfer;
            $transfer = StaffTransfer::find($request->transfer);
            if ($clockData['type'] == 2) {
                $transfer->left_at = $clockData['clock_at'];
                $transfer->status = empty($transfer->arrived_at) ? 1 : 2;
            } elseif ($clockData['type'] == 1) {
                $transfer->arrived_at = $clockData['clock_at'];
                $transfer->status = 2;
            }
            $transfer->save();
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
            $clockData['punctual_time'] = $punctualTime;
        }
        $ym = date('Ym', strtotime($request->date));
        $clockModel = new Clock(['ym' => $ym]);
        $clockTable = $clockModel->getTable();
        $clockModel->fill($clockData)->save();
        $clockId = $clockModel->id;
        $clockData['clock_table'] = $clockTable;
        $clockData['clock_id'] = $clockId;
        DB::connection('attendance')->table('clock_patch')->insert($clockData);
        return ['status' => 1, 'message' => '补签成功'];
    }

    public function makeAttendance(Request $request)
    {
        $date = $request->date;
        $shop = Shop::where('shop_sn', $request->shop_sn)->first()->toArray();
        $attendanceAppHost = App::find(5)->url;
        return Curl::setUrl($attendanceAppHost . '/api/attendance/make')
            ->sendMessageByPost(['date' => $date, 'shop' => $shop, 'user' => app('CurrentUser')->getInfo()]);
    }

    public function syncSalesPerformance()
    {
        Artisan::call('attendance:getSalePerformance');
        return ['status' => 1, 'message' => '同步成功'];
    }
}
