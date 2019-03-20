<?php 

namespace App\Services;


class RelationService
{
    
    public function staffKeys()
    {
        return [
            'realname' => 'realname',
            'id_card_number' => 'id_card_number',
            'mobile' => 'mobile',
            'gender' => 'gender',
            'brand' => 'brand_id',
            'positions' => 'position_id',
            'department' => 'department_id',
            'cost_brand' => 'cost_brands',
            'status' => 'status_id',
            'property' => 'property',
            'shop.value' => 'shop_sn',
            'remark' => 'remark',
            'account_bank' => 'account_bank',
            'account_number' => 'account_number',
            'account_name' => 'account_name',
            'concat_name' => 'concat_name',
            'concat_tel' => 'concat_tel',
            'concat_type' => 'concat_type',
            'wechat_number' => 'wechat_number',
            'dingtalk_number' => 'dingtalk_number',
            'recruiter.value' => 'recruiter_sn',
            'recruiter.text' => 'recruiter_name',
            'recruiter_channel' => 'job_source',
            'staff_tags' => 'tags',
            'household.province_id' => 'household_province_id',
            'household.city_id' => 'household_city_id',
            'household.county_id' => 'household_county_id',
            'household.address' => 'household_address',
            'living_province_id' => 'living_province_id',
            'living_city_id' => 'living_city_id',
            'living_county_id' => 'living_county_id',
            'living_address' => 'living_address',
            'native_place' => 'native_place',
            'national' => 'national',
            'education' => 'education',
            'politics' => 'politics',
            'height' => 'height',
            'weiget' => 'weiget',
            'marital_status' => 'marital_status',
            'operate_at' => 'operate_at',
            'operation_remark' => 'operation_remark',
            'account_active' => '是',
        ];
    }
}