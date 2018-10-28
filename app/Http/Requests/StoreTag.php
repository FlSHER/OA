<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTag extends FormRequest
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
            'name' => 'required|max:10|unique:tags',
            'tag_category_id' => 'required|exists:tag_categories,id',
        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => '标签名称必填',
            'name.max' => '标签名称过长',
            'name.unique' => '标签已经存在',
            'tag_category_id.required' => '标签分类必填',
            'tag_category_id.exists' => '标签分类不存在',
        ];
    }
}
