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
            'mobile' => 'bail|required|unique:staff,mobile|regex:/^1[3456789][0-9]{9}$/',
            'id_card_number' => 'bail|required|max:18',
            'gender_id' => 'bail|required|in:0,1,2',
            'remark' => 'bail|max:200',
            'operation_remark' => 'bail|max:100',
            'education' => 'bail|exists:i_education,name',
            'birthday' => 'bail|date',
            'national' => 'bail|exists:i_national,name',
            'politics' => 'bail|exists:i_politics,name',
            'property' => 'bail|in:0,1,2,3,4',
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
            'concat_tel' => 'bail|regex:/^1[3456789][0-9]{9}$/',
            'concat_type' => 'bail|max:5',
            'qq_number' => 'bail|integer',
            'dingding' => 'bail|max:50',
            'account_bank' => 'bail|max:20',
            'account_name' => 'bail|max:10',
            'account_number' => 'bail|between:16,19',
            'email' => 'bail|email',
            'height' => 'bail|integer|between:140,220',
            'weight' => 'bail|integer|between:30,150',
            'status_id' => 'bail|required|in:1,2,3,-1,-2,-3,-4',
            'operate_at' => 'bail|required|date',
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
			'realname.max' => '姓名长度不能超过 :max 个字',
            'realname.required' => '姓名不能为空',
            'brand_id.required' => '所属品牌不能为空',
            'brand_id.exists' => '所属品牌不存在',
            'department_id.required' => '所属部门不能为空',
            'department_id.exists' => '所属部门不存在',
            'position_id.required' => '职位不能为空',
            'position_id.exists' => '职位不存在',
            'mobile.required' => '手机号码不能为空',
            'mobile.unique' => '手机号码已经存在',
            'mobile.regex' => '手机号码不是一个有效的手机号',
            'id_card_number.required' => '身份证不能为空',
            'id_card_number.max' => '身份证号码无效',
            'gender_id.required' => '性别不能为空',
            'gender_id.in' => '性别填写错误',
            'remark.max' => '员工备注不能超过 :max 个字',
            'operation_remark.max' => '操作备注不能超过 :max 个字',
            'birthday.date' => '生日不是一个有效的日期',
            'national.exists' => '民族填写错误',
            'education.exists' => '学历填写错误',
            'politics.exists' => '政治面貌填写错误',
            'property.in' => '员工属性填写错误',
            'marital_status.exists' => '婚姻状态填写错误',
            'household_province.exists' => '户口所在地（省）不存在',
            'household_city.exists' => '户口所在地（市）不存在',
            'household_county.exists' => '户口所在地（区）不存在',
            'living_province.exists' => '现居地址（省）不存在',
            'living_city.exists' => '现居地址（市）不存在',
            'living_county.exists' => '现居地址（区）不存在',
            'concat_tel.regex' => '联系人电话不是有效电话号码',
            'account_number.between' => '银行卡号长度为 :min - :max 位数字',
            'email.email' => '电子邮箱格式错误',
            'height.between' => '身高输入范围为 :min - :max cm',
            'weight.between' => '体重输入范围为 :min - :max kg',
            'status_id.required' => '人员状态不能为空',
            'status_id.in' => '人员状态填写错误',
            'operate_at.required' => '执行时间不能为空',
        ];
    }

}