<!--智能选人-->
<!--steps_intelligent-->
<div class="tab-pane" id="intelligent">
    <div class="flow_top_type clearfix">
        <div style="float:left"><span class="icon20-intelligent" id="titleintelligent">设置智能选人</span><span class="icon20-intelligent" id="titlesoftintelligent" style="display: none;">设置智能选人(柔性节点)</span></div>
       
        <div class="step_name_intro">
            <span class="flow_intro">步骤名称:</span>
            <span class="flow_name">{{$data['prcs_name'] or ''}}</span>

        </div>
    </div>
    <div class="f_field_block" style="clear:both;">
        <div class="select_main_top">
            <div class="f_field_label">
                <span class="f_field_title">选人过滤规则</span>
            </div>
            <div class="f_field_ctrl">
                <select name="user_filter" id="user_filter" onchange="filter_auto_set()">
                    <option value="" selected="">允许选择全部指定的经办人</option>
                    @if(isset($data))
                    <!--编辑-->
                    <option value="1"@if($data['user_filter'] == 1) selected @endif>只允许选择本部门经办人</option>
                    <option value="8" @if($data['user_filter'] == 8) selected @endif >只允许选择本辅助部门经办人</option>
                    <option value="5" @if($data['user_filter'] == 5) selected @endif >只允许选择同级部门经办人</option>
                    <option value="3" @if($data['user_filter'] == 3) selected @endif >只允许选择上级部门经办人</option>
                    <option value="4" @if($data['user_filter'] == 4) selected @endif >只允许选择下级部门经办人</option>
                    <option value="12" @if($data['user_filter'] == 12) selected @endif >只允许选择本部门和下级部门经办人</option>
                    <option value="6" @if($data['user_filter'] == 6) selected @endif >只允许选择指定部门经办人</option>
                    <option value="9" @if($data['user_filter'] == 9) selected @endif >只允许选择指定辅助部门经办人</option>
                    <option value="2" @if($data['user_filter'] == 2) selected @endif >只允许选择本角色经办人</option>
                    <option value="10" @if($data['user_filter'] == 10) selected @endif >只允许选择本辅助角色经办人</option>
                    <option value="7" @if($data['user_filter'] == 7) selected @endif >只允许选择指定角色经办人</option>	
                    <option value="11" @if($data['user_filter'] == 11) selected @endif >只允许选择指定辅助角色经办人</option>
                    @else
                    <!--新增-->
                    <option value="1">只允许选择本部门经办人</option>
                    <option value="8">只允许选择本辅助部门经办人</option>
                    <option value="5">只允许选择同级部门经办人</option>
                    <option value="3">只允许选择上级部门经办人</option>
                    <option value="4" >只允许选择下级部门经办人</option>
                    <option value="12" >只允许选择本部门和下级部门经办人</option>
                    <option value="6">只允许选择指定部门经办人</option>
                    <option value="9">只允许选择指定辅助部门经办人</option>
                    <option value="2" >只允许选择本角色经办人</option>
                    <option value="10">只允许选择本辅助角色经办人</option>
                    <option value="7">只允许选择指定角色经办人</option>	
                    <option value="11">只允许选择指定辅助角色经办人</option>
                    @endif
                </select>
<!--                <div class="f_field_block" id="filter_dept" style="display:none;">
                    <div class="f_field_label"><span class="f_field_title">指定部门</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="FILTER_OPERATOR_TO_ID" id="F_FILTER_OPERATOR_TO_ID" value="">
                        <textarea style="width:500px;height:60px;" name="FILTER_OPERATOR_TO_NAME" wrap="yes" readonly=""></textarea>   
                        <a href="javascript:;" class="orgAdd" onclick="SelectDept('', 'FILTER_OPERATOR_TO_ID', 'FILTER_OPERATOR_TO_NAME', '', 'flow_step_define')">添加</a>
                        <a href="javascript:;" class="orgClear" onclick="ClearUser('FILTER_OPERATOR_TO_ID', 'FILTER_OPERATOR_TO_NAME')">清空</a>
                    </div>
                </div>
                <div class="f_field_block" id="filter_priv" style="display:none;">
                    <div class="f_field_label"><span class="f_field_title">指定角色</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="FILTER_PRIV_ID" id="F_FILTER_PRIV_ID" value="">
                        <textarea style="width:500px;height:60px;" name="FILTER_PRIV_NAME" wrap="yes" readonly=""></textarea>   
                        <a href="javascript:;" class="orgAdd" onclick="SelectPriv('', 'FILTER_PRIV_ID', 'FILTER_PRIV_NAME', '', 'flow_step_define')">添加</a>
                        <a href="javascript:;" class="orgClear" onclick="ClearUser('FILTER_PRIV_ID', 'FILTER_PRIV_NAME')">清空</a>
                    </div>
                </div>
                <div class="f_field_block" id="filter_assist_dept" style="display:none;">
                    <div class="f_field_label"><span class="f_field_title">指定辅助部门</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="FILTER_ASSIST_OPERATOR_TO_ID" id="F_FILTER_ASSIST_OPERATOR_TO_ID" value="">
                        <textarea style="width:500px;height:60px;" name="FILTER_ASSIST_OPERATOR_TO_NAME" wrap="yes" readonly=""></textarea>   
                        <a href="javascript:;" class="orgAdd" onclick="SelectDept('', 'FILTER_ASSIST_OPERATOR_TO_ID', 'FILTER_ASSIST_OPERATOR_TO_NAME', '', 'flow_step_define')">添加</a>
                        <a href="javascript:;" class="orgClear" onclick="ClearUser('FILTER_ASSIST_OPERATOR_TO_ID', 'FILTER_ASSIST_OPERATOR_TO_NAME')">清空</a>
                    </div>
                </div>
                <div class="f_field_block" id="filter_assist_priv" style="display:none;">
                    <div class="f_field_label"><span class="f_field_title">指定辅助角色</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="FILTER_ASSIST_PRIV_ID" id="F_FILTER_ASSIST_PRIV_ID" value="">
                        <textarea style="width:500px;height:60px;" name="FILTER_ASSIST_PRIV_NAME" wrap="yes" readonly=""></textarea>   
                        <a href="javascript:;" class="orgAdd" onclick="SelectPriv('', 'FILTER_ASSIST_PRIV_ID', 'FILTER_ASSIST_PRIV_NAME', '', 'flow_step_define')">添加</a>
                        <a href="javascript:;" class="orgClear" onclick="ClearUser('FILTER_ASSIST_PRIV_ID', 'FILTER_ASSIST_PRIV_NAME')">清空</a>
                    </div>
                </div>-->

                <div class="f_field_explain_label">
                    <span id="explain" class="f_field_explain_title_red">说明:选人过滤规则在流程转交选择经办人时生效，默认设置为允许选择全部指定的经办</span>
                </div>
            </div>
        </div>

    </div>
     <!--上半部分的样式--> 				 
    <div class="select_main_auto" id="select_body_auto">
        <div class="f_field_block clear">
            <div class="f_field_label">
                <span class="f_field_title">自动选择人规则</span>
            </div>
            <div class="f_field_ctrl">
                <select name="auto_type" id="auto_type" onchange="auto_set()">
                    <option value="" selected="">不进行自动选择</option>
                    @if(isset($data))
                    <!--编辑-->
                    <option value="1" @if($data['auto_type'] == 1) selected @endif>自动选择流程发起人</option>
                    <option value="2" @if($data['auto_type'] ==2) selected @endif>自动选择本部门主管</option>
                    @else
                    <!--新增-->
                    <option value="1">自动选择流程发起人</option>
                    <option value="2">自动选择本部门主管</option>
                    @endif
<!--                    <option value="12">自动选择指定部门主管</option>
                    <option value="9">自动选择本部门助理</option>
                    <option value="13">自动选择指定部门助理</option>
                    <option value="4">自动选择上级主管领导</option>
                    <option value="14">自动选择指定部门上级主管领导</option>
                    <option value="6">自动选择上级分管领导</option>
                    <option value="15">自动选择指定部门上级分管领导</option>
                    <option value="5">自动选择一级部门主管</option>
                    <option value="3">指定自动选择默认人员</option>
                    <option value="16">指定自动选择默认角色</option>
                    <option value="7">按表单字段选择</option>
                    <option value="8">自动选择指定步骤主办人</option>
                    <option value="10">自动选择本部门内符合条件所有人员</option>
                    <option value="11">自动选择本一级部门内符合条件所有人员</option>-->
                </select>
            </div>
        </div>

<!--        <div class="f_field_block" id="intelligent_base_user" style="display:none;">
            <div id="base_user" class="f_field_label">
                <span class="f_field_title">部门针对对象</span>
            </div>
            <div class="f_field_ctrl">
                <select name="AUTO_BASE_USER">
                    <option value="0">当前步骤</option>
                    <option value="1">部门申请</option>
                    <option value="2">上级主管审批</option>
                    <option value="3">人事部意见</option>
                    <option value="4">总经理意见</option>
                </select>
                <div class="f_field_explain_label">
                    <span id="explain" class="f_field_explain_title_green">默认设置为：针对当前步骤主办人</span>
                </div>
            </div>
        </div>
        <div class="f_field_block" id="intelligent_prcs_user" style="width:900px; display:none;">
            <div class="f_field_label">
                <span class="f_field_title">请指定步骤</span>
            </div>
            <div class="f_field_ctrl">
                <select name="AUTO_PRCS_USER">
                    <option value="1">部门申请</option>
                    <option value="2">上级主管审批</option>
                    <option value="3">人事部意见</option>
                    <option value="4">总经理意见</option>
                </select>
                <div class="f_field_explain_label">
                    <span id="explain" class="f_field_explain_title_green">将选择此步骤第一次办理时的主办人</span>
                </div>
            </div>
        </div>				-->
         <!--头部提醒的结束--> 
<!--        <div id="head1">
            <div id="intelligent_item_id_hide" class="f_field_block" style="display: none">
                <div class="f_field_label">
                    <span class="f_field_title">根据表单字段决定默认办理人(第一个作为主办人)</span>
                </div>
                 右侧页面控件左侧部分开始 
                <div class="plug_body_left">
                    <div class="list_title">已选表单字段</div>
                    <div class="list_infomation">
                        <table cellspacing="0px" cellpadding="0px" width="100%" id="intelligent_next_step_tab">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:10px;">
                        <button class="btn" id="intelligent_left_btn" type="button" onclick="intelligentSelectAll('left');" style="margin-left:100px;">全选</button>
                    </div>
                </div>
                 右侧页面控件左侧部分结束 
                 右侧页面控件中间部分开始 
                <div class="plug_body_center">
                    <div class="change_right">
                        <img src="/css/workflow/images/steps/right.png" id="intelligent_change_right" onclick="selectPlug('intelligent_next_step_tab', 'intelligent_alternative_tab', 'intelligent_left_btn');">
                    </div>
                    <div class="change_left">
                        <img src="/css/workflow/images/steps/left.png" id="intelligent_change_left" onclick="selectPlug('intelligent_alternative_tab', 'intelligent_next_step_tab', 'intelligent_right_btn');">
                    </div>
                </div>
                 右侧页面控件中间部分结束 
                 右侧页面控件右侧部分开始 
                <div class="plug_body_right">
                    <div class="list_title">备选表单字段</div>
                    <div class="list_infomation">
                        <table cellspacing="0px" cellpadding="0px" width="100%" id="intelligent_alternative_tab">
                            <tbody>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="2"><td class="step" style="padding-left: 20px;" name="申请日期">申请日期</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="3"><td class="step" style="padding-left: 20px;" name="申请人">申请人</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="4"><td class="step" style="padding-left: 20px;" name="部门">部门</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="5"><td class="step" style="padding-left: 20px;" name="招聘职位">招聘职位</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="6"><td class="step" style="padding-left: 20px;" name="招聘数量">招聘数量</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="7"><td class="step" style="padding-left: 20px;" name="到岗日期">到岗日期</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="8"><td class="step" style="padding-left: 20px;" name="薪资起">薪资起</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="9"><td class="step" style="padding-left: 20px;" name="薪资止">薪资止</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="10"><td class="step" style="padding-left: 20px;" name="招聘缘由">招聘缘由</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="11"><td class="step" style="padding-left: 20px;" name="职位性质">职位性质</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="12"><td class="step" style="padding-left: 20px;" name="工作经验">工作经验</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="13"><td class="step" style="padding-left: 20px;" name="大专">大专</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="14"><td class="step" style="padding-left: 20px;" name="本科">本科</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="15"><td class="step" style="padding-left: 20px;" name="硕士">硕士</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="16"><td class="step" style="padding-left: 20px;" name="博士">博士</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="17"><td class="step" style="padding-left: 20px;" name="其他">其他</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="18"><td class="step" style="padding-left: 20px;" name="其他要求">其他要求</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="19"><td class="step" style="padding-left: 20px;" name="职位描述">职位描述</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="20"><td class="step" style="padding-left: 20px;" name="职位要求">职位要求</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="21"><td class="step" style="padding-left: 20px;" name="上级主管审批">上级主管审批</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="22"><td class="step" style="padding-left: 20px;" name="上级主管意见">上级主管意见</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="23"><td class="step" style="padding-left: 20px;" name="上级主管签字">上级主管签字</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="24"><td class="step" style="padding-left: 20px;" name="人事部审批">人事部审批</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="25"><td class="step" style="padding-left: 20px;" name="人事部意见">人事部意见</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="26"><td class="step" style="padding-left: 20px;" name="人事部签字">人事部签字</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="27"><td class="step" style="padding-left: 20px;" name="总经理审批">总经理审批</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="28"><td class="step" style="padding-left: 20px;" name="总经理意见">总经理意见</td></tr>
                                <tr class="intelligent_alternative_step" onclick="selectTr(this, 'intelligent_next_step_tab', 'intelligent_alternative_tab', 'left_btn', 'intelligent_right_btn');" itemid="29"><td class="step" style="padding-left: 20px;" name="总经理签字">总经理签字</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:10px;">
                        <button class="btn" id="intelligent_right_btn" type="button" onclick="intelligentSelectAll('right');" style="margin-left:100px;">全选</button>
                    </div>
                </div>
                <input type="hidden" name="ITEM_ID">
                 右侧页面控件右侧部分结束
                <div style="clear:both;color:green;margin-top:15px;">点击条目时，可以组合CTRL键进行多选</div>
            </div>
        </div>-->
         <!--页面控件部分的结束--> 
<!--        <div id="intelligent_auto_dept_set" style="display:none">
            <div class="f_field_block">
                <div class="f_field_label"><span class="f_field_title">指定部门</span></div>
                <div class="f_field_ctrl">
                    <input type="hidden" name="AUTO_DEPT" value="">
                    <textarea style="width:500px;height:60px;" name="AUTO_DEPT_NAME" wrap="yes" readonly=""></textarea>
                    <a href="javascript:;" class="orgAdd" onclick="SelectDept('', 'AUTO_DEPT', 'AUTO_DEPT_NAME', '', 'flow_step_define')">添加</a>
                    <a href="javascript:;" class="orgClear" onclick="ClearUser('AUTO_DEPT', 'AUTO_DEPT_NAME')">清空</a>
                </div>
            </div>
        </div>
        <div id="intelligent_auto_prive_set" style="display:none">
            <div class="f_field_block">
                <div class="f_field_label"><span class="f_field_title">指定角色</span></div>
                <div class="f_field_ctrl">
                    <input type="hidden" name="AUTO_PRIVE" value="">
                    <textarea style="width:500px;height:60px;" name="AUTO_PRIVE_NAME" wrap="yes" readonly=""></textarea>
                    <a href="javascript:;" class="orgAdd" onclick="SelectPriv('', 'AUTO_PRIVE', 'AUTO_PRIVE_NAME', '', 'flow_step_define')">添加</a>
                    <a href="javascript:;" class="orgClear" onclick="ClearUser('AUTO_PRIVE', 'AUTO_PRIVE_NAME')">清空</a>
                </div>
            </div>
        </div>
        <div id="intelligent_auto_user_set" class="intelligent_auto_user_set" style="display:none">
            <div class="select_top_left">
                主办人				<input type="hidden" name="AUTO_USER_OP" value="">
                <input type="text" name="AUTO_USER_OP_NAME" value="" style="width:120px;" class="SmallStatic" readonly="">
                <font color="red">主办人是某步骤的负责人，只允许主办人编辑表单、公共附件和转交流程</font><br>
            </div>
            <div class="select_top_left change_left">
                经办人				<input type="hidden" name="AUTO_USER" value="">
                <textarea cols="40" name="AUTO_USER_NAME" rows="4" class="BigStatic span4" wrap="yes" readonly=""></textarea>
                <a href="javascript:;" class="orgAdd" onclick="LoadWindow()" title="指定经办人和主办人">指定经办/主办人</a>
                <a href="javascript:;" class="orgClear" onclick="ClearUser('AUTO_USER', 'AUTO_USER_NAME');ClearUser('AUTO_USER_OP', 'AUTO_USER_OP_NAME')" title="清空经办人和主办人">清空</a>
            </div>
        </div>
-->        <div class="f_field_explain_label">
            <span id="explain" class="f_field_explain_title_red">说明:通过自动选人规则，是流程经办人通过指定的规则智能选择。默认设置为:不能自动选择。注意，请同时设置好经办权限，自动选择规则才能生效</span>
        </div>		
    </div>

</div>