<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopCollection extends ResourceCollection
{
    public static $wrap = null;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($shop) {
            return [
                'shop_sn' => $shop->shop_sn,
                'name' => $shop->name,
                'manager_sn' => $shop->manager_sn,
                'manager_name' => $shop->manager_name,
                'department_id' => $shop->department_id,
                'brand_id' => $shop->brand_id,
                'lng' => $shop->lng,
                'lat' => $shop->lat,
                'province_id' => $shop->province_id,
                'city_id' => $shop->city_id,
                'county_id' => $shop->county_id,
                'address' => $shop->address,
                'clock_out' => $shop->clock_out,
                'clock_in' => $shop->clock_in,
                'staff' => $shop->staff->map(function ($staff) {
                    return [
                        'staff_sn' => $staff->staff_sn,
                        'realname' => $staff->realname,
                    ];
                }),
            ];
        })->toArray();
    }
}
