<?php

namespace App\Http\Controllers\HR;

use App\Contracts\CURD;
use App\Models\HR\Attendance\WorkingSchedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkingScheduleController extends Controller
{

    protected $transPath = 'fields.working_schedule';
    protected $curdService;

    public function __construct(CURD $curdContract)
    {
        $this->curdService = $curdContract;
    }

    public function showManagePage()
    {
        return view('hr.attendance.working_schedule');
    }

    public function getList(Request $request)
    {
        return app('Plugin')->dataTables($request, WorkingSchedule::visible());
    }

    public function getInfo(Request $request)
    {
        $id = $request->id;
        $staffSn = preg_replace('/^(.*)\-.*$/', '$1', $id);
        $shopSn = preg_replace('/^.*\-(.*)$/', '$1', $id);
        $response = WorkingSchedule::where('staff_sn', $staffSn)->where('shop_sn', $shopSn)->first()->toArray();
        $response['id'] = $id;
        return $response;
    }

    public function add(Request $request)
    {
        $this->validate($request, $this->makeValidator($request), [], trans($this->transPath));
        $date = $request->date;
        $model = new WorkingSchedule(['ymd' => str_replace('-', '', $date)]);
        $scheduleExist = $model->where('staff_sn', $request->staff_sn)->where('shop_sn', $request->shop_sn)->count() > 0;
        if ($scheduleExist) {
            return ['status' => -1, 'message' => '已有相同的排班存在'];
        } else {
            $this->curdService->model($model)->createOrUpdate($request->input());
        }
    }

    public function edit(Request $request)
    {
        $this->validate($request, $this->makeValidator($request), [], trans($this->transPath));
        $date = $request->date;
        $model = new WorkingSchedule(['ymd' => str_replace('-', '', $date)]);
        $scheduleExist = $model->where('staff_sn', $request->staff_sn)->where('shop_sn', $request->shop_sn)->count() > 0;
        if ($scheduleExist) {
            return ['status' => -1, 'message' => '已有相同的排班存在'];
        } else {
            $this->curdService->model($model)->createOrUpdate($request->input());
        }
    }

    protected function makeValidator($input)
    {
        $validator = [
            'shop_duty_id' => ['required', 'exists:attendance.shop_duty,id'],
            'date' => ['required'],
        ];
        if (empty($input['id'])) {
            $validator['shop_sn'] = ['required', 'exists:shops,shop_sn,deleted_at,NULL'];
            $validator['staff_sn'] = ['required_with:staff_name'];
            $validator['staff_name'] = ['required', 'exists:staff,realname,staff_sn,' . $input['staff_sn']];
        }
        return $validator;
    }
}
