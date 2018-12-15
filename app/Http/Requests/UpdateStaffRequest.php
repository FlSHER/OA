<?php

namespace App\Http\Requests;

use App\Models\HR\CostBrand;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $brand_id = $this->brand_id;
        return [
            'staff_sn' => 'required|exists:staff,staff_sn,deleted_at,NULL',
        	'realname' => 'required|between:2,10',
            'brand_id' => 'exists:brands,id',
            'department_id' => 'exists:departments,id,deleted_at,NULL',
            'position_id' => 'exists:positions,id,deleted_at,NULL',
            'gender' => 'in:男,女',
            'remark' => 'max:100',
            'property' => 'in:0,1,2,3,4',
            'politics' => 'exists:i_politics,name',
            'shop_sn' => 'exists:shops,shop_sn,deleted_at,NULL|max:10',
            'education' => 'exists:i_education,name',
            'national' => 'exists:i_national,name',
            'marital_status' => 'exists:i_marital_status,name',
            'household_province_id' => 'exists:i_district,id',
            'household_city_id' => 'exists:i_district,id',
            'household_county_id' => 'exists:i_district,id',
            'household_address' => 'string|max:30',
            'living_province_id' => 'exists:i_district,id',
            'living_city_id' => 'exists:i_district,id',
            'living_county_id' => 'exists:i_district,id',
            'living_address' => 'string|max:30',
            'concat_name' => 'required|between:2,10',
            'concat_tel' => 'required|cn_phone',
            'concat_type' => 'required|max:5',
            'account_bank' => 'max:20',
            'account_name' => 'between:2,10',
            'account_number' => 'between:16,19',
            'height' => 'integer|between:140,220',
            'weight' => 'integer|between:30,150',
            'status_id' => 'exists:staff_status,id',
            'operate_at' => 'required|date|after:2000-1-1|before:2038-1-1',
            'operation_remark' => 'max:100',
            'relatives.*.relative_sn' => ['required_with:relative_type,relative_name'],
            'relatives.*.relative_type' => ['required_with:relative_sn,relative_name'],
            'relatives.*.relative_name' => ['required_with:relative_type,relative_sn'],
            'tags' => 'array',
            'tags.*.id' => 'exists:tags,id',
            'mobile' => ['required', 'cn_phone', unique_validator('staff')],
            'wechat_number' => ['between:6,20', unique_validator('staff')],
            'id_card_number' => ['required', 'ck_identity', unique_validator('staff')],
            'dingtalk_number' => ['max:50', unique_validator('staff')],
            'cost_brands' => [
                'required_with:brand_id',
                function ($attribute, $value, $fail) use ($brand_id) {
                    $brands = CostBrand::with('brands')->whereIn('id', $value)->get();
                    $brands->map(function ($item) use ($fail, $brand_id) {
                        if (! $item->brands->contains($brand_id)) {
                            $fail("{$item->name} 不是所属品牌的费用品牌");
                        }
                    });
                }
            ],
        ];
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
            'between' => ':attribute值 :input 不在 :min - :max 之间。',
            'required_with' => ':attribute不能为空。',
        ];
    }

}