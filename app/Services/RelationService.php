<?php 

namespace App\Services;


class RelationService
{
    
    public function makeFillStaffData(array $data): array
    {   
        $newData = [];
        $keys = $this->staffWithKeys();
        foreach ($keys as $key => $withKey) {
            if (empty($data[$key])) continue;
            switch ($key) {
                case 'account_active':
                    $trans = ['否' => 0, '是' => 1];
                    $newData[$withKey] = $trans[$data[$key]];
                    break;
                case 'shop':
                    $newData[$withKey] = $data[$key]['value'];
                    break;
                case 'recruiter':
                    $newData[$withKey] = $data[$key]['value'];
                    $newData['recruiter_name'] = $data[$key]['text'];
                    break;
                case 'privy':
                    $relatives = [];
                    foreach ($data[$key] as $key => $item) {
                        $relatives[] = [
                            'relative_type' => $item['type'],
                            'relative_name' => $item['name']['text'],
                            'relative_sn' => $item['name']['value'],
                        ];
                    }
                    $newData[$withKey] = $relatives;
                    break;
                default:
                    $newData[$withKey] = $data[$key];
                    break;
            }
        }
        return $newData;
    }

    // 员工入职字段映射
    public function staffWithKeys(): array
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
            'remark' => 'remark',
            'account_bank' => 'account_bank',
            'account_number' => 'account_number',
            'account_name' => 'account_name',
            'concat_name' => 'concat_name',
            'concat_tel' => 'concat_tel',
            'concat_type' => 'concat_type',
            'wechat_number' => 'wechat_number',
            'dingtalk_number' => 'dingtalk_number',
            'shop' => 'shop_sn',
            'recruiter' => 'recruiter_sn',
            'recruiter_channel' => 'job_source',
            'staff_tags' => 'tags',
            'household_province_id' => 'household_province_id',
            'household_city_id' => 'household_city_id',
            'household_county_id' => 'household_county_id',
            'household_address' => 'household_address',
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
            'account_active' => 'account_active',
            'privy' => 'relatives',
        ];
    }

    public function res()
    {
        return [
          'realname' => '测试入职',
          'id_card_number' => '360521200101019999',
          'mobile' => '13882143092',
          'gender' => '男',
          'brand' => '12',
          'positions' => '23',
          'cost_brand' => ['4'],
          'department' => '10',
          'status' => '1',
          'property' => '0',
          'shop' => array (
              'value' => 116880,
              'text' => '庞敏',
            ),
          'remark' => '重新入职的人员',
          'account_bank' => '农行',
          'account_number' => '111111111111111',
          'account_name' => '看看',
          'account_active' => '是',
          'concat_name' => '王小二',
          'concat_tel' => '13882143092',
          'concat_type' => '朋友',
          'wechat_number' => '',
          'dingtalk_number' => '',
          'recruiter' =>
            array (
              'value' => 116880,
              'text' => '庞敏',
            ),
          'recruiter_channel' => '拉勾网',
          'staff_tags' => ['1', '3'],
          'household_province_id' => '110000',
          'household_city_id' => '110100',
          'household_county_id' => '110101',
          'household_address' => '哇哈哈',
          'living_province_id' => '220000',
          'living_city_id' => '220400',
          'living_county_id' => '220403',
          'living_address' => 'jiedao',
          'native_place' => '四川',
          'national' => '0',
          'education' => '0',
          'politics' => '1',
          'height' => '55',
          'weiget' => '67',
          'marital_status' => '0',
          'operate_at' => '2019-03-20',
          'operation_remark' => '',
          'privy' =>
          array (
            0 =>
            array (
              'id' => 1,
              'run_id' => 41,
              'data_id' => 1,
              'name' =>
              array (
                'value' => 121833,
                'text' => '张卫',
              ),
              'type' => '11',
              'created_at' => NULL,
              'updated_at' => NULL,
              'deleted_at' => NULL,
            ),
          )
        ];
    }
}