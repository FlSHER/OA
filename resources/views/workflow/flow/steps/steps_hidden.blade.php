<!--保密字段-->
<!--steps_hidden-->
<div class="tab-pane" id="hidden">
    <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/new_flow.css')}}">
    <style>
        #feedback { font-size: 1.4em; }
        .ui-selecting { background: rgb(114, 176, 207); }
        .ui-selected { background: rgb(64,148,189); color: white; }
    </style>

    <!-- 头部的提醒开始 -->
    <div style="margin:0 auto;margin-bottom:10px;margin-top:10px;">
        <div class="flow_top_type clearfix">
            <div style="float:left"><span class="icon20-hidden">编辑保密字段(保密字段对于本步骤主办人、经办人均为不可见)</span></div>
            <div class="step_name_intro">
                <span class="flow_intro">步骤名称:</span>
                <span class="flow_name">{{$data['prcs_name'] or ''}}</span>
            </div>
        </div>
    </div>
    <!-- 头部提醒的结束 -->
    <!-- 页面控件部分的开始 -->
    <div class="plug" style=" margin:0 auto;">
        <!-- 右侧页面控件左侧部分开始 -->
        <div class="plug_body_left">
            <div class="list_title">本步骤保密字段</div>
            <div id="h_next_step_dump_div" style="display: none;margin: 0;padding: 0;"></div>
            <div class="list_infomation ui-selectable" id="h_next_step_div_hidden">
                <table cellspacing="0px" cellpadding="0px" width="100%" id="h_next_step_tab">
                    <tbody id="h_next_step_tbody">
                        @if(isset($data['hidden_item'])&& !empty($data['hidden_item']))
                        @foreach($data['hidden_item'] as $k=>$v)
                        <tr style="cursor: pointer;">
                            <td class="step"  name="{{$k}}">{{$v}}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px;">
                <button class="btn" id="left_btn" type="button" onclick="selectAll(this, 'h_next_step_tab');" style="margin-left:100px;">全选</button>
            </div>	
        </div>
        <!-- 右侧页面控件左侧部分结束 -->
        <!-- 右侧页面控件中间部分开始 -->
        <div class="plug_body_center">
            <div class="change_right">
                <img src="/css/workflow/images/steps/right.png" id="change_right" onclick="selectPlug(this, 'h_next_step_tab', 'h_alternative_tab', 'left_btn');">
            </div>
            <div class="change_left">
                <img src="/css/workflow/images/steps/left.png" id="change_left" onclick="selectPlug(this, 'h_alternative_tab', 'h_next_step_tab', 'right_btn');">
            </div>
        </div>
        <!-- 右侧页面控件中间部分结束 -->
        <!-- 右侧页面控件右侧部分开始 -->
        <div class="plug_body_right">
            <div class="list_title">备选字段</div>
            <div class="list_infomation ui-selectable" id="h_alternative_div_hidden">
                <table cellspacing="0px" cellpadding="0px" width="100%" id="h_alternative_tab">
                    <tbody id="h_alternative_tbody">
                        @if(isset($data['hidden_item_optional_field']) && !empty($data['hidden_item_optional_field']))
                            @foreach($data['hidden_item_optional_field'] as $k=>$v)
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
                <button class="btn" id="right_btn" type="button" onclick="selectAll(this, 'h_alternative_tab');" style="margin-left:100px;">全选</button>
            </div>
        </div>
        <!-- 右侧页面控件右侧部分结束-->
        <div style="clear:both;color:green;margin-top:15px;margin-bottom:15px;margin-left:380px;">点击条目时，可以组合CTRL键进行多选</div>
    </div>
    <!-- 页面控件部分的结束 -->
    <!--<input type="hidden" name="ID" value="0" id="prc_id">
    <input type="hidden" name="FLOW_ID" value="131" id="flow_id">
    <input type="hidden" name="H_FLD_STR" value="" id="H_LIST_FLDS_STR">
    <input type="hidden" name="LIST_TYPE" value="id=&quot;LIST_TYPE&quot;">-->
</div>

