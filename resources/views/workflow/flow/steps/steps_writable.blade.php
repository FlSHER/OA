<!--可写字段-->
<!--steps_writable-->
<div class="tab-pane" id="writable">
    <link rel="stylesheet" type="text/css" href="/css/workflow/steps/new_flow.css">

    <div>
        <div id="head1">
            <!-- 头部的提醒开始 -->
            <div style="margin:0 auto;margin-bottom:10px;margin-top:10px;">
                <div class="flow_top_type clearfix">
                    <div style="float:left"><span class="icon20-writable">编辑可写字段</span></div>
                    <div class="step_name_intro">
                        <span class="flow_intro">步骤名称:</span>
                        <span class="flow_name">{{$data['prcs_name'] or ''}}</span>
                    </div>
                </div>
            </div>
            <!-- 头部提醒的结束 -->
            <div class="plug" style=" margin:0 auto;">
                <!-- 右侧页面控件左侧部分开始 -->
                <div class="plug_body_left" onselectstart="return false">
                    <div class="list_title">本步骤可写字段</div>
                    <div id="write_" style="display: none;margin: 0;padding: 0;"></div>
                    <div class="list_infomation ui-selectable" id="write_next_step_div">
                        <table cellspacing="0px" cellpadding="0px" width="100%" id="write_next_step_tab">
                            <tbody id="writable_left_tbody">
                                @if(isset($data['prcs_item'])&& !empty($data['prcs_item']))
                                @foreach($data['prcs_item'] as $k=>$v)
                                <tr style="cursor: pointer;">
                                    <td class="step"  name="{{$k}}">{{$v}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:10px;">
                        <button class="btn" id="write_left_btn" type="button" onclick="selectAll(this, 'writable_left_tbody');" style="margin-left:100px;">全选</button>
                    </div>	
                </div>
                <!-- 右侧页面控件左侧部分结束 -->
                <!-- 右侧页面控件中间部分开始 -->
                <div class="plug_body_center">
                    <div class="change_right">
                        <img src="{{asset('css/workflow/images/steps/right.png')}}" id="change_right" onclick="selectPlug(this, 'write_next_step_tab', 'write_alternative_tab', 'write_left_btn');">
                    </div>
                    <div class="change_left">
                        <img src="{{asset('css/workflow/images/steps/left.png')}}" id="change_left" onclick="selectPlug(this, 'write_alternative_tab', 'write_next_step_tab', 'write_right_btn');">
                    </div>
                </div>
                <!-- 右侧页面控件中间部分结束 -->
                <!-- 右侧页面控件右侧部分开始 -->
                <div class="plug_body_right" onselectstart="return false">
                    <div class="list_title">备选字段</div>
                    <div class="list_infomation ui-selectable" id="write_alternative_div">
                        <table cellspacing="0px" cellpadding="0px" width="100%" id="write_alternative_tab">
                            <tbody id="writable_right_tbody">
                                @if(isset($data['prcs_item_optional_field']) && !empty($data['prcs_item_optional_field']))
                                @foreach($data['prcs_item_optional_field'] as $k=>$v)
                                @if(isset($v['checkboxs']))
                                @foreach($v['checkboxs'] as $vk=>$vv)
                                <tr style="cursor: pointer;">
                                    <td class="step"  name="{{$vv['name']}}">{{$vv['value']}}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr style="cursor: pointer;">
                                    <td class="step"  name="{{$v['name']}}">{{$v['title']}}</td>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                            </tbody>			
                        </table>
                    </div>
                    <div style="margin-top:10px;">
                        <button class="btn" id="write_right_btn" type="button" onclick="selectAll(this, 'writable_right_tbody');" style="margin-left:100px;">全选</button>
                    </div>
                </div>
                <!-- 右侧页面控件右侧部分结束-->
                <div style="clear:both;color:green;margin-top:15px;margin-left:380px;">点击条目时，可以组合CTRL键进行多选</div>
            </div>
            <!-- 页面控件部分的结束 -->
        </div>
        <!-- 第一个模块结束 -->
        <!-- 第二个模块开始 -->
        <div>
            <div id="head2" style="clear:both;">
                <div style="margin-top:10px; margin:0 auto;margin-bottom:10px;">
                    <div class="flow_top_type clearfix">
                        <div class="float_left">
                            <span style="font-size:14px;font-weight:bold;">列表控件模式</span>
                        </div>
                    </div>
                </div>
                @if(isset($data))
                <!--编辑-->
                @if(count($data['listCtrl'])>0)
                <div style="display:none;" id="updateListCtrlData">{{$data['list_ctrl']}}</div>
                <div style="margin:0 auto;" id="list_item">
                    <style> 
                                        .black_overlay{  display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.8;  opacity:.80;  filter: alpha(opacity=80);  } 
                                        .white_content {  display: none;  position: absolute;  top: 25%;  left: 25%;  width: 50%;  height: 50%;  padding: 16px;  border: 16px solid orange;  background-color: white;  z-index:1002;  overflow: auto;  }  
                                    </style> 
                    <table class="TableList" width="550" style=" border: 1px #cccccc solid;">
                        <tbody>
                            @foreach($data['listCtrl'] as $k=>$v)
                            <tr style="border-bottom-style: solid;" class="tr_attr">
                                <td class="TableData" listCtrlName="{{$v['list_ctrl_name']}}" title="{{$v['title']}}">{{$v['title']}}</td>
                                <td class="TableData">	
                                    <div class="remind_checkbox" style="margin-top:0px" >
                                        <!--                                        <label class="checkbox inline">
                                                                                    <input type="checkbox"name="list_data_1_1" value="1" checkedtag="modify_data_1" disabled=""><label for="">修改模式</label>
                                                                                </label>-->
                                        <label class="checkbox inline m">
                                            <input type="checkbox"  value="2" checkedtag="add_data_1" disabled="">添加模式
                                        </label>
                                        <label class="checkbox inline">
                                            <input type="checkbox"   value="3" checkedtag="delete_data_1" disabled="">删除模式
                                        </label>
                                        <span style="padding-left:20px;">
<!--                                            <input type="hidden"  name="list_data_orgtitle" value="">
                                            <input type="hidden" name="LIST_DATA_1_COLUMN_VLAUE" value="">-->
                                            <button type="button" class="btn btn-default btn-xs" style="padding:4px 6px;"  onclick="open_bootcss_modal('{{"light_".$k}}','{{"fade_".$k}}')" disabled>字段权限设置</button>

                                        </span>

                                    </div>
                                </td>
                                <!--点击字段权限设置弹出框start-->
                                <td>
                                    
                                    <div id="light_{{$k}}" class="white_content"> 
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="close_bootcss_modal('{{"light_".$k}}','{{"fade_".$k}}')">×</button>
                                            <h3>{{$v['title']}} 字段权限设置</h3>
                                        </div>
                                        <div class="modal-body">
                                            <style>
                                                .TableList {line-height: 18px;border: 1px #cccccc solid;font-size: 9pt;border-collapse: collapse;padding: 3px;}
                                                .TableData{background: #FFFFFF;border-bottom: 1px #cccccc solid;padding: 3px;height: 30px;color: #000000;}
                                                .m{margin-top: -5px;}
                                            </style>
                                            <table class="TableList" width="468">
                                                <thead>
                                                    <tr>
                                                        <td></td>
                                                        <td class="TableData"></td>
                                                    <td class="TableData">
                                                    <label class="checkbox inline m">
                                                        <input type="checkbox"onclick="checked_all(this,'update','light_{{$k}}')"> 全选
                                                    </label>
                                                        <label class="checkbox inline" style="margin-left:36px;">
                                                        <input type="checkbox" onclick="checked_all(this,'secrecy','light_{{$k}}')">全选
                                                    </label>

                                                    <label class="checkbox inline" style="margin-left:36px;">
                                                        <input type="checkbox" onclick="checked_all(this,'readonly','light_{{$k}}')">全选
                                                    </label>
                                                </td>
                                                    </tr>
                                            </thead>
                                                <tbody>
                                                    
                                                    @foreach($v['orgtitle'] as $key=>$val)
                                                    <tr>
                                                        <td></td>
                                                        <td class="TableData" >{{$val}}</td>
                                                        <td class="TableData">
                                                            <label class="checkbox inline m">
                                                                <input type="checkbox" list_column_id="LIST_DATA_1_0" mode_type="hidden"  value="3">修改模式
                                                            </label>
                                                            <label class="checkbox inline">
                                                                <input type="checkbox" list_column_id="LIST_DATA_1_0" mode_type="hidden"  value="2">保密模式
                                                            </label>

                                                            <label class="checkbox inline">
                                                                <input type="checkbox" list_column_id="LIST_DATA_1_0" mode_type="readOnly"  value="1">只读模式
                                                            </label>
<!--                                                            <input type="hidden" name="LIST_COLUMN_INFO[DATA_1][0][title]" value="11">
                                                            <input type="hidden" name="LIST_COLUMN_INFO[DATA_1][0][priv]" id="LIST_DATA_1_0" value="">-->
                                                        </td>

                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <span style="color:red;float:left;">提示："保密模式" > "只读模式" > "修改模式"</span>
                                            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="close_bootcss_modal('{{"light_".$k}}','{{"fade_".$k}}')">确定</button>
                                            <!--<button class="btn" data-dismiss="modal" aria-hidden="true"></button>-->
                                        </div>
                                    </div> 
                                    <div id="fade_{{$k}}" class="black_overlay"> 

                                    </div>  
                                </td>
                                <!--点击字段权限设置弹出框end-->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="margin:0 auto;" id="list_item">
                    <table class="TableList" width="550">
                        <tbody><tr><td class="TableData" colspan="2" align="center" style="color:green;">无列表控件</td></tr>
                        </tbody></table>
                </div>
                @endif
                <!--编辑end-->
                @else
                <!--新增start-->
                 @if(count($listCtrlData)>0)
                <div style="margin:0 auto;" id="list_item">
                    <table class="TableList" width="550" style=" border: 1px #cccccc solid;">
                        <tbody>
                            @foreach($listCtrlData as $k=>$v)
                            <tr style="border-bottom-style: solid;" class="tr_attr">
                                <td class="TableData" listCtrlName="{{$v['list_ctrl_name']}}" title="{{$v['title']}}">{{$v['title']}}</td>
                                <td class="TableData">	
                                    <div class="remind_checkbox" style="margin-top:0px" >
                                        <!--                                        <label class="checkbox inline">
                                                                                    <input type="checkbox"name="list_data_1_1" value="1" checkedtag="modify_data_1" disabled=""><label for="">修改模式</label>
                                                                                </label>-->
                                        <label class="checkbox inline m">
                                            <input type="checkbox"  value="2" checkedtag="add_data_1" disabled="">添加模式
                                        </label>
                                        <label class="checkbox inline">
                                            <input type="checkbox"   value="3" checkedtag="delete_data_1" disabled="">删除模式
                                        </label>
                                        <span style="padding-left:20px;">
<!--                                            <input type="hidden"  name="list_data_orgtitle" value="">
                                            <input type="hidden" name="LIST_DATA_1_COLUMN_VLAUE" value="">-->
                                            <button type="button" class="btn btn-default btn-xs" style="padding:4px 6px;"  onclick="open_bootcss_modal('{{"light_".$k}}','{{"fade_".$k}}')" disabled>字段权限设置</button>

                                        </span>

                                    </div>
                                </td>
                                <!--点击字段权限设置弹出框start-->
                                <td>
                                    <style> 
                                        .black_overlay{  display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.8;  opacity:.80;  filter: alpha(opacity=80);  } 
                                        .white_content {  display: none;  position: absolute;  top: 25%;  left: 25%;  width: 50%;  height: 50%;  padding: 16px;  border: 16px solid orange;  background-color: white;  z-index:1002;  overflow: auto;  }  
                                    </style> 
                                    <div id="light_{{$k}}" class="white_content"> 
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="close_bootcss_modal('{{"light_".$k}}','{{"fade_".$k}}')">×</button>
                                            <h3>{{$v['title']}} 字段权限设置</h3>
                                        </div>
                                        <div class="modal-body">
                                            <style>
                                                .TableList {line-height: 18px;border: 1px #cccccc solid;font-size: 9pt;border-collapse: collapse;padding: 3px;}
                                                .TableData{background: #FFFFFF;border-bottom: 1px #cccccc solid;padding: 3px;height: 30px;color: #000000;}
                                                .m{margin-top: -5px;}
                                            </style>
                                            <table class="TableList" width="468">
                                                <thead>
                                                    <tr>
                                                        <td></td>
                                                        <td class="TableData"></td>
                                                        <td class="TableData">
                                                            <label class="checkbox inline m">
                                                                <input type="checkbox"onclick="checked_all(this,'update','light_{{$k}}')">全选
                                                            </label>
                                                            <label class="checkbox inline" style="margin-left:36px;">
                                                                <input type="checkbox" onclick="checked_all(this,'secrecy','light_{{$k}}')">全选
                                                            </label>

                                                            <label class="checkbox inline" style="margin-left:36px;">
                                                                <input type="checkbox" onclick="checked_all(this,'readonly','light_{{$k}}')">全选
                                                            </label>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($v['orgtitle'] as $key=>$val)
                                                    <tr>
                                                        <td></td>
                                                        <td class="TableData">{{$val}}</td>

                                                        <td class="TableData">
                                                            <label class="checkbox inline m">
                                                                <input type="checkbox" list_column_id="LIST_DATA_1_0" mode_type="hidden"  value="3">修改模式
                                                            </label>
                                                            <label class="checkbox inline">
                                                                <input type="checkbox" list_column_id="LIST_DATA_1_0" mode_type="hidden"  value="2">保密模式
                                                            </label>

                                                            <label class="checkbox inline">
                                                                <input type="checkbox" list_column_id="LIST_DATA_1_0" mode_type="readOnly"  value="1">只读模式
                                                            </label>
<!--                                                            <input type="hidden" name="LIST_COLUMN_INFO[DATA_1][0][title]" value="11">
                                                            <input type="hidden" name="LIST_COLUMN_INFO[DATA_1][0][priv]" id="LIST_DATA_1_0" value="">-->
                                                        </td>

                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="close_bootcss_modal('{{"light_".$k}}','{{"fade_".$k}}')">确定</button>
                                            <!--<button class="btn" data-dismiss="modal" aria-hidden="true"></button>-->
                                        </div>
                                    </div> 
                                    <div id="fade_{{$k}}" class="black_overlay"> 

                                    </div>  
                                </td>
                                <!--点击字段权限设置弹出框end-->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="margin:0 auto;" id="list_item">
                    <table class="TableList" width="550">
                        <tbody><tr><td class="TableData" colspan="2" align="center" style="color:green;">无列表控件</td></tr>
                        </tbody></table>
                </div>
                @endif
                <!--新增end-->
                @endif
            </div>
            {{--   <div id="head3">
            <div style="margin-top:10px; margin:0 auto;margin-bottom:10px;margin-top:10px;">
                <div class="flow_top_type clearfix">
                    <div class="float_left">
                        <span style="font-size:14px;font-weight:bold;">附件上传控件权限（只针对office文档附件，其他格式不控制）</span>
                    </div>
                </div>
            </div>
            <div style="margin:0 auto;" id="active">
                <table class="TableList" width="550">
                    <tbody><tr><td class="TableData" colspan="2" align="center" style="color:green;">无附件上传控件</td></tr>	</tbody></table>
            </div>
        </div>
        <div id="head5" style="clear:both;">
            <div style="margin-top:10px; margin:0 auto;margin-bottom:10px;">
                <div class="flow_top_type clearfix">
                    <div class="float_left">
                        <span style="font-size:14px;font-weight:bold;">图片上传控件权限</span>
                    </div>
                </div>
            </div>
            <div style="margin:0 auto;" id="img_upload">
                <table class="TableList" width="550">
                    <tbody><tr><td class="TableData" colspan="2" align="center" style="color:green;">无图片上传控件</td></tr>		</tbody></table>
            </div>
        </div>--}}
            {{--        <div id="head4">
            <div style="margin-top:10px; margin:0 auto;margin-bottom:10px;margin-top:10px;">
                <div class="flow_top_type clearfix">
                    <div class="float_left">
                        <span style="font-size:14px;font-weight:bold;">其他设置项</span>
                    </div>
                </div>
            </div>
            <div style="margin:0 auto;">
                <div class="head4_top">
                    <div class="intro">
                        <span style="font-size:12px;font-weight:bold;color:black;">允许在不可写情况下自动赋值的宏控件</span>
                        <button type="button" class="btn btn-primary" style="margin-left:20px;cursor: pointer;" onclick="open_bootcss_modal('MacroControl', 700);"> 设置</button>
                        <div id="MacroControl" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Macro_Control" aria-hidden="true">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <img src="/css/workflow/images/steps/edit.gif" width="22" height="20" align="absmiddle">
                                <span style="font-size:14px;font-weight:bold;">步骤 - 在不可写情况下自动赋值的宏控件</span>
                            </div>
                            <div class="plug_body_left" style="margin-left:50px;margin-top:20px;">
                                <div class="list_title">在不可写情况下自动赋值的宏控件</div>
                                <div class="list_infomation ui-selectable" id="macro_next_step_div">
                                    <table cellspacing="0px" cellpadding="0px" width="100%" id="macro_next_step_tab">
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="margin-top:10px;">
                                    <button class="btn" id="macro_left_btn" type="button" onclick="selectMacroAll('left', 1);" style="margin-left:100px;">全选</button>
                                </div>	
                            </div>
                            <div class="plug_body_center">
                                <div class="change_right">
                                    <img src="{{asset('css/workflow/images/steps/right.png')}}" id="change_right" onclick="selectPlug('macro_next_step_tab', 'macro_alternative_tab', 'macro_left_btn', 1);">
        </div>
        <div class="change_left">
            <img src="{{asset('css/workflow/images/steps/left.png')}}" id="change_left" onclick="selectPlug('macro_alternative_tab', 'macro_next_step_tab', 'macro_right_btn', 1);">
        </div>
    </div>
    <div class="plug_body_right" style="margin-top:20px;">
        <div class="list_title">备选字段</div>
        <div class="list_infomation ui-selectable" id="macro_alternative_div">
            <table cellspacing="0px" cellpadding="0px" width="100%" id="macro_alternative_tab">
                <tbody>
                    <tr class="macro_alternative_step ui-selectee"><td class="step" style="padding-left: 20px;" name="申请日期">申请日期</td></tr>
                    <tr class="macro_alternative_step ui-selectee"><td class="step" style="padding-left: 20px;" name="申请人">申请人</td></tr>
                    <tr class="macro_alternative_step ui-selectee"><td class="step" style="padding-left: 20px;" name="部门">部门</td></tr>
                    <tr class="macro_alternative_step ui-selectee"><td class="step" style="padding-left: 20px;" name="上级主管签字">上级主管签字</td></tr>
                    <tr class="macro_alternative_step ui-selectee"><td class="step" style="padding-left: 20px;" name="人事部签字">人事部签字</td></tr>
                    <tr class="macro_alternative_step ui-selectee"><td class="step" style="padding-left: 20px;" name="总经理签字">总经理签字</td></tr>
                </tbody>			
            </table>
        </div>
        <div style="margin-top:10px;">
            <button class="btn" id="macro_right_btn" type="button" onclick="selectMacroAll('right', 1);" style="margin-left:100px;">全选</button>
        </div>
    </div>
    右侧页面控件右侧部分结束
    <div style="clear:both;color:green;margin-top:15px;margin-bottom:15px;margin-left:430px;">点击条目时，可以组合CTRL键进行多选</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="macroAppend();">确定</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
</div>
<div class="intro_name"><span style="color:green;">暂无在不可写情况下自动赋值的宏控件</span></div>
</div>
<div class="head4_bottom" style="margin-top:10px;">
    <div class="f_field_label"><span class="f_field_title" style="font-size:12px;font-weight:bold;color:black;">公共附件中的Office文档详细权限设置</span></div>
    <div class="remind_checkbox" style="margin-top:0px">
        <label class="checkbox inline">
            <input type="checkbox" id="PRIV1" name="PRIV_NEW" checked=""><label for="PRIV1">新建权限</label>
        </label>
        <label class="checkbox inline">
            <input type="checkbox" id="PRIV2" name="PRIV_EDIT" checked=""><label for="PRIV2">编辑权限</label>
        </label>
        <label class="checkbox inline">
            <input type="checkbox" id="PRIV3" name="PRIV_DEL" checked=""><label for="PRIV3">删除权限</label>
        </label>
        <label class="checkbox inline">
            <input type="checkbox" id="PRIV4" name="PRIV_OFFICE_DOWN" checked=""><label for="PRIV4">下载权限</label>
        </label>
        <label class="checkbox inline">
            <input type="checkbox" id="PRIV5" name="PRIV_OFFICE_PRINT" checked=""><label for="PRIV5">打印权限</label>
        </label>	
    </div> 页面checkebox 结束 
    <div class="f_field_block clear">
        <div class="f_field_label"><span class="f_field_title" style="font-size:12px;font-weight:bold;color:black;">是否允许本步骤经办人编辑附件</span></div>
        <div class="f_field_ctrl">
            <label class="radio" style="float:left;">
                <input type="radio" name="ATTACH_EDIT_PRIV" id="edit_yes" value="1"><label for="edit_yes">允许</label>
            </label>
            <label class="radio" style="float:left;margin-left:15px;">
                <input type="radio" name="ATTACH_EDIT_PRIV" id="edit_no" value="0" checked=""><label for="edit_no">不允许</label>
            </label>
        </div>
    </div>
    <div class="f_field_block clear">
        <div class="f_field_label"><span class="f_field_title" style="font-size:12px;font-weight:bold;color:black;">是否允许本步骤办理人在线创建文档</span></div>
        <div class="f_field_ctrl">
            <label class="radio" style="float:left;">
                <input type="radio" name="ATTACH_EDIT_PRIV_ONLINE" id="online_edit_yes" value="0" checked=""><label for="online_edit_yes">允许</label>
            </label>
            <label class="radio" style="float:left;margin-left:15px;">
                <input type="radio" name="ATTACH_EDIT_PRIV_ONLINE" id="online_edit_no" value="1"><label for="online_edit_no">不允许</label>
            </label>
        </div>
    </div>
    <div class="f_field_block clear">
        <div class="f_field_label"><span class="f_field_title" style="font-size:12px;font-weight:bold;color:black;">宏标记附件上传为图片时展示效果</span></div>
        <div class="f_field_ctrl">
            <label class="radio" style="float:left;">
                <input type="radio" name="ATTACH_MACRO_MARK" id="attach_macro_mark_img" value="0" checked=""><label for="attach_macro_mark_img">显示图片</label>
            </label>
            <label class="radio" style="float:left;margin-left:15px;">
                <input type="radio" name="ATTACH_MACRO_MARK" id="attach_macro_mark_name" value="1"><label for="attach_macro_mark_name">显示图标和名称</label>
            </label>
        </div>
    </div> 页面radiao结束 
</div>
</div>
</div>--}}
</div> 
{{--
<input type="hidden" name="FLD_STR" value="" id="LIST_FLDS_STR">
<input type="hidden" name="ClASS_NAME" value="" id="class_name">
<input type="hidden" name="FILE_PRIV_ARR" value="" id="FILE_PRIV_ARR">
<input type="hidden" name="ClASS_NAME_ACTIVE" value="" id="class_name_active">
<input type="hidden" name="LIST_ITEM" value="" id="LIST_ITEM">
<input type="hidden" name="docControls" value="docControls" id="docControls">
<input type="hidden" name="imgControls" value="imgControls" id="imgControls">
<input type="hidden" name="listControls" value="listControls" id="listControls">
<input type="hidden" name="LIST_TYPE" value="" id="LIST_TYPE">
<input type="hidden" name="MACRO_FLD_STR" value="" id="MACRO_LIST_FLDS_STR">
--}}
</div><!-- 页面整体布局结束 -->					
</div>
