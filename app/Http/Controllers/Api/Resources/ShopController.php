<?php

namespace App\Http\Controllers\Api\Resources;

use App\Models\HR\Shop;
use App\Models\HR\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\ShopCollection;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Shop::api()
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();

        if (isset($list['data'])) {
            $list['data'] = new ShopCollection($list['data']);

            return $list;
        }
        return new ShopCollection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Shop $shop)
    {        
        $this->validate($request, $this->rules($request), $this->messages());
        $data = $request->all();
        $shop->fill($data);
        
        $staff = collect($data['staff'])->pluck('staff_sn');
        $shop->getConnection()->transaction(function () use ($shop, $staff) {
            // 更新员工店铺编号
            $shop->staff()->update(['shop_sn' => '']);
            Staff::whereIn('staff_sn', $staff)->update(['shop_sn' => $shop->shop_sn]);
            $shop->createShopLog();
            
            $shop->save();
        });

        $shop->load(['staff', 'brand', 'department']);

        return response()->json($shop, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Shop $shop)
    {
        $shop->load(['brand', 'department', 'staff']);

        return response()->json($shop, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $shop)
    {
        $this->validate($request, $this->rules($request), $this->messages());
        $data = $request->all();
        $shop->fill($data);
        
        $staff = collect($data['staff'])->pluck('staff_sn');
        $shop->getConnection()->transaction(function () use ($shop, $staff) {
            // 更新员工店铺编号
            $shop->staff()->update(['shop_sn' => '']);
            Staff::whereIn('staff_sn', $staff)->update(['shop_sn' => $shop->shop_sn]);
            $shop->createShopLog();
            
            $shop->save();
        });

        $shop->load(['staff', 'brand', 'department']);

        return response()->json($shop, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        $shop->getConnection()->transaction(function () use ($shop) {
            $shop->staff()->update(['shop_sn' => '']);
            $shop->delete();
        });

        return response()->json(null, 204);
    }

    /**
     * 保存店铺位置信息.
     *
     * @param Request $request
     * @return void
     */
    public function position(Request $request)
    {
        $shop = Shop::find($request->id);
        if ($shop === null) {
            return response()->json(['message' => '店铺数据错误', 'code' => '0'], 422);
            // return '店铺数据错误';
        }
        $amap = createRequest('/api/amap', 'post', [
            'shop_sn' => $shop->shop_sn,
            'shop_name' => $shop->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
        $shop->lng = $request->longitude;
        $shop->lat = $request->latitude;
        // $shop->geo_hash = $request->geo_hash;
        $shop->save();
        
        return response()->json(['message' => '操作成功', 'code' => '1'], 201);
    }
    
    protected function rules(Request $request)
    {
        $rules = [
            'shop_sn' => 'bail|required|unique:shops|max:10',
            'name' => 'bail|required|max:50',
            'manager_sn' => 'bail|nullable|exists:staff,staff_sn',
            'department_id' => 'bail|exists:departments,id',
            'brand_id' => 'bail|exists:brands,id',
            'province_id' => 'bail|required',
            'city_id' => 'bail|required',
            'county_id' => 'bail|required',
        ];
        if (strtolower($request->getMethod()) === 'patch') {
            return array_merge($rules, [
                'shop_sn' => 'required|max:10',
            ]);
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'shop_sn.required' => '店铺代码不能为空',
            'shop_sn.unique' => '店铺代码不能重复',
            'name.required' => '店铺名称不能为空',
            'manager_sn.exists' => '店长信息未录入系统',
            'department_id.exists' => '部门信息未录入系统',
            'brand_id.exists' => '品牌信息未录入信息',
            'province_id.required' => '店铺省地区不能为空',
            'city_id.required' => '店铺城市不能为空',
            'county_id.required' => '店铺地区不能为空',
        ];
    }
}
