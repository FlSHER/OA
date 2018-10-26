<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\CostBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\CostBrandCollection;

class CostBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = CostBrand::query()
            ->with('brands')
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
            
        if (isset($list['data'])) {
            $list['data'] = new CostBrandCollection($list['data']);

            return $list;
        }
        
        return new CostBrandCollection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CostBrand $brand)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required|unique:brands',
            'brands' => 'array',
        ];
        $message = [
            'name.required' => '费用品牌名称不能为空',
            'name.unique' => '费用品牌名称已存在',
            'brands.array' => '关联品牌数据错误',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $data['name'];
        $brand->save();
        $brand->brands()->attach($data['brands']);

        return response()->json($brand, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function show(CostBrand $brand)
    {
        $brand->load('brands');

        return response()->json($brand, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CostBrand $brand)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required',
            'brands' => 'array',
        ];
        $message = [
            'name.required' => '费用品牌名称不能为空',
            'brands.array' => '关联品牌数据错误',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $data['name'];
        $brand->save();
        $brand->brands()->sync($data['brands']);

        return response()->json($brand, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(CostBrand $brand)
    {
        $hasBrand = $brand->brands->isNotEmpty();
        if ($hasBrand) {
            return response()->json(['message' => '有品牌使用的费用品牌不能删除'], 422);
        }

        $brand->delete();
        $brand->brands()->detach();

        return response()->json(null, 204);
    }
}
