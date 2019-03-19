<?php 

namespace App\Services;


class RelationService
{
    
    public function staffKeys()
    {
        return [
            'realname' => 'realname',
            'id_card' => 'id_card_number',
            'mobile' => 'mobile',
            'sex' => 'gender',
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
            'native_place' => '四川',
            'nation' => '0',
            'education' => '0',
            'politics_status' => '1',
            'stature' => '55',
            'weiget' => '67',
            'marital_status' => '0',
            'execution_date' => '2019-03-19',
            'account_for' => '',
            'created_at' => 'null',
            'updated_at' => 'null',
            'deleted_at' => 'null',
            'account_active' => '是',
        ];
    }
}