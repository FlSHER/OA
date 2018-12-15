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
        	'realname' => 'bail|required|string|max:10',
            'brand_id' => 'bail|exists:brands,id',
            'department_id' => 'bail|exists:departments,id,deleted_at,NULL',
            'position_id' => 'bail|exists:positions,id,deleted_at,NULL',
            'gender' => 'bail|in:男,女',
            'remark' => 'bail|max:100',
            'property' => 'bail|in:0,1,2,3,4',
            'politics' => 'bail|exists:i_politics,name',
            'shop_sn' => 'bail|exists:shops,shop_sn|max:10',
            'education' => 'bail|exists:i_education,name',
            'national' => 'bail|exists:i_national,name',
            'marital_status' => 'bail|exists:i_marital_status,name',
            'household_province_id' => 'bail|exists:i_district,id',
            'household_city_id' => 'bail|exists:i_district,id',
            'household_county_id' => 'bail|exists:i_district,id',
            'household_address' => 'bail|string|max:30',
            'living_province_id' => 'bail|exists:i_district,id',
            'living_city_id' => 'bail|exists:i_district,id',
            'living_county_id' => 'bail|exists:i_district,id',
            'living_address' => 'bail|string|max:30',
            'concat_name' => 'bail|required|max:10',
            'concat_tel' => 'bail|required|cn_phone',
            'concat_type' => 'bail|required|max:5',
            'dingtalk_number' => 'bail|max:50',
            'account_bank' => 'bail|max:20',
            'account_name' => 'bail|max:10',
            'account_number' => 'bail|between:16,19',
            'height' => 'bail|integer|between:140,220',
            'weight' => 'bail|integer|between:30,150',
            'status_id' => 'bail|exists:staff_status,id',
            'operate_at' => 'bail|required|date',
            'operation_remark' => 'bail|max:100',
            'relatives.*.relative_sn' => ['required_with:relative_type,relative_name'],
            'relatives.*.relative_type' => ['required_with:relative_sn,relative_name'],
            'relatives.*.relative_name' => ['required_with:relative_type,relative_sn'],
            'tags' => 'bail|array',
            'tags.*.id' => 'bail|exists:tags,id',
            'mobile' => [
                'required',
                'cn_phone',
                Rule::unique('staff')->ignore($this->staff_sn, 'staff_sn')->where(function ($query) {
                    $query->whereNotNull('deleted_at');
                }),
            ],
            'id_card_number' => [
                'required',
                'ck_identity',
                Rule::unique('staff')->ignore($this->staff_sn, 'staff_sn')->where(function ($query) {
                    $query->whereNotNull('deleted_at');
                }),
            ],
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