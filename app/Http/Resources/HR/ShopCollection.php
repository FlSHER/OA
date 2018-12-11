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
                'full_address' => $this->makeShop($shop),
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
                'assistant_sn' => $shop->assistant_sn,
                'assistant_name' => $shop->assistant_name,
                'real_address' => $shop->real_address,
                'total_area' => $shop->total_area,
                'shop_type' => $shop->shop_type,
                'work_type' => $shop->work_type,
                'status_id' => $shop->status_id,
            ];
        })->toArray();
    }

    protected function makeShop($shop)
    {
        $address = \DB::table('i_district')
            ->whereIn('id', [
                $shop->province_id,
                $shop->city_id,
                $shop->county_id,
            ])
            ->pluck('name')->implode('-');

        return $address.' '.$shop->address;
    }
}
