<?php

namespace App\Http\Requests;

use App\Models\HR\Staff;
use App\Models\HR\CostBrand;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $staffSn = $this->staff_sn;
        $brandId = $this->brand_id;
        $departmentId = $this->department_id;
        $staff = Staff::visible()->find($staffSn);
        $authority = app('Authority');

        if (!empty($staffSn) && empty($staff)) {

            return false;
        }
        if (!empty($departmentId) && !$authority->checkDepartment($departmentId)) {

            return false;
        }
        if (!empty($brandId) && !$authority->checkBrand($brandId)) {

            return false;
        }

        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_collapse([
            $this->makeRequiredValidator(),
            $this->makeBasicValidator(),
        ]);
    }

    /**
     * 必填项验证规则.
     * 
     * @return rules
     */
    public function makeRequiredValidator(): array
    {
        $operateType = $this->operation_type;
        $validator  = [
            'realname' => 'required|between:2,10',
            'gender' => 'required|in:男,女',
            'concat_name' => 'required|between:2,10',
            'concat_tel' => 'required|cn_phone',
            'concat_type' => 'required|max:5',
            'operate_at' => 'required|date|after:2000-1-1|before:2038-1-1',
            'mobile' => ['required', 'cn_phone', unique_validator('staff')],
            'id_card_number' => ['required', 'ck_identity', unique_validator('staff')],
        ];
        if (in_array($operateType, ['entry', 'reinstate'])) {
            $validator  = array_merge($validator, [
                'brand_id' => 'required|exists:brands,id',
                'status_id' => 'required|exists:staff_status,id',
                'position_id' => 'required|exists:positions,id,deleted_at,NULL',
                'department_id' => 'required|exists:departments,id,deleted_at,NULL',
                'mobile' => ['required', 'cn_phone', unique_validator('staff', false)],
                'id_card_number' => ['required', 'ck_identity', unique_validator('staff', false)],
                'cost_brands' => ['required', 'array',
                    function ($attribute, $value, $fail) {
                        $brands = CostBrand::with('brands')->whereIn('id', $value)->get();
                        $brand = $brands->map(function ($item) use ($fail) {
                            if (! $item->brands->contains($this->brand_id)) {
                                return $item->name;
                            }
                        })->filter();
                        if ($brand->isNotEmpty()) {
                            $fail("“{$brand->implode('，')}” 不是所属品牌的费用品牌");
                        }
                    }
                ],
            ]);
        }
        if (in_array($operateType, ['edit', 'reinstate'])) {
            $validator = array_merge($validator, [
                'staff_sn' => 'required|exists:staff,staff_sn,deleted_at,NULL',
            ]);

        }
        if ($operateType === 'reinstate') {
            $validator = array_merge($validator, [
                'mobile' => ['required', 'cn_phone', unique_validator('staff')],
                'id_card_number' => ['required', 'ck_identity', unique_validator('staff')],
            ]);
        }

        return $validator;
    }

    /**
     * 非必填基础资料验证规则.
     * 
     * @return rules
     */
    public function makeBasicValidator(): array
    {
        $operateType = $this->operation_type;

        $validator  = [
            'remark' => 'max:100',
            'job_source' => 'max:20',
            'property' => 'in:0,1,2,3,4',
            'operation_remark' => 'max:100',
            'account_bank' => 'max:20',
            'account_name' => 'between:2,10',
            'account_number' => 'between:16,19',
            'account_active' => 'in:0,1',
            'wechat_number' => ['between:6,20', unique_validator('staff')],
            'dingtalk_number' => ['max:50', unique_validator('staff')],
            'recruiter_sn' => 'exists:staff,staff_sn,deleted_at,NULL|max:10',
            'recruiter_name' => 'max:10',
            'tags' => 'array',
            'tags.*.id' => 'exists:tags,id',
            'household_province_id' => 'exists:i_district,id',
            'household_city_id' => 'exists:i_district,id',
            'household_county_id' => 'exists:i_district,id',
            'household_address' => 'string|max:30',
            'living_province_id' => 'exists:i_district,id',
            'living_city_id' => 'exists:i_district,id',
            'living_county_id' => 'exists:i_district,id',
            'living_address' => 'string|max:30',
            'marital_status' => 'exists:i_marital_status,name',
            'native_place' => 'string|max:30',
            'national' => 'exists:i_national,name',
            'education' => 'exists:i_education,name',
            'politics' => 'exists:i_politics,name',
            'height' => 'integer|between:140,220',
            'weight' => 'integer|between:30,150',
            'relatives.*.relative_sn' => ['required_with:relative_type,relative_name'],
            'relatives.*.relative_type' => ['required_with:relative_sn,relative_name'],
            'relatives.*.relative_name' => ['required_with:relative_type,relative_sn'],
        ];
        if ($operateType === 'entry') {
            $validator = array_merge($validator, [
                'shop_sn' => 'exists:shops,shop_sn,deleted_at,NULL|max:10',
                'dingtalk_number' => ['max:50', unique_validator('staff', false)],
                'wechat_number' => ['between:6,20', unique_validator('staff', false)],
            ]);

        } elseif ($operateType === 'reinstate') {
            $validator = array_merge($validator, [
                'shop_sn' => 'exists:shops,shop_sn,deleted_at,NULL|max:10',
            ]);
        }

        return $validator;
    }

    /**
     * Get rule messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'in' => ':attribute必须在【:values】中选择。',
            'max' => ':attribute不能大于 :max 个字。',
            'exists' => ':attribute填写错误。',
            'unique' => ':attribute已经存在，请重新填写。',
            'required' => ':attribute为必填项，不能为空。',
            'between' => ':attribute :input 不在 :min - :max 之间。',
            'required_with' => ':attribute不能为空。',
        ];
    }
    
}