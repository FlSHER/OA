<!--必填字段-->
<!--steps_required-->
<div class="tab-pane" id="required">
    <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/new_flow.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/style.css')}}">
    <!-- 头部的提醒开始 -->
    <div style="margin:0 auto;margin-bottom:10px;margin-top:10px;">
        <div class="flow_top_type clearfix">
            <div style="float:left"><span class="icon20-required">编辑必填字段</span></div>
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
            <div class="list_title">本步骤必填字段</div>
            <div id="r_next_step_dump_div" style="display: none;margin: 0;padding: 0;"></div>
            <div class="list_infomation ui-selectable" id="r_next_step_div">
                <table cellspacing="0px" cellpadding="0px" width="100%" id="r_next_step_tab">
                    <tbody id="r_next_step_tbody">
                        @if(isset($data['required_item'])&& !empty($data['required_item']))
                        @foreach($data['required_item'] as $k=>$v)
                        <tr style="cursor: pointer;">
                            <td class="step"  name="{{$k}}">{{$v}}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px;">
                <button class="btn" id="r_left_btn" type="button" onclick="selectAll(this, 'r_next_step_tbody');" style="margin-left:100px;">全选</button>
            </div>	
        </div>
        <!-- 右侧页面控件左侧部分结束 -->
        <!-- 右侧页面控件中间部分开始 -->
        <div class="plug_body_center">
            <div class="change_right">
                <img src="{{asset('css/workflow/images/steps/right.png')}}" id="change_right" onclick="selectPlug(this, 'r_next_step_tbody', 'r_alternative_tbody', 'r_left_btn');">
            </div>
            <div class="change_left">
                <img src="{{asset('css/workflow/images/steps/left.png')}}" id="change_left" onclick="selectPlug(this, 'r_alternative_tbody', 'r_next_step_tbody', 'r_right_btn');">
            </div>
        </div>
        <!-- 右侧页面控件中间部分结束 -->
        <!-- 右侧页面控件右侧部分开始 -->
        <div class="plug_body_right">
            <div class="list_title">备选字段</div>
            <div class="list_infomation ui-selectable" id="r_alternative_div">
                <table cellspacing="0px" cellpadding="0px" width="100%" id="r_alternative_tab">
                    <tbody id="r_alternative_tbody">
                        @if(isset($data['required_item_optional_field']) && !empty($data['required_item_optional_field']))
                            @foreach($data['required_item_optional_field'] as $k=>$v)
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
                <button class="btn" id="r_right_btn" type="button" onclick="selectAll(this, 'r_alternative_tbody');" style="margin-left:100px;">全选</button>
            </div>
        </div>
        <!-- 右侧页面控件右侧部分结束-->
        <div style="clear:both;color:green;margin-top:15px;margin-bottom:15px;margin-left:380px;">点击条目时，可以组合CTRL键进行多选</div>
    </div>
    <!-- 页面控件部分的结束 -->
<!--    <input type="hidden" name="R_FLD_STR" value="" id="R_LIST_FLDS_STR">
    <input type="hidden" name="LIST_TYPE" value="id=&quot;LIST_TYPE&quot;">			-->
</div>
