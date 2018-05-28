<?php

namespace App\Http\Controllers\app\WorkMission;

use App\Models\App\WorkMission\Department;
use App\Models\Department as StaffDepartment;
use App\Models\HR\Staff;
use App\Models\App\WorkMission\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Curl;

class AllotmentUserController extends Controller
{
    /**
     * 列表视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('app.work_mission.allotment_user');
    }

    /**
     * 获取分配人员列表数据
     * @param Request $request
     */
    public function listData(Request $request)
    {
        $result = app('Plugin')->dataTables($request, User::class);
        return $result;
    }

    /**
     * 新增与编辑保存处理
     * @param Request $request
     */
    public function save(Request $request)
    {
        $this->validation($request);
        $this->saveToTable($request);
        $this->clearAllotmentUserInfo();//清楚分配数据缓存
        return ['status' => 1, 'message' => 'success'];
    }

    /**
     * 清楚分配人员缓存数据
     */
    private function clearAllotmentUserInfo(){
        Curl::setUrl(config('api.url.workMission.user_info_cache_clear'))->get();//清楚分配数据缓存
    }

    /**
     * 验证
     * @param $request
     */
    private function validation($request)
    {
        $rules = [
            'staff_sn' => 'required|exists:staff,staff_sn|unique:work_mission.users,staff_sn',
            'department' => 'array',
            'department.*.department_id' => 'required|exists:departments,id',
        ];

        if ($request->has('id') && !empty($request->id)) {
            $rules['staff_sn'] = 'required|exists:staff,staff_sn|unique:work_mission.users,staff_sn,' . $request->id;
            $rules['department.*.department_id'] = 'required|exists:departments,id|unique:work_mission.departments,department_id,' . $request->id . ',user_id';
        }
        $this->validate($request, $rules, [], trans('fields.allotment_user'));
    }

    /**
     * 编辑获取数据
     * @param Request $request
     */
    public function update(Request $request)
    {
        $data = User::with('department')->find($request->input('id'));
        return $data;
    }

    /**
     * 删除处理
     * @param Request $request
     */
    public function delete(Request $request)
    {
        DB::transaction(function () use ($request) {
            Department:: where('user_id', $request->input('id'))->delete();
            User::where('id', $request->input('id'))->delete();
        });
        $this->clearAllotmentUserInfo();//清楚分配数据缓存
        return ['status' => 1, 'message' => 'success'];
    }


    private function saveToTable($request)
    {
        DB::transaction(function () use ($request) {
            $userId = $this->userTableSave($request);
            $this->departmentTableSave($request, $userId);
        });
    }

    private function userTableSave($request)
    {
        $staff = Staff::with('department')->find($request->input('staff_sn'));
        if ($request->has('id') && !empty(intval($request->id))) {
            //编辑保存
            $user = User::find($request->id);
        } else {
            //新增保存
            $user = new User();
        }
        $user->staff_sn = $request->input('staff_sn');
        $user->realname = $staff->realname;
        $user->department_id = $staff->department_id;
        $user->department_name = $staff->department->name;
        $user->save();
        return $user->id;
    }

    private function departmentTableSave($request, $userId)
    {
        $depIdArray = array_pluck($request->input('department'), 'department_id');
        $depData = StaffDepartment::select('id', 'full_name')->find($depIdArray);
        if ($request->has('id') && !empty(intval($request->id))) {
            //编辑
            Department:: where('user_id', $request->input('id'))->delete();
        }
        foreach ($depData as $v) {
            $department = new Department();
            $department->user_id = $userId;
            $department->department_id = $v->id;
            $department->department_name = $v->full_name;
            $department->save();
        }
    }
}
