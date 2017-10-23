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

    public function addOrEdit(Request $request)
    {
        $this->validate($request, $this->makeValidator($request), [], trans($this->transPath));
        $date = $request->date;
        if (empty($request->clock_in)) $request->offsetSet('clock_in', null);
        if (empty($request->clock_out)) $request->offsetSet('clock_out', null);
        if ($request->has('id')) {
            $id = $request->id;
            $staffSn = preg_replace('/^(.*)\-.*$/', '$1', $id);
            $shopSn = preg_replace('/^.*\-(.*)$/', '$1', $id);
        } else {
            $staffSn = $request->staff_sn;
            $shopSn = $request->shop_sn;
        }
        $model = new WorkingSchedule(['ymd' => str_replace('-', '', $date)]);
        $request->offsetUnset('date');
        $workingSchedule = $model->where('staff_sn', $staffSn)->where('shop_sn', $shopSn)->first();
        if ($request->shop_duty_id == 1) {
            $model->where('shop_sn', $shopSn)->where('shop_duty_id', 1)->update(['shop_duty_id' => 3]);
        }
        if ($request->has('id')) {
            $request->offsetUnset('id');
            $model->where('staff_sn', $staffSn)->where('shop_sn', $shopSn)->update($request->only(['clock_in', 'clock_out', 'shop_duty_id']));
            return ['status' => 1, 'message' => '编辑成功'];
        } else if (!empty($workingSchedule)) {
            return ['status' => -1, 'message' => '已有相同的排班存在'];
        } else {
            $model->insert($request->input());
            return ['status' => 1, 'message' => '添加成功'];
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $staffSn = preg_replace('/^(.*)\-.*$/', '$1', $id);
        $shopSn = preg_replace('/^.*\-(.*)$/', '$1', $id);
        WorkingSchedule::where('staff_sn', $staffSn)->where('shop_sn', $shopSn)->delete();
        return ['status' => 1, 'message' => '删除成功'];
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
