<?php 

namespace App\Services\Workflow\Process;

use Validator;

class StaffEntry
{
	
	public function makeFillData(array $data): array
    {   
        $newData = ['operation_remark' => '', 'operation_type' => 'entry'];
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

    public function validator(array $value)
    {
        $rules = [
            'realname' => 'required|string|max:10',
            'brand_id' => 'required|exists:brands,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'mobile' => 'required|unique:staff,mobile|cn_phone',
            'id_card_number' => 'required|ck_identity',
            'property' => 'in:0,1,2,3,4',
            'gender' => 'required|in:未知,男,女',
            'education' => 'exists:i_education,id',
            'national' => 'exists:i_national,id',
            'politics' => 'exists:i_politics,id',
            'shop_sn' => 'exists:shops,shop_sn|max:10',
            'status_id' => 'required|exists:staff_status,id',
            'marital_status' => 'exists:i_marital_status,id',
            'household_province_id' => 'exists:i_district,id',
            'household_city_id' => 'exists:i_district,id',
            'household_county_id' => 'exists:i_district,id',
            'living_province_id' => 'exists:i_district,id',
            'living_city_id' => 'exists:i_district,id',
            'living_county_id' => 'exists:i_district,id',
            'household_address' => 'string|max:30',
            'living_address' => 'string|max:30',
            'concat_name' => 'required|max:10',
            'concat_tel' => 'required|cn_phone',
            'concat_type' => 'required|max:5',
            'dingtalk_number' => 'max:50',
            'account_bank' => 'max:20',
            'account_name' => 'max:10',
            'account_number' => 'between:16,19',
            'remark' => 'max:100',
            'height' => 'integer|between:140,220',
            'weight' => 'integer|between:30,150',
            'operate_at' => 'required|date',
            'operation_remark' => 'max:100',
            'relatives.*.relative_sn' => ['required_with:relative_type,relative_name'],
            'relatives.*.relative_type' => ['required_with:relative_sn,relative_name'],
            'relatives.*.relative_name' => ['required_with:relative_type,relative_sn'],
        ];

        return Validator::make($value, $rules, $this->message());
    }

    /**
     * 统一返回验证错误信息.
     * 
     * @return array
     */
    public function message(): array
    {
        return [
            'in' => ':attribute必须在【:values】中选择。',
            'max' => ':attribute不能大于 :max 个字。',
            'exists' => ':attribute填写错误。',
            'unique' => ':attribute已经存在，请重新填写。',
            'required' => ':attribute为必填项，不能为空。',
            'between' => ':attribute参数 :input 不在 :min - :max 之间。',
            'required_with' => ':attribute不能为空。',
            'date_format' => '时间格式错误',
        ];
    }
}