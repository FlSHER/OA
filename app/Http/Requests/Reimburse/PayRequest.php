<?php

namespace App\Http\Requests\Reimburse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayRequest extends FormRequest
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
        if(is_array($this->id)){
            //批量验证
            return [
                'id.*'=>[
                    'required',
                    Rule::exists('reimburse_mysql.reimbursements','id')
                        ->where('status_id', 6)
                        ->whereNull('deleted_at')
                ]
            ];
        }
        //单条验证
        return [
            'id' => [
                'required',
                Rule::exists('reimburse_mysql.reimbursements')
                    ->where('status_id', 6)
                    ->whereNull('deleted_at')
            ]
        ];
    }

    public function attributes()
    {
        return [
            'id'=>'报销单ID',
            'id.*'=>'报销单ID'
        ];
    }
}
