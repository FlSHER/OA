<?php

/**
 * 字段翻译
 */
return [
    /* 员工管理 Start */
    'staff' => [
        'staff_sn' => '员工编号',
        'username' => '用户名',
        'password' => '密码',
        'realname' => '姓名',
        'mobile' => '手机号码',
        'wechat_number' => '微信号',
        'gender_id' => '性别ID',
        'gender.name' => '性别',
        'birthday' => '生日',
        'national_id' => '民族ID',
        'national.name' => '民族',
        'marital_status_id' => '婚姻状况ID',
        'marital_status.name' => '婚姻状况',
        'politics_id' => '政治面貌ID',
        'politics.name' => '政治面貌',
        'department_id' => '部门ID',
        'department.name' => '部门名称',
        'department.full_name' => '部门全称',
        'shop_sn' => '店铺代码',
        'shop.name' => '店铺名称',
        'position_id' => '职位ID',
        'position.name' => '职位',
        'brand_id' => '品牌ID',
        'brand.name' => '品牌',
        'dingding' => '钉钉用户编码',
        'is_active' => '是否激活',
        'status_id' => '员工状态ID',
        'status.name' => '员工状态',
        'hired_at' => '入职时间',
        'employed_at' => '转正时间',
        'left_at' => '离职时间',
        'info' => [
            'id_card_number' => '身份证号',
            'account_number' => '银行卡号',
            'account_name' => '开户人',
            'account_bank' => '开户行',
            'account_active' => '是否激活银行卡',
            'email' => '电子邮箱',
            'qq_number' => 'QQ号',
            'recruiter_sn' => '招聘人编号',
            'recruiter_name' => '招聘人姓名',
            'height' => '身高',
            'weight' => '体重',
            'household_province_id' => '户口所在地ID（省）',
            'household_province.name' => '户口所在地（省）',
            'household_city_id' => '户口所在地ID（市）',
            'household_city.name' => '户口所在地（市）',
            'household_county_id' => '户口所在地ID（区/县）',
            'household_county.name' => '户口所在地（区/县）',
            'household_address' => '户口所在地（详细地址）',
            'living_province_id' => '现居住地ID（省）',
            'living_province.name' => '现居住地（省）',
            'living_city_id' => '现居住地ID（市）',
            'living_city.name' => '现居住地（市）',
            'living_county_id' => '现居住地ID（区/县）',
            'living_county.name' => '现居住地（区/县）',
            'living_address' => '现居住地（详细地址）',
            'native_place' => '籍贯',
            'education' => '学历',
            'mini_shop_sn' => '微商城编码',
            'remark' => '备注',
            'concat_name' => '紧急联系人',
            'concat_tel' => '联系人电话',
            'concat_type' => '联系人关系类型',
        ],
        'relative' => '关系人',
        'relative.*' => [
            'relative_sn' => '关系人编号',
            'pivot' => [
                'relative_type' => '关系类型',
                'relative_name' => '关系人姓名',
            ],
        ],
        'operate_at' => '执行时间',
        'operation_type' => [// 操作类型
            'edit' => '编辑',
            'entry' => '入职',
            'import_entry' => '导入入职',
            'employ' => '转正',
            'transfer' => '人事变动',
            'import_transfer' => '导入变动',
            'leave' => '离职',
            'reinstate' => '再入职',
            'active' => '激活',
            'delete' => '删除',
            'leaving' => '离职交接'
        ],
        'operation_remark' => '操作备注',
    ],
    /* 员工管理 End */

    /* 店铺管理 Start */
    'shop' => [
        'shop_sn' => '店铺编号',
        'name' => '店铺名称',
        'department_id' => '所属部门ID',
        'brand_id' => '品牌ID',
        'province_id' => '省',
        'city_id' => '市',
        'county_id' => '区/县',
        'address' => '详细地址',
        'manager_sn' => '店长员工编号',
        'manager_name' => '店长姓名',
    ],
    /* 店铺管理 End */

    /* 店铺人员调动 Start */
    'transfer' => [
        'staff_sn' => '员工编号',
        'staff_name' => '员工姓名',
        'staff_gender' => '性别',
        'staff_department' => '部门名称',
        'current_shop_sn' => '当前店铺代码',
        'current_shop' => [
            'name' => '当前店铺名称',
            'province.name' => '当前店铺地址（省）',
            'city.name' => '当前店铺地址（市）',
            'county.name' => '当前店铺地址（区/县）',
            'address' => '当前店铺地址',
        ],
        'leaving_shop_sn' => '调离店铺代码',
        'leaving_shop' => [
            'name' => '调离店铺名称',
            'province.name' => '调离店铺地址（省）',
            'city.name' => '调离店铺地址（市）',
            'county.name' => '调离店铺地址（区/县）',
            'address' => '调离店铺地址',
        ],
        'arriving_shop_sn' => '到达店铺代码',
        'arriving_shop' => [
            'name' => '到达店铺名称',
            'province.name' => '到达店铺地址（省）',
            'city.name' => '到达店铺地址（市）',
            'county.name' => '到达店铺地址（区/县）',
            'address' => '到达店铺地址',
        ],
        'arriving_shop_duty' => '到店职务',
        'left_at' => '出发时间',
        'arrived_at' => '到达时间',
        'created_at' => '创建时间',
        'tag.*' => [
            'pivot.tag_id' => '标签ID',
            'name' => '标签',
        ],
        'maker_sn' => '建单人编号',
        'maker_name' => '建单人',
        'remark' => '备注',
    ],
    /* 店铺人员调动 End */

    /* ----------工作流start-------------- */
    'database_manage' => [//数据源管理
        'connection' => '数据库类型',
        'host' => 'IP地址',
        'port' => '端口',
        'database' => '数据源名称',
        'username' => '用户名',
        'password' => '密码',
    ],
    /* ---------------工作流end-------------- */
    /* -------------报销start---------------- */
    'reimburse' => [
        //审核
        'auditor' => [
            'name' => '资金归属',
            'auditor.*.auditor_staff_sn' => '审核人编号',
            'auditor.*.auditor_realname' => '审核人',
        ],
        //审批
        'approver' => [
            'department_id' => "部门",
            'reim_department_id' => "资金归属",
            'approver1' => '一级审批',
            'approver2' => '二级审批',
            'approver3' => '三级审批',
        ],
    ],
        /* ----------报销end--------------- */
];

