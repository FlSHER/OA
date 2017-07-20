<!--条件设置-->
<!--steps_condition-->
<div class="tab-pane" id="condition">
    <div class="flow_top_type clearfix">
        <div class="float_left"><span class="icon20-condition_4">条件生成器</span></div>
        <!--<div style="float:right;margin-top:5px;margin-right:5px;"class="step_name_intro">步骤名称:</div>-->
        <div class="step_name_intro">
            <span class="flow_intro">步骤名称:</span>
            <span class="flow_name">{{$data['prcs_name'] or ''}}</span>
        </div>
    </div>
    <div class="circulation_sponsor" style="clear:both;">
        <div class="condition_sponsor_content">
            <div class="circulation_sponsor_revise_left">字段</div>
            <div class="btn-group circulation_sponsor_select">
                <select id="condition_item_name" class="span2">
                @if(isset($data) && !empty($data['flow_prcs_all_field']))
                    @foreach($data['flow_prcs_all_field'] as $k=>$v)
                        @if(isset($v['name']))
                            <option names="{{$v['name']}}" value="{{$v['title']}}">{{$v['title']}}</option>
                        @else if(isset($v['checkboxs']))
                            @foreach($v['checkboxs'] as $ck=>$cv)
                                <option names="{{$cv['name']}}" value="{{$cv['value']}}">{{$cv['value']}}</option>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    @if(!empty($field))
                        @foreach($field as $k=>$v)
                            @if(isset($v['name']))
                                <option names="{{$v['name']}}" value="{{$v['title']}}">{{$v['title']}}</option>
                            @else if(isset($v['checkboxs']))
                                @foreach($v['checkboxs'] as $ck=>$cv)
                                    <option names="{{$cv['name']}}" value="{{$cv['value']}}">{{$cv['value']}}</option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endif
                </select>
            </div>
            <div class="circulation_sponsor_revise_left">条件</div>
            <div class="btn-group circulation_sponsor_select2">
                <select id="condition_where" style="width:100px" onchange="change_condition(this.value);">
                    <option value="=">等于</option>
                    <option value="<>">不等于</option>
                    <option value=">">大于</option>
                    <option value="<">小于</option>
                    <option value=">=">大于等于</option>
                    <option value="<=">小于等于</option>
                    <option value="include">包含</option>
                    <option value="exclude">不包含</option>
                </select>
            </div>
            <div class="circulation_sponsor_checkbox" id="div_check_estimate">
                <label class="checkbox inline">
                    <input type="checkbox" id="check_type" style="display:inline" onclick="change_type(this);"> <b>类型判断</b>
                </label>
            </div>
            <div id="div_type" style="display:none">
                <div id="div_check" class="circulation_sponsor_revise_left">类型</div>
                <div class="btn-group circulation_sponsor_select">
                    <select id="item_type" class="span2">
                        <option value="数值">数值</option>
                        <option value="日期">日期</option>
                        <option value="日期+时间">日期+时间</option>
                    </select>
                </div>
            </div>
            <div id="div_value" style="display:inline">
                <div class="circulation_sponsor_revise_left">值</div>
                <div class="btn-group circulation_sponsor_select">
                    <input type="text" id="condition_item_value" style="height: 30px;" class="span2">
                </div>
            </div>
            <label class="condition_note">类型判断勾选显示“类型”（下拉框），不勾选显示“单行文本”</label>
        </div>
    </div>
    <div class="condition_button">
        <button type="button" id="in_condition" class="btn btn-primary" onclick="add_condition(0)">添加转入条件</button>
        <button type="button" id="out_condition" class="btn btn-primary" onclick="add_condition(1)">添加转出条件</button>	
    </div>
    <div id="in_flow_top_type" class="flow_top_type clearfix">
        <div style="float:left"><span class="icon20-condition_2">转入条件列表</span></div>
        <div style="float:right;margin-top:5px;margin-right:5px;"></div>
    </div>
    <div id="in_circulation_sponsor" class="circulation_sponsor" style="clear:both;">	
        <div class="condition_sponsor_table">
            <table class="table table-bordered" id="prcs_in_tab">
                <thead>
                    <tr class="condition_sponsor_table_tr">				 
                        <td id="condition_sponsor_table_td1" style="color:black;font-weight:bold;">编号</td>
                        <td id="condition_sponsor_table_td2" style="color:black;font-weight:bold;">条件描述</td>
                        <td id="condition_sponsor_table_td3" style="color:black;font-weight:bold;">操作</td>
                    </tr>
                </thead>
                <tbody id="condition_in">
                @if(isset($data) && !empty($data['prcs_in']) && 'null' != $data['prcs_in'])
                    @foreach($data['prcs_in'] as $inKey => $inVal)
                        <tr names="{{$inVal[0]}}" prcs_in_id="{{$inKey + 1}}">
                            <td>[{{$inKey + 1}}]</td>
                            <td><span>{{$inVal[1]}}</span></td>
                            <td>
                                <a href="javascript:void(0)" onclick="edit_condition(this)">编辑</a>
                                <a href="javascript:void(0);" onclick="dalete_condition('condition_in',this)">删除</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <input type="hidden" name="prcs_in" id="prcs_in" value="{{$data['prcs_in_hide'] or ''}}">
        </div>
        <div>
            <label class="circulation_size2" style="color:black;font-weight:bold;">转入条件公式(条件与逻辑运算符之间需空格，如[1] AND [2])</label>
            <input class="condition_input" type="text" name="prcs_in_set" id="prcs_in_set" value="{{$data['prcs_in_set'] or ''}}" style="height: 30px;">
        </div>
        <div>
            <label class="circulation_size2" style="color:black;font-weight:bold;">不符合条件公式时，给用户的文字描述：</label>
            <input class="condition_input" type="text" name="prcs_in_desc" value="{{$data['prcs_in_desc'] or ''}}" style="height: 30px;">
        </div>
        <div class="alert fade in condition_warning" style="color: red;padding-left:0px;">
            <button type="button" class="close" data-dismiss="alert">×</button>
            合理设定转入条件，可形成流程的条件分支，但数据满足转入条件，才可转入本步骤	</div>
    </div>
    <div id="out_flow_top_type" class="flow_top_type clearfix">
        <div style="float:left"><span class="icon20-condition_3">转出条件列表</span></div>
        <div style="float:right;margin-top:5px;margin-right:5px;"></div>
    </div>
    <div id="out_circulation_sponsor" class="circulation_sponsor">
        <div class="condition_sponsor_table">
            <table class="table table-bordered" id="prcs_out_tab">
                <thead>
                    <tr class="condition_sponsor_table_tr">				 
                        <td id="condition_sponsor_table_td1" style="color:black;font-weight:bold;">编号</td>
                        <td id="condition_sponsor_table_td2" style="color:black;font-weight:bold;">条件描述</td>
                        <td id="condition_sponsor_table_td3" style="color:black;font-weight:bold;">操作</td>
                    </tr>
                </thead>
                <tbody id="condition_out">
                @if(!empty($data['prcs_out']) && 'null' != $data['prcs_out'])
                    @foreach($data['prcs_out'] as $inKey => $inVal)
                        <tr names="{{$inVal[0]}}" prcs_in_id="{{$inKey + 1}}">
                            <td>[{{$inKey + 1}}]</td>
                            <td><span>{{$inVal[1]}}</span></td>
                            <td>
                                <a href="javascript:void(0)" onclick="edit_condition(this)">编辑</a>
                                <a href="javascript:void(0);" onclick="dalete_condition('condition_out',this)">删除</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <input type="hidden" name="prcs_out" id="prcs_out" value="{{$data['prcs_out_hide'] or ''}}">
        </div>
        <div>
            <label class="circulation_size2" style="color:black;font-weight:bold;">转出条件公式(条件与逻辑运算符之间需空格，如[1] AND [2])</label>
            <input class="condition_input" type="text" name="prcs_out_set" id="prcs_out_set" value="{{$data['prcs_out_set'] or ''}}" style="height: 30px;">
        </div>
        <div>
            <label class="circulation_size2" style="color:black;font-weight:bold;">不符合条件公式时，给用户的文字描述：</label>
            <input class="condition_input" type="text" name="prcs_out_desc" value="{{$data['prcs_out_desc'] or ''}}" style="height: 30px;">
        </div>
    </div>
    {{-- <script type="text/javascript" src="/static/js/jquery-1.5.1/jsrender/jsrender.min.js"></script> --}}
</div>