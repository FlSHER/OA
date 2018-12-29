<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HrRoleRequest extends FormRequest
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
        $rules = [
            'name' => 'required|unique:hr_roles,name,NULL,name,deleted_at,NULL|max:10',
            'staff' => 'array',
            'brand' => 'array',
            'department' => 'array',
        ];
        if ($this->getMethod() === 'PATCH') {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    unique_validator('hr_roles'),
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
    public function attributes(): array
    {
        return [
            'name' => '角色名称',
            'staff' => '关联员工',
            'brand' => '关联品牌',
            'department' => '关联部门',
        ];
    }
}
