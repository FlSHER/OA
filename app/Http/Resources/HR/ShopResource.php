<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\Resource;

class ShopResource extends Resource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'shop_sn' => $this->shop_sn,
            'name' => $this->name,
            'manager_sn' => $this->manager_sn,
            'manager_name' => $this->manager_name,
            'manager_mobile' => $this->manager->mobile ?? '',
            'department_id' => $this->department_id,
            'brand_id' => $this->brand_id,
            'lng' => $this->lng,
            'lat' => $this->lat,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'county_id' => $this->county_id,
            'address' => $this->address,
            'full_address' => $this->makeShop($this),
            'clock_out' => $this->clock_out,
            'clock_in' => $this->clock_in,
            'brand' => $this->brand->only('id', 'name'),
            'department' => $this->department->only('id', 'name', 'full_name', 'parent_id'),
            'staff' => $this->staff->map(function ($staff) {
                return [
                    'staff_sn' => $staff->staff_sn,
                    'realname' => $staff->realname,
                ];
            }),
            'tags' => $this->tags,
            'end_at' => $this->end_at,
            'opening_at' => $this->opening_at,
            'assistant_sn' => $this->assistant_sn,
            'assistant_name' => $this->assistant_name,
            'real_address' => $this->real_address,
            'total_area' => $this->total_area,
            'shop_type' => $this->shop_type,
            'work_type' => $this->work_type,
            'city_ratio' => $this->city_ratio,
            'staff_deploy' => $this->staff_deploy,
            'status_id' => $this->status_id,
        ];
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
