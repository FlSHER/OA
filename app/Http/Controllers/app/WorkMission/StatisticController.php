<?php

namespace App\Http\Controllers\app\WorkMission;

use App\Models\App\WorkMission\StatisticDepartment;
use App\Models\App\WorkMission\StatisticUser;
use App\Models\Department;
use App\Models\HR\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Curl;

class StatisticController extends Controller
{
    public function index()
    {
        return view('app.work_mission.statistic');
    }

    public function listData(Request $request)
    {

        $result = app('Plugin')->dataTables($request, StatisticUser::class);
        return $result;
    }

    public function save(Request $request)
    {
        $this->validation($request);
        if ($request->has('id') && intval($request->input('id'))) {
            $statistic = StatisticUser::find($request->input('id'));
        } else {
            $statistic = new StatisticUser();
        }
        $staff = Staff::with('department')->find($request->input('staff_sn'));
        $statistic->staff_sn = $staff->staff_sn;
        $statistic->realname = $staff->realname;
        $statistic->department_id = $staff->department_id;
        $statistic->department_name = $staff->department->name;
        $statistic->save();

        $depIdArray = array_pluck($request->input('statistic_department'), 'department_id');
        $depData = Department::select('id', 'full_name')->find(array_unique($depIdArray));
        if ($request->has('id') && !empty(intval($request->id))) {
            //编辑
            StatisticDepartment:: where('statistic_user_id', $request->input('id'))->delete();
        }
        foreach ($depData as $k => $v) {
            $department = new StatisticDepartment();
            $department->statistic_user_id = $statistic->id;
            $department->department_id = $v->id;
            $department->department_name = $v->full_name;
            $department->save();
        }
        $this->clearStatisticUserInfo();//清楚统计数据缓存
        return ['status' => 1, 'message' => 'success'];
    }

    public function update(Request $request){
        $data = StatisticUser::with('statisticDepartment')->find($request->input('id'));
        return $data;
    }


    public function delete(Request $request)
    {
        StatisticDepartment:: where('statistic_user_id', $request->input('id'))->delete();
        StatisticUser::where('id', $request->input('id'))->delete();
        $this->clearStatisticUserInfo();
        return ['status' => 1, 'message' => 'success'];
    }

    /**
     * 验证
     * @param $request
     */
    private function validation($request)
    {
        $rules = [
            'staff_sn' => 'required|exists:staff,staff_sn|unique:work_mission.statistic_users,staff_sn',
            'statistic_department' => 'array',
            'statistic_department.*.department_id' => 'required|exists:departments,id',
        ];

        if ($request->has('id') && !empty($request->id)) {
            $rules['staff_sn'] = 'required|exists:staff,staff_sn|unique:work_mission.statistic_users,staff_sn,' . $request->id;
            $rules['statistic_department.*.department_id'] = 'required|exists:departments,id';
        }
        $this->validate($request, $rules, [], trans('fields.statistic'));
    }

    /**
     * 清楚统计人员缓存数据
     */
    private function clearStatisticUserInfo(){
        Curl::setUrl(config('api.url.workMission.statistic_clear'))->get();//清楚统计数据缓存
    }
}
