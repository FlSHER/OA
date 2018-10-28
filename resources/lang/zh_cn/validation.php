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

    'accepted'             => ':attribute 必须为“是”。',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => ':attribute 必须晚于 :date。',
    'after_or_equal'       => ':attribute 必须晚于或等于 :date。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母、数字和点。',
    'alpha_num'            => ':attribute 只能包含字母和数字。',
    'array'                => ':attribute 必须是数组。',
    'before'               => ':attribute 必须早于 :date。',
    'before_or_equal'      => ':attribute 必须早于或等于 :date。',
    'between'              => [
        'numeric' => ':attribute 必须介于 :min 和 :max 之间。',
        'file'    => ':attribute 文件大小必须介于 :minK 和 :maxK 之间。',
        'string'  => ':attribute 必须包含 :min 和 :max 个字符。',
        'array'   => ':attribute 必须包含 :min 到 :max 个元素。',
    ],
    'boolean'              => ':attribute 必须为“是”或“否”。',
    'confirmed'            => ':attribute 两次输入不一致。',
    'date'                 => ':attribute 必须是日期格式。',
    'date_format'          => ':attribute 必须是格式为 :format 的日期。',
    'different'            => ':attribute 和 :other 不能相同。',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => ':attribute 不可出现重复值。',
    'email'                => ':attribute 必须是电子邮件格式。',
    'exists'               => ':attribute 记录不存在。',
    'file'                 => ':attribute 必须是文件。',
    'filled'               => ':attribute 不能为空。',
    'image'                => ':attribute 必须是图片。',
    'in'                   => ':attribute 不存在。',
    'in_array'             => ':attribute 必须包含在 :other 中。',
    'integer'              => ':attribute 必须是整数。',
    'ip'                   => ':attribute 必须是一个有效的IP地址。',
    'ipv4'                 => ':attribute 必须是一个有效的IPv4地址。',
    'ipv6'                 => ':attribute 必须是一个有效的IPv6地址。',
    'json'                 => ':attribute 必须是json字符串。',
    'max'                  => [
        'numeric' => ':attribute 不能大于 :max 。',
        'file'    => ':attribute 文件大小不能大于 :maxK 。',
        'string'  => ':attribute 长度不能大于 :max 。',
        'array'   => ':attribute 不能包含超过 :max 个元素。',
    ],
    'mimes'                => ':attribute 必须是 :values 格式的文件。',
    'mimetypes'            => ':attribute 必须是 :values 格式的文件。',
    'min'                  => [
        'numeric' => ':attribute 不能小于 :min 。',
        'file'    => ':attribute 文件大小不能小于 :minK 。',
        'string'  => ':attribute 长度不能小于 :min 。',
        'array'   => ':attribute 必须包含 :min 个以上元素。',
    ],
    'not_in'               => ':attribute 禁止使用该值。',
    'numeric'              => ':attribute 必须是数字。',
    'present'              => ':attribute 必须存在。',
    'regex'                => ':attribute 格式不正确。',
    'required'             => ':attribute 不能为空。',
    'required_if'          => '当 :other 等于 :value 时，:attribute 不能为空。',
    'required_unless'      => '除非 :other 在 :values 中，:attribute 不能为空。',
    'required_with'        => ':values 存在时， :attribute 不能为空。',
    'required_with_all'    => ':values 都存在时， :attribute 不能为空。',
    'required_without'     => ':values 不存在时， :attribute 不能为空。',
    'required_without_all' => ':values 都不存在时， :attribute 不能为空。',
    'same'                 => ':attribute 和 :other 必须相同。',
    'size'                 => [
        'numeric' => ':attribute 必须等于 :size 。',
        'file'    => ':attribute 文件大小必须是 :sizeK。',
        'string'  => ':attribute 长度必须是 :size 。',
        'array'   => ':attribute 必须包含 :size 个元素。',
    ],
    'string'               => ':attribute 必须是字符串。',
    'timezone'             => ':attribute 必须是时区标识。',
    'unique'               => ':attribute 已经存在。',
    'uploaded'             => ':attribute 上传失败。',
    'url'                  => 'The :attribute format is invalid.',
    'cn_phone'             => ':attribute 必须是大陆地区合法手机号码',
    'ck_identity'          => ':attribute 不是一个有效的身份证号码',

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

    'attributes' => [
        'realname' => '姓名',
        'brand' => '品牌',
        'position' => '职位',
        'mobile' => '手机号',
        'id_card_number' => '身份证',
        'gender' => '性别',
        'property' => '员工属性',
        'status' => '员工状态',
        'national' => '民族',
        'education' => '学历',
        'politics' => '政治面貌',
        'marital_status' => '婚姻状况',
        'household_province' => '户口所在地（省）',
        'household_city' => '户口所在地（市）',
        'household_county' => '户口所在地（区/县）',
        'living_province' => '现居住地（省）',
        'living_city' => '现居住地（市）',
        'living_county' => '现居住地（区/县）',
        'household_address' => '户口所在地（详细地址）',
        'living_address' => '现居住地（详细地址）',
        'concat_name' => '紧急联系人',
        'concat_tel' => '联系人电话',
        'concat_type' => '联系人类型',
        'account_bank' => '开户人',
        'account_name' => '开户人',
        'account_number' => '银行卡号',
        'height' => '身高',
        'weight' => '体重',
        'dingtalk_number' => '钉钉编号',
        'remark' => '备注',
        'shop_sn' => '店铺编号',
        'status_id' => '员工状态',
        'department' => '部门',
        'cost_brand' => '费用品牌',
        'cost_brands' => '费用品牌',
        'operate_at' => '执行时间',
        'operation_type' => '操作类型',
        'operation_remark' => '执行备注',
        'relative_sn' => '关系人编号',
        'relative_type' => '关系人类型',
        'relative_name' => '关系人姓名',
    ],

];
