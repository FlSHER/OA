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
        $rules = [
            'name' => 'required|unique:brands',
        ];
        $message = [
            'name.required' => '费用品牌名称不能为空',
            'name.unique' => '费用品牌名称已存在',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $request->name;
        $brand->save();

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
        $rules = [
            'name' => 'required',
        ];
        $message = [
            'name.required' => '费用品牌名称不能为空',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $request->name;
        $brand->save();

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

        return response()->json(null, 204);
    }
}
