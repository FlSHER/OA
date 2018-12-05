<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\TagCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class TagCateController extends Controller
{
    /**
     * 获取全部标签分类.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $type = in_array($type = $request->query('type', 'staff'), ['staff', 'shops']) ? $type : 'staff';
        $categories = TagCategory::withCount('tags')
            ->where('type', $type)
            ->orderBy('weight', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($categories);
    }

    /**
     * 存储标签分类.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TagCategory $category
     * @return mixed
     */
    public function store(Request $request, TagCategory $category)
    {
        $this->validate($request, [
            'name' => 'required|unique:tag_categories|max:10',
            'type' => 'required|in:staff,shops',
            'color' => 'string|max:7',
            'weight' => 'numeric|min:0',
        ], [
            'type.required' => '标签分类类型必填',
            'type.in' => '标签分类类型错误',
            'name.required' => '标签分类名称必填',
            'name.max' => '标签分类名称过长',
            'name.unique' => '标签分类已经存在',
        ]);

        $category->fill($request->all());
        $category->save();

        return response()->json($category, 201);
    }

    /**
     * 更新标签分类.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\TagCategory $category
     * @return mixed
     */
    public function update(Request $request, TagCategory $category)
    {
        $this->validate($request, [
            'name' => [
                'max:10',
                Rule::unique('tag_categories')->ignore($category->id),
            ],
            'color' => 'string|max:7',
            'weight' => 'numeric|min:0',
        ], [
            'name.max' => '标签分类名称过长',
            'name.unique' => '标签分类已经存在',
            'weight.numeric' => '权重值必须为数字',
            'weight.min' => '权重值不能小于0',
        ]);

        $category->fill($request->all());
        $category->save();

        return response()->json($category, 201);
    }

    /**
     * 删除标签分类.
     * 
     * @param  \App\Models\TagCategory $category
     * @return mixed
     */
    public function destroy(TagCategory $category)
    {
        if (!$category->tags()->count()) {
            $category->delete();

            return response()->json(null, 204);
        }

        return response()->json(['message' => '该分类下还有标签存在，请先删除标签再删除分类'], 422);
    }

    /**
     * 获取一个分类信息.
     * 
     * @param  \App\Models\TagCategory $category
     * @return mixed
     */
    public function show(TagCategory $category)
    {   
        return response()->json($category);
    }
}
