<?php

namespace App\Http\Controllers\Api\HR;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreTag;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagCollection;
use App\Models\Tag as TagModel;
use App\Models\TagCategory as TagCategoryModel;


class TagController extends Controller
{
    /**
     * 获取标签列表。
     * 
     * @param  Request  $request
     * @param  TagModel $TagModel
     * @return mixed
     */
    public function index(Request $request)
    {
        $tags = TagModel::query()
            ->with('category')
            ->filterByQueryString()
            ->withPagination();

        if (isset($tags['data'])) {
            $tags['data'] = new TagCollection($tags['data']);

            return $tags;
        }

        return new TagCollection($tags);
    }

    /**
     * 创建标签.
     * 
     * @param  \App\Http\Requests\StoreTag $request
     * @param  \App\Models\Tag $tag
     * @return mixed
     */
    public function store(StoreTag $request, TagModel $tag)
    {
        $tag->fill($request->all());
        $tag->save();

        return response()->json($tag->load('category'), 201);
    }

    /**
     * 更新标签.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Tag  $tag
     * @return mixed
     */
    public function update(Request $request, TagModel $tag)
    {
        $this->validate($request, [
            'name' => [
                'max:10',
                Rule::unique('tags')->ignore($tag->id),
            ],
            'tag_category_id' => 'exists:tag_categories,id',
            'description' => 'max:50',
            'weight' => 'numeric|min:0',
        ], [
            'name.max' => '标签名称过长',
            'name.unique' => '标签已经存在',
            'tag_category_id.exists' => '标签分类不存在',
            'description.max' => '标签描述过长',
            'weight.numeric' => '权重值必须为数字',
            'weight.min' => '权重值不能小于0',
        ]);

        $tag->fill($request->all());
        $tag->save();

        return response()->json($tag->load('category'), 201);
    }

    /**
     * 删除标签.
     * 
     * @param  \App\Models\Tag $tag
     * @return mixed
     */
    public function delete(TagModel $tag)
    {
        if (!$tag->taggable()->count()) {
            $tag->delete();

            return response()->json(null, 204);
        }

        return response()->json(['message' => '有资源使用该标签，不能删除，请先清理使用该标签的资源'], 422);
    }

    /**
     * 获取全部标签分类.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function categories(Request $request)
    {
        $categories = TagCategoryModel::withCount('tags')
            ->orderBy('weight', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($categories);
    }

    /**
     * 存储标签分类.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TagCategory $cate
     * @return mixed
     */
    public function storeCate(Request $request, TagCategoryModel $cate)
    {
        $this->validate($request, [
            'name' => 'required|unique:tag_categories|max:10',
            'color' => 'string|max:7',
            'weight' => 'numeric|min:0',
        ], [
            'name.required' => '标签分类名称必填',
            'name.max' => '标签分类名称过长',
            'name.unique' => '标签分类已经存在',
        ]);

        $cate->fill($request->all());
        $cate->save();

        return response()->json($cate, 201);
    }

    /**
     * 更新标签分类.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\TagCategory $cate
     * @return mixed
     */
    public function updateCate(Request $request, TagCategoryModel $cate)
    {
        $this->validate($request, [
            'name' => [
                'max:10',
                Rule::unique('tag_categories')->ignore($cate->id),
            ],
            'color' => 'string|max:7',
            'weight' => 'numeric|min:0',
        ], [
            'name.max' => '标签分类名称过长',
            'name.unique' => '标签分类已经存在',
            'weight.numeric' => '权重值必须为数字',
            'weight.min' => '权重值不能小于0',
        ]);

        $cate->fill($request->all());
        $cate->save();

        return response()->json($cate, 201);
    }

    /**
     * 删除标签分类.
     * 
     * @param  \App\Models\TagCategory $cate
     * @return mixed
     */
    public function deleteCate(TagCategoryModel $cate)
    {
        if (!$cate->tags()->count()) {
            $cate->delete();

            return response()->json(null, 204);
        }

        return response()->json(['message' => '该分类下还有标签存在，请先删除标签再删除分类'], 422);
    }
}
