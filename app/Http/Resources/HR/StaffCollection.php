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
                'brand_id' => $staff->brand_id,
                'brand' => $staff->brand->only(['id', 'name']),
                'department_id' => $staff->department_id,
                'department' => $staff->department->only(['id', 'full_name', 'manager_sn', 'manager_name']),
                'position_id' => $staff->position_id,
                'position' => $staff->position->only(['id', 'name', 'level']),
                'shop_sn' => $staff->shop_sn,
                'shop' => $staff->shop ? $staff->shop->only(['shop_sn', 'name', 'manager_sn', 'manager_name']) : null,
                'status_id' => $staff->status_id,
                'status' => $staff->status->only(['id', 'name']),
                'hired_at' => $staff->hired_at,
                'employed_at' => $staff->employed_at,
                'left_at' => $staff->left_at,
                'gender_id' => $staff->gender_id,
//                'gender' => $staff->gender,
                'birthday' => $staff->birthday,
                'property' => $staff->property,
            ];
        })->toArray();
    }
}
