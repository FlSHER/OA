<?php

namespace App\Http\Resources;

use App\Http\Resources\HR\StaffRelativeCollection;
use Illuminate\Http\Resources\Json\Resource;

class CurrentUserResource extends Resource
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
            'staff_sn' => $this->staff_sn,
            'realname' => $this->realname,
            'mobile' => $this->mobile,
            'brand' => $this->brand->only(['id', 'name']),
            'department' => $this->department->only([
                'id',
                'name',
                'full_name',
                'manager_sn',
                'manager_name',
                'parent_id',
                'parentIds',
                'childrenIds',
            ]),
            'shop' => $this->shop ? $this->shop->only(['shop_sn', 'name', 'manager_sn', 'manager_name']) : null,
            'position' => $this->position->only(['id', 'name', 'level']),
            'status' => $this->status->only(['id', 'name']),
            'property' => [
                'id' => $this->property,
                'name' => ['无', '109将', '36天罡', '24金刚', '18罗汉'][$this->property],
            ],
            'hired_at' => $this->hired_at,
            'employed_at' => $this->employed_at,
            'left_at' => $this->left_at,
            'gender' => $this->gender,
            'id_card_number' => $this->id_card_number,
            'dingtalk_number' => $this->dingtalk_number,
            'wechat_number' => $this->wechat_number,
            'roles' => $this->roles,
            'is_active' => $this->is_active,
            'authorities' => [
                'oa' => app('Authority')->getAuthoritiesByStaffSn($this->staff_sn),
                'available_brands' => app('Authority')->getAvailableBrandsByStaffSn($this->staff_sn),
                'available_departments' => app('Authority')->getAvailableDepartmentsByStaffSn($this->staff_sn),
                'available_shops' => app('Authority')->getAvailableShopsByStaffSn($this->staff_sn),
            ]
        ];
    }
}
