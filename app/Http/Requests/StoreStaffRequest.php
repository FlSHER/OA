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
        $brand_id = $this->brand_id;
        return [
        	'realname' => 'required|between:2,10',
            'brand_id' => 'required|exists:brands,id',
            'department_id' => 'required|exists:departments,id,deleted_at,NULL',
            'position_id' => 'required|exists:positions,id,deleted_at,NULL',
            'property' => 'in:0,1,2,3,4',
            'gender' => 'required|in:男,女',
            'education' => 'exists:i_education,name',
            'national' => 'exists:i_national,name',
            'politics' => 'exists:i_politics,name',
            'shop_sn' => 'exists:shops,shop_sn,deleted_at,NULL|max:10',
            'status_id' => 'required|exists:staff_status,id',
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
            'remark' => 'max:100',
            'height' => 'integer|between:140,220',
            'weight' => 'integer|between:30,150',
            'operate_at' => 'required|date|after:2000-1-1|before:2038-1-1',
            'operation_remark' => 'max:100',
            'relatives.*.relatives_sn' => ['required_with:relatives_type,relative_name'],
            'relatives.*.relative_stype' => ['required_with:relatives_sn,relative_name'],
            'relatives.*.relative_nsame' => ['required_with:relative_tsype,relative_sn'],
            'tags' => 'array',
            'tags.*.id' => 'exists:tags,id',
            'mobile' => ['required', 'cn_phone', unique_validator('staff', false)],
            'wechat_number' => ['between:6,20', unique_validator('staff', false)],
            'id_card_number' => ['required', 'ck_identity', unique_validator('staff', false)],
            'dingtalk_number' => ['max:50', unique_validator('staff', false)],
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