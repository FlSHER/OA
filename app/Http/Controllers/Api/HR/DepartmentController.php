<?php

namespace App\Http\Controllers\Api\HR;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
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
            ->with('category')
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
    public function store(DepartmentRequest $request, Department $department)
    {
        $department->fill($request->all());
        $department->save();
        $department->load('category', 'brand', 'children');

        return response()->json(new DepartmentResource($department), 201);
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

        return response()->json($department);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Department $department
     * @return \Illuminate\Http\Response
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        $department->fill($request->all());
        $department->save();
        $department->load('category', 'brand', 'children');

        return response()->json(new DepartmentResource($department), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $hasStaff = $department->staff()->where('status_id', '>=', 0)->count() > 0;
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
    public function tree()
    {
        return Department::get()->map(function ($item) {
            $item->parent_id = $item->parent_id ?: null;

            return $item;
        });
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
    public function sortBy(Request $request, Department $department)
    {
        $this->validate($request, [
            'new_data.*.id' => 'required|exists:departments,id',
            'new_data.*.name' => 'required',
            'new_data.*.sort' => 'required|numeric',
        ], [
            'new_data.*.id.required' => '部门不能为空',
            'new_data.*.name.required' => '部门名称不能为空',
            'new_data.*.sort.required' => '部门排序值不能为空',
        ]);

        $data = array_filter($request->input('new_data', []));
        if (empty($data)) {
            return response()->json(['message' => '没有变动']);
        }
        return $department->getConnection()->transaction(function () use ($data) {
            $column = array_column($data, 'id');
            Department::whereIn('id', $column)->get()->map(function ($item) use ($data) {
                foreach ($data as $key => $dept) {
                    if ($dept['id'] == $item->id) {
                        $item->sort = $dept['sort'];
                        $item->parent_id = $dept['parent_id'];
                        $item->save();
                    }
                }
            });

            $newData = Department::whereIn('id', $column)->get();

            return response()->json($newData, 201);
        });
    }
}
