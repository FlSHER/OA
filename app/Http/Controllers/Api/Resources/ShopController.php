<?php

namespace App\Http\Controllers\Api\Resources;

use Validator;
use App\Models\HR\Shop;
use App\Models\HR\Staff;
use App\Models\HR\ShopStatu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\ShopCollection;
use Illuminate\Support\Facades\Log;

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
        $this->validate($request, $this->rules($request), $this->message());
        $data = $request->all();
        $shop->fill($data);

        $shop->getConnection()->transaction(function () use ($shop, $data) {
            $shop->save();
            $shop->createShopLog($shop->id);
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
        $this->validate($request, $this->rules($request), $this->message());
        $data = $request->all();
        $shop->fill($data);

        $shop->getConnection()->transaction(function () use ($shop, $data) {
            $shop->save();
            $shop->createShopLog($shop->id);
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
     * 获取店铺状态列表.
     * 
     * @return mixed
     */
    public function status()
    {
        $list = ShopStatu::get();

        return response()->json($list);
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

    /**
     * 从流程创建店铺档案.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function storeProcess(Request $request)
    {
        $data = $request->input('data', []);
        $original = $this->filterData($data, [
            'id' ,'run_id', 'location', 'shop_name', 'created_at', 'updated_at', 'deleted_at'
        ]);
        Log::info($data);
        if ($request->type == 'finish') {
            $params = array_merge($original, [
                'status_id' => 1,
                'name' => $data['shop_name'],
            ]);
            Log::info($params);
            $this->validateWithProcess($params);
            
            // return response()->json($result, 201);
        }

        // return response()->json(['status' => 0, 'msg' => '流程验证错误'], 422);
    }

    /**
     * 过滤回调数据。
     * 
     * @param  array $data
     * @param  array  $fields
     * @return array
     */
    protected function filterData($data, $fields = [])
    {
        return array_filter($data, function($k) use ($fields) {

            return !in_array($k, $fields);

        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * 库房创建店铺档案验证.
     * 
     * @param  [type] $value
     * @return mixed
     */
    public function validateWithProcess($value)
    {
        $rules = [
            'name' => 'bail|required|max:50',
            'shop_sn' => 'bail|required|unique:shops|max:10',
            'status_id' => 'bail|required|exists:shop_status,id',
            'department_id' => 'bail|exists:departments,id',
            'brand_id' => 'bail|exists:brands,id',
            'province_id' => 'bail|required|exists:i_district,id',
            'city_id' => 'bail|required|exists:i_district,id',
            'county_id' => 'bail|required|exists:i_district,id',
            'address' => 'bail|max:50',
        ];

        return Validator::make($value, $rules, $this->message())->validate();
    }

    /**
     * 店铺验证规则.
     * 
     * @param  Request $request
     * @return array
     */
    protected function rules(Request $request): array
    {
        $rules = [
            'name' => 'bail|required|max:50',
            'shop_sn' => 'bail|required|unique:shops|max:10',
            'status_id' => 'bail|required|exists:shop_status,id',
            'department_id' => 'bail|exists:departments,id',
            'brand_id' => 'bail|exists:brands,id',
            'opening_at' => 'bail|required',
            'end_at' => 'bail|required|after_or_equal:'.$request->opening_at,
            'province_id' => 'bail|required|exists:i_district,id',
            'city_id' => 'bail|required|exists:i_district,id',
            'county_id' => 'bail|required|exists:i_district,id',
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
                'shop_sn' => [
                    'required',
                    'max:10',
                    Rule::unique('shops')->ignore($request->id),
                ],
            ]);
        }

        return $rules;
    }

    /**
     * 店铺验证返回错误信息.
     * 
     * @return array
     */
    protected function message(): array
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
