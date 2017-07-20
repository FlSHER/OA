<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute 必须为“是”',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => ':attribute 必须晚于 :date ',
    'alpha'                => ':attribute 只能包含字母',
    'alpha_dash'           => ':attribute 只能包含字母、数字和点',
    'alpha_num'            => ':attribute 只能包含字母和数字',
    'array'                => ':attribute 必须是数组',
    'before'               => ':attribute 必须早于 :date ',
    'between'              => [
        'numeric' => ':attribute 必须介于 :min 和 :max 之间',
        'file'    => ':attribute 文件大小必须介于 :min K和 :max K之间',
        'string'  => ':attribute 长度必须介于 :min 和 :max 之间',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => ':attribute 必须为“是”或“否”',
    'confirmed'            => ':attribute 两次输入不一致',
    'date'                 => ':attribute 必须是日期格式',
    'date_format'          => ':attribute 必须是格式为 :format 的日期',
    'different'            => ':attribute 和 :other 不能相同',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => ':attribute 必须是电子邮件格式',
    'exists'               => ':attribute 记录不存在',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field is required.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => ':attribute 不存在',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => ':attribute 必须是整数',
    'ip'                   => ':attribute 必须是一个有效的IP地址',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => ':attribute 不能大于 :max ',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => ':attribute 长度不能大于 :max ',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => ':attribute 不能小于 :min ',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => ':attribute 长度不能小于 :min ',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => ':attribute 必须是数字.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => ':attribute 格式不正确',
    'required'             => ':attribute 不能为空',
    'required_if'          => ':attribute 不能为空',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => ':values 存在时， :attribute 不能为空',
    'required_with_all'    => ':values 存在时， :attribute 不能为空',
    'required_without'     => ':values 不存在时， :attribute 不能为空',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => ':attribute 和 :other 必须相同。',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => ':attribute 已经存在。',
    'url'                  => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
