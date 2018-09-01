<?php

namespace App\Http\Controllers\Api;

use DB;
use Authority;
use App\Models\Role;
use App\Http\Requests;
use App\Contracts\CURD;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\PluginService;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{

    protected $model = 'App\Models\Department';
    protected $curdService;

    public function __construct(CURD $curd)
    {
        $this->curdService = $curd->model($this->model);
    }

    /**
     * 获取部门下拉选择option标签
     * @param Request $request
     * @param \App\Services\HRMService $HRM
     * @return type
     */
    public function getOptionsById(Request $request, \App\Services\HRMService $HRM)
    {
        $id = $request->id ? $request->id : 0;
        return $HRM->getDepartmentOptionsById($id);
    }

    /**
     * dateTables获取部门列表
     * @param Request $request
     * @param PluginService $plugin
     * @return type
     */
    public function index(Request $request, PluginService $plugin)
    {
        return $plugin->dataTables($request, Department::visible());
    }

    /**
     * 获取部门详情.
     *
     * @param Department $department
     * @return void
     */
    public function show(Department $department)
    {
        $department->load('position');

        return $department;
    }

    /**
     * 获取部门ZTree所需数据
     * @param Request $request
     * @return type
     */
    public function tree(Request $request)
    {
        $checked = [];
        $hidden = [];
        if ($request->has('role_id')) {
            $checked = Role::find($request->role_id)->department->pluck('id')->toArray();
        } else if ($request->has('position_id')) {
            $checked = Position::find($request->position_id)->department->pluck('id')->toArray();
        }
        if ($request->has('checked') && $request->checked == 'all') {
            $checked = Department::all()->pluck('id')->toArray();
        }
        if ($request->has('without')) {
            $hidden = $request->without;
        }
        $departments = Department::where('parent_id', '=', 0)->whereNotIn('id', $hidden)->orderBy('sort', 'asc')->get();
        $response = $this->changeDepartmentsIntoZtreeNode($departments, $checked, $hidden);
        return $response;
    }

    /**
     * 添加单个部门
     * @param Request $request
     * @return type
     */
    public function store(Request $request)
    {
        return $this->updateOrCreate($request);
    }

    /**
     * 编辑单个部门
     * @param Request $request
     * @return type
     */
    public function update(Request $request)
    {
        return $this->updateOrCreate($request);
    }

    /**
     * 添加或编辑
     * @param Request $request
     * @return type
     */
    protected function updateOrCreate(Request $request)
    {
        $validateMsg = [
            'manager_name.exists' => '部门负责人 已离职或不存在',
        ];
        $this->validate($request, $this->makeValidator($request), $validateMsg, trans('fields.department'));
        $response = $this->curdService->createOrUpdate($request->all());
        Authority::forgetAuthorities();
        return $response;
    }

    /**
     * 删除部门及其子部门
     * @param Request $request
     * @return array
     */
    public function delete(Request $request, Department $department)
    {
        if ($department->is_locked) {
            return ['status' => -1, 'message' => '包含已锁定部门,请联系技术人员'];
        } elseif ($department->staff->where('status_id', '>=', 0)->count() > 0) {
            return ['status' => -1, 'message' => '当前部门有在职员工，无法删除'];
        } else {
            $department->getConnection()->transaction(function () use ($department) {
                $department->_children()->delete();
                $department->delete();
            });

            return ['status' => 1, 'message' => '删除成功'];
        }
    }

    /**
     * 部门重排序
     * @param Request $request
     * @return type
     */
    public function reOrder(Request $request)
    {
        $info = $request->info;
        Department::reOrder($info);
        return ['status' => 1, 'message' => '排序成功'];
    }

    private function changeDepartmentsIntoZtreeNode($departments, $checked, $hidden)
    {
        $data = [];
        $availableDepartments = Authority::getAvailableDepartments();
        foreach ($departments as $department) {
            if (in_array($department->id, $hidden)) {
                continue;
            }
            $dataTmp = [
                'name' => $department->name,
                'full_name' => $department->full_name,
                'drag' => true,
                'id' => $department->id,
                'children' => $this->changeDepartmentsIntoZtreeNode($department->_children, $checked, $hidden),
                'iconSkin' => '_'
            ];
            if ($department->is_public) {
                $dataTmp['chkDisabled'] = true;
                $dataTmp['checked'] = true;
            }
            if (!in_array($department->id, $availableDepartments)) {
                $dataTmp['iconSkin'] = 'fa fa-lock _';
                $dataTmp['font'] = ['color' => '#ccc'];
                $dataTmp['drag'] = false;
                $dataTmp['chkDisabled'] = true;
            }
            if (in_array($department->id, $checked)) {
                $dataTmp['checked'] = true;
            }
            $data[] = $dataTmp;
        }
        return $data;
    }

    protected function makeValidator(Request $request)
    {
        return [
            'name' => ['required', 'unique:departments,name,' . $request->id . ',id,parent_id,' . $request->parent_id . ',deleted_at,NULL'],
            'parent_id' => ['required'],
            'manager_name' => ['exists:staff,realname'],
        ];
    }

}
