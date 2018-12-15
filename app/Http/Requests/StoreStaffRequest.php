<?php

namespace App\Http\Requests;

use App\Models\HR\CostBrand;
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
        	'realname' => 'bail|required|string|max:10',
            'brand_id' => 'bail|required|exists:brands,id',
            'department_id' => 'bail|required|exists:departments,id',
            'position_id' => 'bail|required|exists:positions,id',
            'mobile' => 'bail|required|unique:staff,mobile|cn_phone',
            'id_card_number' => 'bail|required|unique:staff,id_card_number|ck_identity',
            'property' => 'bail|in:0,1,2,3,4',
            'gender' => 'bail|required|in:男,女',
            'education' => 'bail|exists:i_education,name',
            'national' => 'bail|exists:i_national,name',
            'politics' => 'bail|exists:i_politics,name',
            'shop_sn' => 'bail|exists:shops,shop_sn|max:10',
            'status_id' => 'bail|required|exists:staff_status,id',
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
            'remark' => 'bail|max:100',
            'height' => 'bail|integer|between:140,220',
            'weight' => 'bail|integer|between:30,150',
            'operate_at' => 'bail|required|date',
            'operation_remark' => 'bail|max:100',
            'relatives.*.relatives_sn' => ['required_with:relatives_type,relative_name'],
            'relatives.*.relative_stype' => ['required_with:relatives_sn,relative_name'],
            'relatives.*.relative_nsame' => ['required_with:relative_tsype,relative_sn'],
            'tags' => 'bail|array',
            'tags.*.id' => 'bail|exists:tags,id',
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
            'between' => ':attribute :input 不在 :min - :max 之间。',
            'required_with' => ':attribute不能为空。',
        ];
    }
    
}