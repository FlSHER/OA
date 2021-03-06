<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffCollection extends ResourceCollection
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
        return $this->collection->map(function ($staff) {
            return [
                'staff_sn' => $staff->staff_sn,
                'realname' => $staff->realname,
                'mobile' => $staff->mobile,
                'gender' => $staff->gender,
                'brand_id' => $staff->brand_id,
                'department_id' => $staff->department_id,
                'position_id' => $staff->position_id,
                'shop_sn' => $staff->shop_sn,
                'status_id' => $staff->status_id,
                'is_active' => $staff->is_active,
                'hired_at' => $staff->hired_at,
                'employed_at' => $staff->employed_at,
                'left_at' => $staff->left_at,
                'property' => $staff->property,

                'shop' => $staff->shop ? $staff->shop->only(['shop_sn', 'name', 'manager_sn', 'manager_name']) : null,
                'cost_brands' => $staff->cost_brands,
                'department' => $staff->department->only(['id', 'full_name', 'manager_sn', 'manager_name']),
                'position' => $staff->position->only(['id', 'name', 'level']),
                'status' => $staff->status->only(['id', 'name']),
                'brand' => $staff->brand->only(['id', 'name']),
            ];

        })->toArray();
    }
}
