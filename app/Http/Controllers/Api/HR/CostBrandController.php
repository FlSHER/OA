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
        $this->validate($request, [
            'name' => 'required|unique:brands|max:10',
        ], [], [
            'name' => '费用品牌名称',
        ]);
        $brand->name = $data['name'];
        $brand->save();
        $brand->brands()->attach((array)$data['brands']);

        return response()->json($brand->load('brands'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function show(CostBrand $cost_brand)
    {
        $cost_brand->load('brands');

        return response()->json($cost_brand, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CostBrand $cost_brand)
    {
        $data = $request->all();
        $this->validate($request, [
            'name' => 'required|max:10',
        ], [], [
            'name' => '费用品牌名称',
        ]);
        $cost_brand->name = $data['name'];
        $cost_brand->save();
        $cost_brand->brands()->sync((array)$data['brands']);

        return response()->json($cost_brand->load('brands'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(CostBrand $cost_brand)
    {
        $hasBrand = $cost_brand->brands->isNotEmpty();
        if ($hasBrand) {
            return response()->json(['message' => '有品牌使用的费用品牌不能删除'], 422);
        }

        $cost_brand->delete();

        return response()->json(null, 204);
    }
}
