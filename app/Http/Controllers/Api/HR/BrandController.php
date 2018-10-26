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
            'name' => 'required|unique:brands',
        ];
        $message = [
            'name.required' => '品牌名称不能为空',
            'name.unique' => '品牌名称已存在',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $request->name;
        $brand->is_public = $request->is_public;
        $brand->save();

        return response()->json($brand, 201);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $rules = [
            'name' => 'required',
        ];
        $message = [
            'name.required' => '品牌名称不能为空',
        ];
        $this->validate($request, $rules, $message);
        $brand->name = $request->name;
        $brand->is_public = $request->is_public;
        $brand->save();

        return response()->json($brand, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $hasStaff = $brand->staffs->isNotEmpty();
        if ($hasStaff) {
            return response()->json(['message' => '有在职员工使用的品牌不能删除'], 422);
        }

        $brand->delete();

        return response()->json(null, 204);
    }
}
