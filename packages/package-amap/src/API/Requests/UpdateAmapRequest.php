<?php

namespace Fisher\Amap\API\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAmapRequest extends FormRequest
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
            'shop_sn' => 'required|string|exists:around_amaps,shop_sn',
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
            'shop_sn.exists' => '更新的店铺不存在',
            'latitude.required' => '位置坐标不能为空',
            'longitude.required' => '位置坐标不能为空',
            'shop_name.required' => '店铺名不能为空',
        ];
    }
}