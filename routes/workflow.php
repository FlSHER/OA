<?php

/* -- 工作流 -- */

Route::group(['prefix' => 'workflow', 'namespace' => 'app\WorkFlow', 'as' => 'workflow'], function() {//工作流
    Route::get('/', ['uses' => 'HomeController@home'])->name('.home'); //首页运行流程
    Route::post('/flowRunTableList', ['uses' => 'HomeController@flowRunTableList'])->name('.flowRunTableList'); //流程运行table列表 

    Route::get('/formClassify', ['uses' => 'FormClassifyController@classifyList'])->name('.classify'); //分类列表
    Route::post('/formClassifyList', ['uses' => 'FormClassifyController@getFormClassifyList'])->name('.formClassify'); //获取表单分类列表
    Route::post('/formClassifySave', ['uses' => 'FormClassifyController@formClassifySave'])->name('.formClassifySave'); //表单分类修改
    Route::post('/formClassifySubmit', ['uses' => 'FormClassifyController@formClassifySubmit'])->name('.formClassifySubmit'); //表单分类数据提交
    Route::post('/formClassifyDelete', ['uses' => 'FormClassifyController@formClassifyDelete'])->name('.formClassifyDelete'); //表单分类删除
    Route::post('/formClassifyVeridateName', ['uses' => 'FormClassifyController@formClassifyVeridateName'])->name('.formClassifyVeridateName'); //表单分类验证名字是否重复

    Route::post('/flowClassifySubmit', ['uses' => 'FlowClassifyController@flowClassifySubmit'])->name('.flowClassifySubmit'); //流程分类提交
    Route::post('/flowClassifyList', ['uses' => 'FlowClassifyController@flowClassifyList'])->name('.flowClassifyList'); //流程分类列表
    Route::post('/flowClassifySave', ['uses' => 'FlowClassifyController@flowClassifySave'])->name('.flowClassifySave'); //流程分类修改
    Route::post('/flowClassifyDelete', ['uses' => 'FlowClassifyController@flowClassifyDelete'])->name('.flowClassifyDelete'); //流程分类删除
    Route::post('/flowClassifyValidateName', ['uses' => 'FlowClassifyController@flowClassifyValidateName'])->name('.flowClassifyValidateName'); //流程分类验证名称是否重复

    Route::get('/formConfigList', ['uses' => 'FormConfigController@formConfigList'])->name('.formConfigList'); //表单设置列表
    Route::post('/formConfigTableList', ['uses' => 'FormConfigController@formConfigTableList'])->name('.formConfigTableList'); //表单设置table列表
    Route::post('/formConfigCreate', ['uses' => 'FormConfigController@formConfigCreate'])->name('.formConfigCreate'); //表单设置创建
    Route::post('/formConfigSubmit', ['uses' => 'FormConfigController@formConfigSubmit'])->name('.formConfigSubmit'); //表单设置提交
    Route::post('/formConfigSave', ['uses' => 'FormConfigController@formConfigSave'])->name('.formConfigSave'); //表单设置修改
    Route::post('/formConfigDelete', ['uses' => 'FormConfigController@formConfigDelete'])->name('.formConfigDelete'); //表单设置删除
    Route::post('/formConfigValidateName', ['uses' => 'FormConfigController@formConfigValidateName'])->name('.formConfigValidateName'); //表单设置验证名称是否重复
    Route::get('/formConfigExcelBlade/{id?}', ['uses' => 'FormConfigController@formConfigExcelBlade'])->name('.formConfigExcelBlade'); //表单设置导出流程模板

    Route::get('/formDesignList/{id?}', ['uses' => 'FormDesignController@formDesignList'])->name('.formDesignList'); //表单设计列表
    Route::post('/formDesignSave', ['uses' => 'FormDesignController@formDesignSave'])->name('.formDesignSave'); //表单设计保存
    Route::post('/formDesignPreview', ['uses' => 'FormDesignController@formDesignPreview'])->name('.formDesignPreview'); //表单预览
    Route::post('/formDesignPhonePreview', ['uses' => 'FormDesignController@formDesignPhonePreview'])->name('.formDesignPhonePreview'); //移动设计
    Route::get('/getInternalDataTable', ['uses' => 'FormDesignController@getInternalDataTable'])->name('.getInternalDataTable'); //列表控件  获取内部数据来源表
    Route::get('/getInternalDataField', ['uses' => 'FormDesignController@getInternalDataField'])->name('.getInternalDataField'); //列表控件  获取内部数据来源表的字段
    Route::get('/dbConnectionInfo', ['uses' => 'FormDesignController@dbConnectionInfo'])->name('.dbConnectionInfo'); //列表控件  获取数据库数据类型数据
    Route::post('/optionalField', ['uses' => 'FormDesignController@optionalField'])->name('.optionalField'); //表单设计 预览表单 操作点击选择获取字段
    Route::get('/optionalFieldList', ['uses' => 'FormDesignController@optionalFieldList'])->name('.optionalFieldList'); //表单设计 预览表单 操作点击选择展示视图
    Route::post('/fieldsPage', ['uses' => 'FormDesignController@fieldsPage'])->name('.fieldsPage'); //表单设计 预览表单 列表控件 操作点击选择展示分页数据请求
    Route::post('/macrosUserInfoPage', ['uses' => 'FormDesignController@macrosUserInfoPage'])->name('.macrosUserInfoPage'); //表单设计 预览表单 宏控件人员列表下拉框加载更多

    Route::get('/flowConfigList', ['uses' => 'Flow\FlowConfigController@flowConfigList'])->name('.flowConfigList'); //工作流设置列表
    Route::post('/flowConfigCreate', ['uses' => 'Flow\FlowConfigController@flowConfigCreate'])->name('.flowConfigCreate'); //流程设置创建
    Route::post('/flowConfigSubmit', ['uses' => 'Flow\FlowConfigController@flowConfigSubmit'])->name('.flowConfigSubmit'); //流程设置提交
    Route::post('/flowConfigUpdateSave', ['uses' => 'Flow\FlowConfigController@flowConfigUpdateSave'])->name('.flowConfigUpdateSave'); //流程设置定义属性修改
    Route::post('/flowConfigValidateName', ['uses' => 'Flow\FlowConfigController@flowConfigValidateName'])->name('.flowConfigValidateName'); //流程设置验证名称是否重复
    Route::post('/flowFormSet', ['uses' => 'Flow\FlowConfigController@flowFormSet'])->name('.flowFormSet'); //流程选择表单
    Route::post('/flowLeftMenu', ['uses' => 'Flow\FlowConfigController@flowLeftMenu'])->name('.flowLeftMenu'); //获取流程左侧树形菜单列表
    Route::post('/flowLeftMenuSerach', ['uses' => 'Flow\FlowConfigController@flowLeftMenuSerach'])->name('.flowLeftMenuSerach'); //查询流程左侧树形菜单列表
    Route::get('/flowAttribute', ['uses' => 'Flow\FlowDataController@flowAttribute'])->name('.flowAttribute'); //流程属性  加白名单
    Route::get('/flowDesignPreview/{flow_id}', ['uses' => 'Flow\FlowDataController@flowDesignPreview'])->name('.flowDesignPreview'); //设计流程 预览表单
    

    Route::get('/flowTypeList', ['uses' => 'FlowTypeController@flowTypeList'])->name('.flowTypeList'); //流程管理列表
    Route::post('/flowTypeTableList', ['uses' => 'FlowTypeController@flowTypeTableList'])->name('.flowTypeTableList'); //流程管理table列表
    Route::post('/flowTypeRelease', ['uses' => 'FlowTypeController@flowTypeRelease'])->name('.flowTypeRelease'); //流程管理 流程启用
    Route::post('/flowTypeStop', ['uses' => 'FlowTypeController@flowTypeStop'])->name('.flowTypeStop'); //流程管理 流程停用
    Route::post('/flowTypeDelete', ['uses' => 'FlowTypeController@flowTypeDelete'])->name('.flowTypeDelete'); //流程管理 流程删除

    Route::get('/releaseFlow', ['uses' => 'ReleaseFlowController@releaseFlow'])->name('.releaseFlow'); //已发布流程
    Route::post('/releaseFlowTable', ['uses' => 'ReleaseFlowController@releaseFlowTable'])->name('.releaseFlowTable'); //已发布流程table列表
    /* --设计流程步骤-- */
    Route::get('/deviseFlowSteps', ['uses' => 'Flow\FlowStepsController@deviseFlowSteps'])->name('.deviseFlowSteps'); //设计流程步骤
    Route::get('/AddFlowStepsList', ['uses' => 'Flow\FlowStepsController@AddFlowStepsList'])->name('.AddFlowStepsList'); //流程步骤 新增
    Route::get('/updateFlowStepsList', ['uses' => 'Flow\FlowStepsController@updateFlowStepsList'])->name('.updateFlowStepsList'); //流程步骤 编辑
    Route::post('/prcsIdRepetition', ['uses' => 'Flow\FlowStepsController@prcsIdRepetition'])->name('.prcsIdRepetition'); //验证步骤ID是否重复
    Route::post('/stepsWritableTemplate', ['uses' => 'Flow\FlowStepsController@stepsWritableTemplate'])->name('.stepsWritableTemplate'); //可写字段读取模板html内容
    Route::post('/submitFlowSteps', ['uses' => 'Flow\FlowStepsController@submitFlowSteps'])->name('.submitFlowSteps'); //流程步骤 保存
    Route::post('/deleteFlowSteps', ['uses' => 'Flow\FlowStepsController@deleteFlowSteps'])->name('.deleteFlowSteps'); //流程步骤删除
    Route::post('/cloneFlowSteps', 'Flow\FlowStepsController@cloneFlowSteps')->name('.cloneFlowSteps'); //流程步骤 克隆
    Route::post('/check_auto_type','Flow\FlowStepsController@check_auto_type')->name('.check_auto_type');//智能选人验证自动选人规则
    //传阅
    Route::get('/passReadPerson', 'Flow\FlowConfigController@passReadPerson')->name('.passReadPerson');
    Route::get('/passReadDept', 'Flow\FlowConfigController@passReadDept')->name('.passReadDept');
    Route::get('/passReadRole', 'Flow\FlowConfigController@passReadRole')->name('.passReadRole');
    //管理设置
    Route::get('/databaseManage', ['uses' => 'DatabaseManageController@databaseManage'])->name('.databaseManage'); //数据源管理列表
    Route::post('/databaseManageAdd', ['uses' => 'DatabaseManageController@databaseManageAdd'])->name('.databaseManageAdd'); //数据源管理保存
    Route::post('/databaseManageUpdateBefore',['uses'=>'DatabaseManageController@databaseManageUpdateBefore'])->name('.databaseManageUpdateBefore');//数据源管理编辑获取数据
    Route::post('/databaseManageDelete',['uses'=>'DatabaseManageController@databaseManageDelete'])->name('.databaseManageDelete');//数据源管理 删除
    Route::post('/databaseTestCheck',['uses'=>'DatabaseManageController@databaseTestCheck'])->name('.databaseTestCheck');//数据源管理检测连接
});

