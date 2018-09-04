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
            'staff_name' => ['required', 'exists:staff,realname'],
        ];
        $messages = [
            'name.required' => '部门名称不能为空',
            'name.unique' => '部门名称已存在',
            'staff_name.required' => '部门负责人必填',
            'staff_name.exists' => '部门负责人不存在',
        ];
        $this->validate($request, $rules, $messages);
        $department = new Department;
        $department->name = $request->name;
        $department->brand_id = $request->brand_id;
        $department->manager_sn = $request->staff_sn;
        $department->manager_name = $request->staff_name;
        $department->parent_id = $request->parent_id ? : 0;
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
        $department->delete();

        return response()->json(null, 204);
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
}
