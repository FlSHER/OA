<?php 

namespace App\Http\Controllers\Api\HR;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DepartmentCategory;
use App\Http\Controllers\Controller;

class DepartmentCategoryController extends Controller
{
	
    /**
     * 获取部门分类.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $categories = DepartmentCategory::query()->get();

        return response()->json($categories);
    }

    /**
     * 存储部门分类.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DepartmentCategory $cate
     * @return mixed
     */
    public function store(Request $request, DepartmentCategory $cate)
    {
        $this->validate($request, [
            'name' => 'required|unique:department_categories|max:10',
            'fields' => 'array',
        ], [
            'name.required' => '部门分类名称必填',
            'name.max' => '部门分类名称过长',
            'name.unique' => '部门分类已经存在',
            'fields.array' => '分类字段类型错误',
        ]);

        $cate->fill($request->all());
        $cate->save();

        return response()->json($cate, 201);
    }

    /**
     * 更新部门分类.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\DepartmentCategory $cate
     * @return mixed
     */
    public function update(Request $request, DepartmentCategory $cate)
    {
        $this->validate($request, [
            'name' => [
                'max:10',
                Rule::unique('department_categories')->ignore($cate->id),
            ],
            'fields' => 'array',
        ], [
            'name.max' => '部门分类名称过长',
            'name.unique' => '部门分类已经存在',
            'fields.array' => '分类字段类型错误',
        ]);

        $cate->fill($request->all());
        $cate->save();

        return response()->json($cate, 201);
    }

    /**
     * 删除部门分类.
     * 
     * @param  \App\Models\DepartmentCategory $cate
     * @return mixed
     */
    public function destroy(DepartmentCategory $cate)
    {
        if (!$cate->department()->count()) {
            $cate->delete();

            return response()->json(null, 204);
        }

        return response()->json(['message' => '该分类下还有部门存在，请先删除部门再删除分类'], 422);
    }

    /**
     * 获取一个分类信息.
     * 
     * @param  \App\Models\DepartmentCategory $category
     * @return mixed
     */
    public function show(DepartmentCategory $cate)
    {   
        return response()->json($category);
    }
}
