<?php

namespace App\Http\Requests;

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
        return [
        	'realname' => 'bail|required|string|max:10',
            'brand_id' => 'bail|required|exists:brands,id',
            'department_id' => 'bail|required|exists:departments,id',
            'position_id' => 'bail|required|exists:positions,id',
            'mobile' => 'bail|required|unique:staff,mobile|cn_phone',
            'id_card_number' => 'bail|required|ck_identity',
            'property' => 'bail|in:0,1,2,3,4',
            'gender' => 'bail|required|in:未知,男,女',
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
            'concat_name' => 'bail|max:10',
            'concat_tel' => 'bail|cn_phone',
            'concat_type' => 'bail|max:5',
            'dingtalk_number' => 'bail|max:50',
            'account_bank' => 'bail|max:20',
            'account_name' => 'bail|max:10',
            'account_number' => 'bail|between:16,19',
            'remark' => 'bail|max:100',
            'height' => 'bail|integer|between:140,220',
            'weight' => 'bail|integer|between:30,150',
            'operate_at' => 'bail|required|date',
            'operation_remark' => 'bail|max:100',
            'relative.*.relative_sn' => ['required_with:relative_type,relative_name'],
            'relative.*.relative_type' => ['required_with:relative_sn,relative_name'],
            'relative.*.relative_name' => ['required_with:relative_type,relative_sn'],
            // 'email' => 'bail|email',
            // 'birthday' => 'bail|date',
            // 'qq_number' => 'bail|integer',
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
            'in' => ':attribute 必须在【:values】中选择。',
            'max' => ':attribute 不能大于 :max 个字。',
            'exists' => ':attribute 填写错误。',
            'unique' => ':attribute 已经存在，请重新填写。',
            'required' => ':attribute 为必填项，不能为空。',
            'between' => ':attribute 值 :input 不在 :min - :max 之间。',
            'required_with' => ':attribute 不能为空。',
        ];
    }

}