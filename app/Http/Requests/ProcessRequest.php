<?php

namespace App\Http\Requests;

use App\Models\HR\CostBrand;
use Illuminate\Foundation\Http\FormRequest;

class ProcessRequest extends FormRequest
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
        $operateType = $this->operation_type;
        $rules = $this->makeBasicValidator();

        if ($operateType === 'employ') {
            $rules = array_merge($rules, [
                'status_id' => 'required|in:2',
            ]);

        } elseif ($operateType === 'leave') {
            $rules = array_merge($rules, [
                'status_id' => 'required|in:-1,-2,-3,-4',
                'skip_leaving' => 'in:0,1',
            ]);

        } elseif ($operateType === 'transfer') {
            $rules = array_merge($rules, [
                'brand_id' => 'required|exists:brands,id',
                'status_id' => 'required|exists:staff_status,id',
                'shop_sn' => 'exists:shops,shop_sn,deleted_at,NULL|max:10',
                'position_id' => 'required|exists:positions,id,deleted_at,NULL',
                'department_id' => 'required|exists:departments,id,deleted_at,NULL',
                'cost_brands' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $brands = CostBrand::with('brands')->whereIn('id', $value)->get();
                        $brand = $brands->map(function ($item) {
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

        return $rules;
    }

    /**
     * basic rules.
     * 
     * @return array
     */
    protected function makeBasicValidator(): array
    {
        return [
            'staff_sn' => 'required|exists:staff,staff_sn,deleted_at,NULL',
            'operate_at' => 'required|date_format:Y-m-d|after:2018-1-1|before:2038-1-1',
            'operation_type' => 'required|in:entry,employ,transfer,leave,active,leaving',
            'operation_remark' => 'max:100',
        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    protected function message(): array
    {
        return [
            'required' => ':attribute为必填项，不能为空。',
            'unique' => ':attribute已经存在，请重新填写。',
            'in' => ':attribute必须在【:values】中选择。',
            'max' => ':attribute不能大于 :max 个字。',
            'exists' => ':attribute填写错误。',
            'date_format' => '时间格式错误',
        ];
    }
}
