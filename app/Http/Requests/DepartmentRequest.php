<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'name' => ['required','unique:departments,name,NULL,name,deleted_at,NULL','regex:/^[\pL\pM\pN.]+$/','max:10'],
            // 'name' => 'required|unique:departments,name,NULL,name,deleted_at,NULL|max:10',
            'cate_id' => 'exists:department_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'manager_sn' => 'required_with:manager_name|exists:staff,staff_sn',
            'manager_name' => 'max:10',
            'minister_sn' => 'required_with:minister_name|exists:staff,staff_sn',
            'minister_name' => 'max:10',
            'area_manager_sn' => 'required_with:area_manager_name|exists:staff,staff_sn',
            'area_manager_name' => 'max:10',
            'personnel_manager_sn' => 'required_with:personnel_manager_name|exists:staff,staff_sn',
            'personnel_manager_name' => 'max:10',
            'regional_manager_sn' => 'required_with:regional_manager_name|exists:staff,staff_sn',
            'regional_manager_name' => 'max:10',
            'province_id' => 'exists:i_district,id,level,1',
        ];
        if ($this->getMethod() === 'PATCH') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    'regex:/^[\pL\pM\pN.]+$/u',
                    unique_validator('departments'),
                ],
            ]);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '部门名称',
            'cate_id' => '部门分类',
            'manager_sn' => '负责人',
            'manager_name' => '负责人',
            'minister_sn' => '部长',
            'minister_name' => '部长',
            'area_manager_sn' => '区域经理',
            'area_manager_name' => '区域经理',
            'regional_manager_sn' => '大区经理',
            'regional_manager_name' => '大区经理',
            'personnel_manager_sn' => '人事负责人',
            'personnel_manager_name' => '人事负责人',
        ];
    }
}
