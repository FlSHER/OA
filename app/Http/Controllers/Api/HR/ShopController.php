<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\Shop;
use App\Models\HR\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\ShopResource;
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
        $list = Shop::query()
            ->with(['manager', 'department', 'brand', 'staff', 'tags'])
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
        $this->validate($request, $this->rules($request), $this->message());
        $data = $request->all();
        $shop->fill($data);

        $shop->getConnection()->transaction(function () use ($shop, $data) {
            $shop->save();
            $this->position($shop);
            
            if (!empty($data['staff'])) {
                $staffSn = array_column($data['staff'], 'staff_sn');
                Staff::whereIn('staff_sn', $staffSn)->update(['shop_sn' => $shop->shop_sn]);
            }
            if (!empty($data['tags'])) {
                $shop->tags()->sync($data['tags']);
            }
        });

        $shop->load(['staff', 'brand', 'department', 'manager', 'tags']);

        return response()->json(new ShopResource($shop), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Shop $shop)
    {
        $shop->load(['manager', 'department', 'brand', 'staff', 'tags']);

        return response()->json(new ShopResource($shop), 200);
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
        $this->validate($request, $this->rules($request), $this->message());
        $data = $request->all();
        $shop->fill($data);

        $shop->getConnection()->transaction(function () use ($shop, $data) {
            $shop->save();
            $this->position($shop);

            $shop->tags()->sync($data['tags']);
            if (isset($data['staff'])) {
                $shop->staff()->update(['shop_sn' => '']);
                if (!empty($data['staff'])) {
                    $staffSn = array_column($data['staff'], 'staff_sn');
                    Staff::whereIn('staff_sn', $staffSn)->update(['shop_sn' => $shop->shop_sn]);
                }
            }
        });

        $shop->load(['staff', 'brand', 'department', 'manager', 'tags']);

        return response()->json(new ShopResource($shop), 201);
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
            $shop->tags()->detach();
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
    protected function position(Shop $shop)
    {
        if (!empty($shop->lat) && !empty($shop->lng)) {
            createRequest('/api/amap', 'post', [
                'shop_sn' => $shop->shop_sn,
                'shop_name' => $shop->name,
                'latitude' => $shop->lat,
                'longitude' => $shop->lng
            ]);
        }
    }

    protected function rules(Request $request)
    {
        $rules = [
            'name' => 'bail|required|max:50',
            'shop_sn' => 'bail|required|unique:shops|max:10',
            'status_id' => 'bail|required|exists:shop_status,id',
            'department_id' => 'bail|exists:departments,id',
            'brand_id' => 'bail|exists:brands,id',
            'opening_at' => 'bail|date_format:Y-m-d',
            'end_at' => 'bail|date_format:Y-m-d|after_or_equal:'.$request->opening_at,
            'province_id' => 'bail|required|exists:i_district,id',
            'city_id' => 'bail|required|exists:i_district,id',
            'county_id' => 'bail|required|exists:i_district,id',
            'address' => 'bail|max:50',
            'real_address' => 'bail|max:50',
            'tags' => 'bail|array',
            'tags.*.id' => 'bail|exists:tags,id',
            'manager_sn' => 'bail|exists:staff,staff_sn',
            'manager_name' => 'bail|max:10',
            'assistant_sn' => 'bail|exists:staff,staff_sn',
            'assistant_name' => 'bail|max:10',
            'staff' => 'bail|array',
            'staff.*.staff_sn' => 'bail|exists:staff,staff_sn',
        ];
        if (strtolower($request->getMethod()) === 'patch') {
            return array_merge($rules, [
                'shop_sn' => [
                    'required',
                    'max:10',
                    Rule::unique('shops')->ignore($request->shop_sn, 'shop_sn'),
                ],
            ]);
        }

        return $rules;
    }

    protected function message()
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
