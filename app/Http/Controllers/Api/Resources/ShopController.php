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

        $shop->getConnection()->transaction(function () use ($shop, $data) {
            $shop->save();
            $shop->createShopLog();
            if (!empty($data['staff'])) {
                $staffSn = array_column($data['staff'], 'staff_sn');
                $shop->staff()->update(['shop_sn' => '']);
                Staff::whereIn('staff_sn', $staffSn)->update(['shop_sn' => $shop->shop_sn]);
            }
            if (!empty($data['tags'])) {
                $tags = array_column($data['tags'], 'id');
                $shop->tags()->sync($tags);
            }
        });

        $shop->load(['staff', 'brand', 'department', 'manager', 'manager1', 'manager2', 'manager3', 'tags']);

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
        $shop->load(['staff', 'brand', 'department', 'manager', 'manager1', 'manager2', 'manager3', 'tags']);

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

        $shop->getConnection()->transaction(function () use ($shop, $data) {
            $shop->save();
            $shop->createShopLog();
            if (!empty($data['staff'])) {
                $staffSn = array_column($data['staff'], 'staff_sn');
                $shop->staff()->update(['shop_sn' => '']);
                Staff::whereIn('staff_sn', $staffSn)->update(['shop_sn' => $shop->shop_sn]);
            }
            if (!empty($data['tags'])) {
                $tags = array_column($data['tags'], 'id');
                $shop->tags()->sync($tags);
            }
        });

        $shop->load(['staff', 'brand', 'department', 'manager', 'manager1', 'manager2', 'manager3', 'tags']);

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
        $shop->save();

        return response()->json(['message' => '操作成功', 'code' => '1'], 201);
    }

    protected function rules(Request $request)
    {
        $rules = [
            'name' => 'bail|required|max:50',
            'shop_sn' => 'bail|required|unique:shops|max:10',
            'status_id' => 'bail|required|exists:shop_status,id',
            'department_id' => 'bail|exists:departments,id',
            'brand_id' => 'bail|exists:brands,id',
            'opening_at' => 'bail|required',
            'end_at' => 'bail|required|after_or_equal:'.$request->opening_at,
            'province_id' => 'bail|required',
            'city_id' => 'bail|required',
            'county_id' => 'bail|required',
            'address' => 'bail|max:50',
            'tags' => 'bail|array',
            'tags.*.id' => 'bail|exists:tags,id',
            'manager_sn' => 'bail|exists:staff,staff_sn',
            'manager_name' => 'bail|max:10',
            'manager1_sn' => 'bail|exists:staff,staff_sn',
            'manager1_name' => 'bail|max:10',
            'manager2_sn' => 'bail|exists:staff,staff_sn',
            'manager2_name' => 'bail|max:10',
            'manager3_sn' => 'bail|exists:staff,staff_sn',
            'manager3_name' => 'bail|max:10',
            'staff' => 'bail|array',
            'staff.*.staff_sn' => 'bail|exists:staff,staff_sn',
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
            'required' => ':attribute 为必填项，不能为空。',
            'unique' => ':attribute 已经存在，请重新填写。',
            'array' => ':attribute 数据格式错误。',
            'max' => ':attribute 不能大于 :max 个字。',
            'exists' => ':attribute 填写错误。',
            'after_or_equal' => ':attribute 不能小于 :values。',
        ];
    }
}
