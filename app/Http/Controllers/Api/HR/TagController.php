<?php

namespace App\Http\Controllers\Api\HR;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreTag;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagCollection;
use App\Models\Tag as TagModel;


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
        $type = in_array($type = $request->query('type', 'staff'), ['staff', 'shops']) ? $type : 'staff';
        $tags = TagModel::query()
            ->with('category')
            ->whereHas('category', function($query) use ($type) {
                $query->where('type', $type);
            })  
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
                'required',
                Rule::unique('tags')->ignore($tag->id),
            ],
            'tag_category_id' => 'exists:tag_categories,id',
            'description' => 'max:50',
            'weight' => 'numeric|min:0',
        ], [
            'name.max' => '标签名称过长',
            'name.required' => '标签名称必填',
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
    public function destroy(TagModel $tag)
    {
        if (!$tag->staff()->count() && !$tag->shop()->count()) {
            $tag->delete();

            return response()->json(null, 204);
        }

        return response()->json(['message' => '有资源使用该标签，不能删除，请先清理使用该标签的资源'], 422);
    }

    /**
     * 获取单个标签信息.
     * 
     * @param  \App\Models\Tag $tag
     * @return mixed
     */
    public function show(TagModel $tag)
    {   
        $tag->load('category');

        return response()->json($tag);
    }
}
