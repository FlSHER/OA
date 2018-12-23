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
}
