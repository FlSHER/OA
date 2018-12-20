<?php

namespace App\Http\Controllers\Api\Resources;

use Illuminate\Http\Request;
use App\Models\Department;
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
    public function store(Request $request, Department $department)
    {
        $rules = [
            'name' => 'required|unique:departments|max:10',
            'manager_sn' => ['exists:staff,staff_sn'],
        ];
        $messages = [
            'name.required' => '部门名称不能为空',
            'name.unique' => '部门名称已存在',
            'name.max' => '部门名称不能超过 :max 个字',
            'manager_sn.exists' => '部门负责人不存在',
        ];
        $this->validate($request, $rules, $messages);
        $department->fill($request->all());
        $department->save();

        return response()->json($department, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        $department->load('brand', 'children');

        return new DepartmentResource($department);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Department $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $rules = [
            'name' => 'required|max:10',
            'manager_sn' => ['exists:staff,staff_sn'],
        ];
        $messages = [
            'name.required' => '部门名称不能为空',
            'name.max' => '部门名称不能超过 :max 个字',
            'manager_sn.exists' => '部门负责人不存在',
        ];
        $this->validate($request, $rules, $messages);
        $department->fill($request->all());
        $department->save();

        return response()->json($department, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        \DB::beginTransaction();
        
        $response = Department::deleteByTrees($department->id);
        if ($response['status'] == 1) {
            \DB::commit();
        } else {

            return response()->json($response, 422);
            \DB::rollBack();
        }

        return response()->json(null, 204);
    }

    /**
     * 获取全部部门.
     *
     * @return mixed
     */
    public function tree(Department $department)
    {
        return response()->json($department->get());
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

    /**
     * 返回部门树形结构.
     * 
     * @param  Department $department
     * @return mixed
     */
    public function getTreeById(Department $department)
    {
        $department->load('parent');

        return response()->json($department);
    }
}
