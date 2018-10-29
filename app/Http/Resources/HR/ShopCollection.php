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
                'id' => $shop->id,
                'shop_sn' => $shop->shop_sn,
                'name' => $shop->name,
                'manager_sn' => $shop->manager_sn,
                'manager_name' => $shop->manager_name,
                'manager_mobile' => $shop->manager->mobile ?? '',
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
                'brand' => $shop->brand->only('id', 'name'),
                'department' => $shop->department->only('id', 'name', 'full_name', 'parent_id'),
                'staff' => $shop->staff->map(function ($staff) {
                    return [
                        'staff_sn' => $staff->staff_sn,
                        'realname' => $staff->realname,
                    ];
                }),
                'tags' => $shop->tags,
                'end_at' => $shop->end_at,
                'opening_at' => $shop->opening_at,
                'manager1_sn' => $shop->manager1_sn,
                'manager1_name' => $shop->manager1_name,
                'manager2_sn' => $shop->manager2_sn,
                'manager2_name' => $shop->manager2_name,
                'manager3_sn' => $shop->manager3_sn,
                'manager3_name' => $shop->manager3_name,
                'status_id' => $shop->status_id,
            ];
        })->toArray();
    }
}
