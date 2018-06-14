<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\Resource;

class StaffResource extends Resource
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
            'brand_id' => $this->brand_id,
            'brand' => $this->brand->only(['id', 'name']),
            'department_id' => $this->department_id,
            'department' => $this->department->only(['id', 'full_name', 'manager_sn', 'manager_name']),
            'position_id' => $this->position_id,
            'position' => $this->position->only(['id', 'name', 'level']),
            'shop_sn' => $this->shop_sn,
            'shop' => $this->shop ? $this->shop->only(['shop_sn', 'name', 'manager_sn', 'manager_name']) : null,
            'status_id' => $this->status_id,
            'status' => $this->status->only(['id', 'name']),
            'is_active' => $this->is_active,
            'property' => $this->property,
            'hired_at' => $this->hired_at,
            'employed_at' => $this->employed_at,
            'left_at' => $this->left_at,
            'gender_id' => $this->gender_id,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'id_card_number' => $this->info->id_card_number,
            'account_number' => $this->info->account_number,
            'account_bank' => $this->info->account_bank,
            'account_name' => $this->info->account_name,
            'account_active' => $this->info->account_active,
            'email' => $this->info->email,
            'dingtalk_number' => $this->dingding,
            'wechat_number' => $this->wechat_number,
            'qq_number' => $this->info->qq_number,
            'recruiter_sn' => $this->info->recruiter_sn,
            'recruiter_name' => $this->info->recruiter_name,
            'height' => $this->info->height,
            'weight' => $this->info->weight,
            'household_province_id' => $this->info->household_province_id,
            'household_province_name' => $this->info->household_province ? $this->info->household_province->name : '',
            'household_city_id' => $this->info->household_city_id,
            'household_city_name' => $this->info->household_city ? $this->info->household_city->name : '',
            'household_county_id' => $this->info->household_county_id,
            'household_county_name' => $this->info->household_county ? $this->info->household_county->name : '',
            'household_address' => $this->info->household_address,
            'living_province_id' => $this->info->living_province_id,
            'living_province_name' => $this->info->living_province ? $this->info->living_province->name : '',
            'living_city_id' => $this->info->living_city_id,
            'living_city_name' => $this->info->living_city ? $this->info->living_city->name : '',
            'living_county_id' => $this->info->living_county_id,
            'living_county_name' => $this->info->living_county ? $this->info->living_county->name : '',
            'living_address' => $this->info->living_address,
            'native_place' => $this->info->native_place,
            'education' => $this->info->education,
            'national' => $this->info->national,
            'marital_status' => $this->info->marital_status,
            'politics' => $this->info->politics,
            'remark' => $this->info->remark,
            'concat_name' => $this->info->concat_name,
            'concat_tel' => $this->info->concat_tel,
            'concat_type' => $this->info->concat_type,
            'relatives' => new StaffRelativeCollection($this->relatives),
        ];
    }
}
