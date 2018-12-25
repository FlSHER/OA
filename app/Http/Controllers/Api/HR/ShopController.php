<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\Shop;
use Illuminate\Http\Request;
use App\Services\ShopService;
use App\Http\Requests\ShopRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\ShopResource;
use App\Http\Resources\HR\ShopCollection;

class ShopController extends Controller
{
    protected $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

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
    public function store(ShopRequest $request, Shop $shop)
    {
        $shop = $this->shopService->index($request->all());
        $this->position($shop);

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
    public function update(ShopRequest $request, Shop $shop)
    {
        $shop = $this->shopService->index($request->all());
        $this->position($shop);

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

    /**
     * 导出店铺信息.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        $data = [];
        $hasAuth = app('Authority')->checkAuthority(196);
        $shops = Shop::query()
            ->with(['staff', 'department', 'brand', 'province', 'city', 'county', 'status', 'tags'])
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();

        $shops->map(function ($item, $key) use (&$data, $hasAuth) {

            // 基础数据
            $exportData = $this->makeExportBaseData($item);

            $data[$key] = $exportData;
        });

        return response()->json($data, 201);
    }

    /**
     * 组装导出基础数据。
     * 
     * @param  [type] $item
     * @return array
     */
    protected function makeExportBaseData($item)
    {
        $address = implode(' ', [
            $item->province->name ?? '',
            $item->city->name ?? '',
            $item->county->name ?? '',
        ]);

        return [
            'shop_sn' => $item->shop_sn,
            'name' => $item->name,
            'manager_sn' => $item->manager_sn ?: '',
            'manager_name' => $item->manager_name,
            'department' => $item->department->full_name,
            'area_manager_name' => $item->department->area_manager_name,
            'regional_manager_name' => $item->department->regional_manager_name,
            'personnel_manager_name' => $item->department->personnel_manager_name,
            'assistant_sn' => $item->assistant_sn,
            'assistant_name' => $item->assistant_name,
            'brand' => $item->brand->name,
            'address' => $address.' '.$item->address,
            'status' => $item->status->name,
            'clock_out' => $item->clock_out,
            'clock_in' => $item->clock_in,
            'opening_at' => $item->opening_at,
            'end_at' => $item->end_at,
            'total_area' => $item->total_area,
            'shop_type' => $item->shop_type,
            'work_type' => $item->work_type,
            'city_ratio' => $item->city_ratio,
            'staff_deploy' => $item->staff_deploy,
            'staff' => $item->staff->implode('realname', ','),
            'tags' => $item->tags->implode('name', ','),
        ];
    }
}
