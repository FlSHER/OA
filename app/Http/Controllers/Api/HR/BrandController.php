<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\BrandCollection;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Brand::query()
            ->with('cost_brands')
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
            
        if (isset($list['data'])) {
            $list['data'] = new BrandCollection($list['data']);

            return $list;
        }
        
        return new BrandCollection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Brand $brand)
    {
        $rules = [
            'name' => 'required|unique:brands|max:10',
            'cost_brands' => 'required|array',
        ];
        $message = [
            'cost_brands.required' => '关联费用品牌不能为空',
            'name.required' => '品牌名称不能为空',
            'name.max' => '品牌名称不能超过 :max 个字',
            'name.unique' => '品牌名称已存在',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $request->name;
        $brand->is_public = $request->is_public;
        $brand->save();
        $brand->cost_brands()->attach($request->cost_brands);

        return response()->json($brand->load('cost_brands'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $rules = [
            'name' => 'required|max:10',
            'cost_brands' => 'required|array',
        ];
        $message = [
            'cost_brands.required' => '关联费用品牌不能为空',
            'name.required' => '品牌名称不能为空',
            'name.max' => '品牌名称不能超过 :max 个字',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $request->name;
        $brand->is_public = $request->is_public;
        $brand->save();
        $brand->cost_brands()->sync($request->cost_brands);

        return response()->json($brand->load('cost_brands'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        return response()->json($brand, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        if ($brand->staffs->isNotEmpty()) {

            return response()->json(['message' => '有在职员工使用的品牌不能删除'], 422);

        } elseif ($brand->cost_brands->isNotEmpty()) {

            return response()->json(['message' => '有费用品牌关联的品牌不能删除'], 422);
        }

        $brand->delete();
        $brand->cost_brands()->detach();

        return response()->json(null, 204);
    }
}
