<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\Resource;

class StaffResource extends Resource
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
            'username' => $this->username,
            'gender' => $this->gender,
            'mobile' => $this->mobile,
            'brand_id' => (int) $this->brand_id,
            'department_id' => (int) $this->department_id,
            'position_id' => $this->position_id,
            'shop_sn' => $this->shop_sn,
            'status_id' => $this->status_id,
            'is_active' => (int) $this->is_active,
            'property' => $this->property,
            'hired_at' => $this->hired_at,
            'employed_at' => $this->employed_at,
            'left_at' => $this->left_at,
            'dingtalk_number' => $this->dingtalk_number,
            'wechat_number' => $this->wechat_number,

            'brand' => $this->brand->only(['id', 'name']),
            'status' => $this->status->only(['id', 'name']),
            'position' => $this->position->only(['id', 'name', 'level']),
            'relatives' => $this->relative ? new StaffRelativeCollection($this->relative) : [],
            'department' => $this->department->only(['id', 'full_name', 'manager_sn', 'manager_name']),
            'cost_brands' => $this->cost_brands,
            'shop' => $this->shop ? $this->shop->only(['shop_sn', 'name', 'manager_sn', 'manager_name']) : null,
            'household_province_name' => $this->household_province ? $this->household_province->name : '',
            'household_city_name' => $this->household_city ? $this->household_city->name : '',
            'household_county_name' => $this->household_county ? $this->household_county->name : '',
            'living_province_name' => $this->living_province ? $this->living_province->name : '',
            'living_city_name' => $this->living_city ? $this->living_city->name : '',
            'living_county_name' => $this->living_county ? $this->living_county->name : '',
            
            'oa' => $this->oa ?? [],
            'height' => $this->height,
            'weight' => $this->weight,
            'recruiter_sn' => $this->recruiter_sn,
            'recruiter_name' => $this->recruiter_name,
            'id_card_number' => $this->id_card_number,
            'account_number' => $this->account_number,
            'account_bank' => $this->account_bank,
            'account_name' => $this->account_name,
            'account_active' => (int) $this->account_active,
            'household_province_id' => $this->household_province_id,
            'household_city_id' => $this->household_city_id,
            'household_county_id' => $this->household_county_id,
            'household_address' => $this->household_address,
            'living_province_id' => $this->living_province_id,
            'living_city_id' => $this->living_city_id,
            'living_county_id' => $this->living_county_id,
            'living_address' => $this->living_address,
            'native_place' => $this->native_place,
            'education' => $this->education,
            'national' => $this->national,
            'marital_status' => $this->marital_status,
            'politics' => $this->politics,
            'remark' => $this->remark,
            'concat_name' => $this->concat_name,
            'concat_tel' => $this->concat_tel,
            'concat_type' => $this->concat_type,
        ];
    }
}
