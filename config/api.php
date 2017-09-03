<?php

$reimbursePath = 'http://120.77.14.132:8001/';//报销
$crmPath = 'http://120.77.14.132:8003/';
$workflow = 'http://192.168.1.63:802/';
$transfer = 'http://192.168.1.117:8002/api/transfer/';  //调动
$holiday = 'http://192.168.1.117:8002/api/holiday/'; //请假
$attendance = 'http://192.168.1.117:8002/api';  //考勤
return [
    /*
     * 各系统接口地址
     */
    'url' => [
        'reimburse' => [
            'base' => $reimbursePath,
            'approverCache' => $reimbursePath . 'api/reimburse/approverCache', //审批人信息存入缓存
        ],
        'crm' => [
            'admin' => $crmPath . 'ad', //CRM管理后台
        ],
        'workflow' => [
            'formClassifySubmit' => $workflow . 'api/workflow/formClassifySubmit', //表单分类提交
            'formClassifySave' => $workflow . 'api/workflow/formClassifySave', //获取表单分类数据修改
            'formClassifyDelete' => $workflow . 'api/workflow/formClassifyDelete', //表单分类删除
            'formClassifyList' => $workflow . 'api/workflow/formClassifyList', //表单分类列表数据获取
            'formClassifyVeridateName' => $workflow . 'api/workflow/formClassifyVeridateName', //表单分类验证名称是否重复
            'flowClassifySubmit' => $workflow . 'api/workflow/flowClassifySubmit', //流程分类保存
            'flowClassifyList' => $workflow . 'api/workflow/flowClassifyList', //流程分类列表
            'flowClassifySave' => $workflow . 'api/workflow/flowClassifySave', //流程分类修改
            'flowClassifyDelete' => $workflow . 'api/workflow/flowClassifyDelete', //流程分类删除
            'flowClassifyVeridateName' => $workflow . 'api/workflow/flowClassifyVeridateName', //流程分类验证名称是否重复
            'flowConfigCreateFormClassifyList' => $workflow . 'api/workflow/flowConfigCreateFormClassifyList', //流程设置创建、修改，流程分类列表
            'flowFormSet' => $workflow . 'api/workflow/flowFormSet', //流程选择表单
            'flowConfigValidateName' => $workflow . 'api/workflow/flowConfigValidateName', //流程设置验证名称是否重复
            'flowConfigSubmit' => $workflow . 'api/workflow/flowConfigSubmit', //流程设置创建提交
            'flowLeftMenu' => $workflow . 'api/workflow/flowLeftMenu', //获取流程左侧树形菜单列表
            'flowLeftMenuSerach' => $workflow . 'api/workflow/flowLeftMenuSerach', //查询流程左侧树形菜单列表
            'flowAttribute' => $workflow . 'api/workflow/flowAttribute', //流程属性
            'flowConfigUpdateSave' => $workflow . 'api/workflow/flowConfigUpdateSave', //流程设置定义属性修改
            'formConfigCreateFormClassifyList' => $workflow . 'api/workflow/formConfigCreateFormClassifyList', //表单设置创建、修改，表单分类列表
            'formConfigSubmit' => $workflow . 'api/workflow/formConfigSubmit', //表单设置创建提交
            'formConfigList' => $workflow . 'api/workflow/formConfigList', //表单设置列表
            'formConfigSave' => $workflow . 'api/workflow/formConfigSave', //表单设置修改
            'formConfigDelete' => $workflow . 'api/workflow/formConfigDelete', //表单设置删除
            'formConfigValidateName' => $workflow . 'api/workflow/formConfigValidateName', //表单设置验证名称是否重复
            'formConfigExcelBlade' => $workflow . 'api/workflow/formConfigExcelBlade', //表单设置导出流程模板
            'formDesignSave' => $workflow . 'api/workflow/formDesignSave', //表单设计保存
            'formDesignUpdate' => $workflow . 'api/workflow/formDesignUpdate', //表单设计编辑  和表单设计列表预览
            'formDesignPhonePreview' => $workflow . 'api/workflow/formDesignPhonePreview', //移动设计预览
            'flowRunTableList' => $workflow . 'api/workflow/flowRunTableList', //运行流程table列表
            'flowTypeTableList' => $workflow . 'api/workflow/flowTypeTableList', //流程管理table列表
            'flowTypeRelease' => $workflow . 'api/workflow/flowTypeRelease', //流程管理 流程启用
            'flowTypeStop' => $workflow . 'api/workflow/flowTypeStop', //流程管理 流程停用
            'flowTypeDelete' => $workflow . 'api/workflow/flowTypeDelete', //流程管理 流程删除
            'releaseFlowTable' => $workflow . 'api/workflow/releaseFlowTable', //已发布流程table列表
            /* 设计流程步骤 */
            'deviseFlowSteps' => $workflow . 'api/workflow/deviseFlowSteps', //设计流程步骤
            'AddFlowStepsList' => $workflow . 'api/workflow/AddFlowStepsList', //新建步骤
            'flowDesignFormGetFormId' => $workflow . 'api/workflow/flowDesignFormGetFormId', //设计流程 得到预览表单的form_id
            'updateFlowSteps' => $workflow . 'api/workflow/updateFlowSteps', //编辑流程步骤
            'prcsIdRepetition' => $workflow . 'api/workflow/prcsIdRepetition', //验证步骤ID是否重复
            'stepsWritableTemplate' => $workflow . 'api/workflow/stepsWritableTemplate', //可写字段读取模板html内容
            'submitFlowSteps' => $workflow . 'api/workflow/submitFlowSteps', //流程步骤 保存
            'deleteFlowSteps' => $workflow . 'api/workflow/deleteFlowSteps', //流程步骤  删除
            'cloneFlowSteps' => $workflow . 'api/workflow/cloneFlowSteps', //流程步骤 克隆
            'checkMinFlowSteps' => $workflow . 'api/workflow/checkMinFlowSteps', //查询该流程的最小步骤
            'check_auto_type' => $workflow . 'api/workflow/check_auto_type', //智能选人 验证自动选人规则
            /* 管理设置 */
            'databaseManageList' => $workflow . 'api/workflow/databaseManageList', //数据源管理列表
            'databaseManageUpdateBefore' => $workflow . 'api/workflow/databaseManageUpdateBefore', //数据源管理编辑之前
            'databaseManageAdd' => $workflow . 'api/workflow/databaseManageAdd', //数据源管理保存新增,编辑
            'databaseManageDelete' => $workflow . 'api/workflow/databaseManageDelete', //数据源管理删除
        ],
        'transfer' => [//调动
            'list' => $transfer . 'list',
            'save' => $transfer . 'save',
            'edit' => $transfer . 'edit',
            'cancel' => $transfer . 'cancel',
        ],
        'holiday' => [//请假
            'list' => $holiday . 'list', //请假列表
            'cancel' => $holiday . 'cancel',
            'imports' => $holiday . 'imports', //导入
            'edit' => $holiday . 'edit',
        ],
        'attendance' => [//考勤
            'public' => preg_replace('/\/\w+?$/', '', $attendance),
            'stafflist' => $attendance . '/attendance/getstafflist', //获取店铺员工考勤数据 
        ],
        'statistic' => [//考勤
            'getlist' => $attendance . '/statistic/getlist', //获取员工考勤
            'export' => '/hr/attendance/export', //获取店铺员工考勤数据 
            'stafflist' => $attendance . '/statistic/getstatistic', //获取店铺员工考勤数据 
            'getstaffdetail' => $attendance . '/statistic/getstaffdetail', //获取店铺员工考勤数据 
        ],
    ],
];
