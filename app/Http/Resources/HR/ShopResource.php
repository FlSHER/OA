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
            'manager1_sn' => $this->manager1_sn,
            'manager1_name' => $this->manager1_name,
            'manager2_sn' => $this->manager2_sn,
            'manager2_name' => $this->manager2_name,
            'manager3_sn' => $this->manager3_sn,
            'manager3_name' => $this->manager3_name,
            'status_id' => $this->status_id,
        ];
    }
}
