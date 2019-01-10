<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\HR\StaffCollection;
use App\Http\Resources\HR\DepartmentResource;
use App\Http\Resources\HR\DepartmentCollection;
use App\Services\Dingtalk\Server\DeptService;

class DeptController extends Controller
{
    protected $deptService;

    public function __construct(DeptService $deptService)
    {
        $this->deptService = $deptService;
    }

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
        try {
            $data = $request->all();
            $department->fill($data);
            $parent_id = $request->input('parent_id');
            $parentID = $this->getParentById($parent_id);
            $result = $this->deptService->create([
                'name' => $department->name,
                'order' => $department->sort,
                'parentid' => $parentID,
            ]);
            if ($result['errcode'] == 0) {
                $department->source_id = $result['id'];
                $department->save();
                $this->deptService->update([
                    'id' => $result['id'],
                    'sourceIdentifier' => $department->id,
                ]);
            } else {
                return response()->json(['message' => $result['errmsg']], 500);
            }

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        $department->load('category', 'brand', 'children');

        return response()->json(new DepartmentResource($department), 201);
    }

    /**
     * 获取钉钉部门ID.
     * 
     * @param  int $parent_id
     */
    public function getParentById($parent_id)
    {
        if (empty($parent_id)) { // 顶级部门
            return 1;
        }
        $sourceId = \App\Models\Department::query()
            ->where('id', $parent_id)
            ->value('source_id');

        return $sourceId;
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
    public function update(DepartmentRequest $request, Department $department)
    {
        try {
            $data = $request->all();
            $department->fill($data);
            $dirty = $department->getDirty();
            if (!empty($dirty)) {
                $params = ['id' => $department->source_id];
                if (!empty($dirty['name'])) {
                    $params = array_add($params, 'name', $dirty['name']);
                }
                if (!empty($dirty['sort'])) {
                    $params = array_add($params, 'order', $dirty['sort']);
                }
                if (!empty($dirty['parent_id'])) {
                    $parentID = $this->getParentById($dirty['parent_id']);
                    $params = array_add($params, 'parentid', $parentID);
                }
                $result = $this->deptService->update($params);
                if ($result['errcode'] == 0) {
                    $department->save();
                } else {
                    return response()->json(['message' => $result['errmsg']], 500);
                }
            } else {
                return response()->json(['message' => '未发现改动'], 201);
            }
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
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
        try {
            $result = $this->deptService->delete($department->source_id);
            if ($result['errcode'] == 0) {
                $department->delete();
            } else {
                return response()->json(['message' => $result['errmsg']], 500);
            }
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

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
    public function sortBy(Request $request)
    {
        $this->validate($request, [
            'new_data.*.name' => 'required|string',
            'new_data.*.sort' => 'required|numeric',
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
