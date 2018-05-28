<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Contracts\CURD;
use App\Services\PluginService;
use Authority;
use DB;

class DepartmentController extends Controller
{

    protected $model = 'App\Models\Department';
    protected $curdService;

    public function __construct(CURD $curd)
    {
        $this->curdService = $curd->model($this->model);
    }

    public function showManagePage()
    {
        $data['position'] = Position::orderBy('level', 'asc')->get();
        return view('hr.department')->with($data);
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
    public function getDepartmentList(Request $request, PluginService $plugin)
    {
        return $plugin->dataTables($request, Department::visible());
    }

    public function getInfo(Request $request)
    {
        $id = $request->id;
        $department = Department::with('position')->find($id);
        return $department;
    }

    /**
     * 获取部门ZTree所需数据
     * @param Request $request
     * @return type
     */
    public function getTreeView(Request $request)
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
    public function addDepartmentByOne(Request $request)
    {
        return $this->addOrUpdate($request);
    }

    /**
     * 编辑单个部门
     * @param Request $request
     * @return type
     */
    public function editDepartmentByOne(Request $request)
    {
        return $this->addOrUpdate($request);
    }

    /**
     * 添加或编辑
     * @param Request $request
     * @return type
     */
    protected function addOrUpdate(Request $request)
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
    public function deleteDepartment(Request $request)
    {
        $id = $request->id;
        $response = ['status' => 0, 'message' => '无效操作'];
        DB::beginTransaction();
        $response = Department::deleteByTrees($id);
        if ($response['status'] == 1) {
            DB::commit();
        } else {
            DB::rollBack();
        }
        return $response;
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
