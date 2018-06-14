<?php

namespace App\Http\Resources;

use App\Http\Resources\HR\StaffRelativeCollection;
use Illuminate\Http\Resources\Json\Resource;

class CurrentUserResource extends Resource
{
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
            'position' => $this->position->only(['id', 'name', 'level']),
            'shop' => $this->shop ? $this->shop->only([
                'shop_sn',
                'name',
                'manager_sn',
                'manager_name',
            ]) : null,
            'status' => $this->status->only(['id', 'name']),
            'property' => [
                'id' => $this->property,
                'name' => ['无', '108将', '36天罡', '24金刚', '18罗汉'][$this->property],
            ],
            'hired_at' => $this->hired_at,
            'employed_at' => $this->employed_at,
            'left_at' => $this->left_at,
            'gender' => $this->gender,
            'id_card_number' => $this->info->id_card_number,
            'email' => $this->info->email,
            'dingtalk_number' => $this->dingding,
            'wechat_number' => $this->wechat_number,
            'qq_number' => $this->info->qq_number,
            'authorities' => [
                'oa' => app('Authority')->getAvailableAuthorities(),
                'available_brands' => app('Authority')->getAvailableBrands(),
                'available_departments' => app('Authority')->getAvailableDepartments(),
                'available_shops' => app('Authority')->getAvailableShops(),
            ]
        ];
    }
}
