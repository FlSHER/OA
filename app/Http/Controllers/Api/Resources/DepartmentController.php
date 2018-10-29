<?php

namespace App\Http\Controllers\Api\Resources;

use Illuminate\Http\Request;
use App\Models\HR\Department;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\StaffCollection;
use App\Http\Resources\HR\DepartmentResource;
use App\Http\Resources\HR\DepartmentCollection;

class DepartmentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Department::query()
            ->when(!empty($request->withTrashed), function ($query) {
                return $query->withTrashed();
            })
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();

        if (isset($list['data'])) {
            $list['data'] = new DepartmentCollection($list['data']);
            
            return $list;
        }
        
        return new DepartmentCollection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'unique:departments'],
            'manager_name' => ['required', 'exists:staff,realname'],
        ];
        $messages = [
            'name.required' => '部门名称不能为空',
            'name.unique' => '部门名称已存在',
            'manager_name.required' => '部门负责人必填',
            'manager_name.exists' => '部门负责人不存在',
        ];
        $this->validate($request, $rules, $messages);
        $department = new Department();
        $department->fill($request->all());
        $department->save();

        return response()->json($department, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Department $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return new DepartmentResource($department);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Department $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $rules = [
            'name' => ['required'],
            'manager_name' => ['exists:staff,realname'],
        ];
        $messages = [
            'name.required' => '部门名称不能为空',
            'manager_name.exists' => '部门负责人不存在',
        ];
        $this->validate($request, $rules, $messages);
        $department->name = $request->name;
        $department->brand_id = $request->brand_id;
        $department->manager_sn = $request->staff_sn;
        $department->manager_name = $request->staff_name;
        $department->parent_id = $request->parent_id ? : 0;
        $department->save();

        return response()->json($department, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Department $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $hasStaff = $department->staff->isNotEmpty();
        if ($hasStaff) {
            return response()->json(['message' => '有在职员工使用的部门不能删除'], 422);
        }

        $department->delete();

        return response()->json(null, 204);
    }

    /**
     * 获取全部部门.
     *
     * @return mixed
     */
    public function tree(Department $department)
    {
        return response()->json($department->get())
    }

    public function getChildrenAndStaff(Department $department)
    {
        return [
            'children' => new DepartmentCollection($department->children),
            'staff' => new StaffCollection($department->staff()->working()->get()),
        ];
    }

    public function getStaff(Department $department)
    {
        return new StaffCollection($department->staff()->working()->get());
    }

    /**
     * 部门拖动排序
     *
     * @return mixed 
     */
    public function sortBy(Request $request)
    {
        $this->validate($request, [
            'new_data.*.name' => 'bail|required|string',
            'new_data.*.sort' => 'bail|required|numeric',
        ], [
            'new_data.*.name.required' => '部门名称不能为空',
            'new_data.*.sort.required' => '部门排序值不能为空',
        ]);

        $departments = Department::get();
        $data = $request->input('new_data', []);
        foreach ($data as $key => $val) {
            $isUpdate = $departments->filter(function ($item) use ($val) {
                return $item->id === $val['id'] && $item->sort !== $val['sort'];
            });
            if ($isUpdate->isNotEmpty()) {
                Department::where('id', $val['id'])->update(['sort' => $val['sort']]);
            }
        }

        return response()->json(['message' => '操作成功'], 201);
    }
}
