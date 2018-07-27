<?php

namespace Fisher\Amap\API\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmapRequest extends FormRequest
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
            'shop_sn' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'shop_name' => 'required'
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
            'shop_sn.required' => '店铺编号不能为空',
            'latitude.required' => '位置坐标不能为空',
            'longitude.required' => '位置坐标不能为空',
            'shop_name.required' => '店铺名不能为空',
        ];
    }
}