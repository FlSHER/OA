<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
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
    public function rules()
    {
    	$rules = [
            'name' => 'required|max:50',
            'shop_sn' => 'required|unique:shops,shop_sn,NULL,shop_sn,deleted_at,NULL|max:10',
            'department_id' => 'required|exists:departments,id,deleted_at,NULL',
            'brand_id' => 'required|exists:brands,id',
            'province_id' => 'required|exists:i_district,id,level,1',
            'city_id' => 'required|exists:i_district,id,level,2',
            'county_id' => 'required|exists:i_district,id,level,3',
            'address' => 'required|max:50',
            'status_id' => 'required|exists:shop_status,id',
            'clock_in' => ['regex:/^\d{2}:\d{2}$/'],
            'clock_out' => ['regex:/^\d{2}:\d{2}$/'],
            'opening_at' => 'required_with:end_at|date_format:Y-m-d',
            'end_at' => 'date_format:Y-m-d|after:opening_at',
            'manager_sn' => 'required_with:manager_name|exists:staff,staff_sn',
            'manager_name' => 'max:10',
            'assistant_sn' => 'required_with:assistant_name|exists:staff,staff_sn',
            'assistant_name' => 'max:10',
            'real_address' => 'max:50',
            'tags' => 'array',
            'tags.*.id' => 'exists:tags,id',
            'staff' => 'array',
            'staff.*.staff_sn' => 'exists:staff,staff_sn',
            'total_area' => ['regex:/^\d+(\.\d{1,2})?$/'],
            'shop_type' => Rule::in(['A', 'B1', 'B2', 'B3', 'C']),
            'work_type' => Rule::in(['全班', '倒班']),
            'city_ratio' => Rule::in(['0.8', '1', '1.2']),
            'staff_deploy' => 'numeric|between:1,99',
        ];
        if ($this->getMethod() === 'PATCH') {
			$rules = array_merge($rules, [
                'shop_sn' => 'required|exists:shops,shop_sn|max:10',
            ]);
        }

        return $rules;
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
