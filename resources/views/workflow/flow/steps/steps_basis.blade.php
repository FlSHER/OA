<!-- 基本信息 -->
<!--steps_basis-->

<div class="tab-pane active" id="basis">
    <div class="flow_top_type clearfix">
        <div style="float:left"><span class="icon20-basis">基本设置</span></div>
        <div class="step_name_intro">
            <span class="flow_intro">步骤名称:</span>
            <span class="flow_name">{{$data['prcs_name'] or ''}}</span>

        </div>
    </div>
    <div class="flow_main_body">
        <!-- ============ 右侧部分开始================== -->
        <div class="flow_main_body_left" style="width:384px;">
            <div class="f_field_block">
                <div class="f_field_label"><span class="f_field_title">步骤ID</span><span class="f_field_required">*</span></div>
                <div class="f_field_ctrl clear">
                    <input type="hidden" name="flow_id"  id="flow_id" value="{{$flow_id or $data['flow_id']}}">
                    <input type="hidden" id="edit_sign" value="{{$data['id'] or ''}}">
                    <input type="text" name="prcs_id" style="height: 30px;float: left;" id="prcs_id" class="span1" placeholder="0" value="{{$prcs_id or $data['prcs_id']}}">
                    <p style="color:red;height:30px;float:left;padding: 6px;text-align:center;display:table-cell;vertical-align:middle"></p>
                    <div id="PRCS_ERPEAT">
                    </div>
                </div>
            </div>
            <div class="f_field_block clear">
                <div class="f_field_label"><span class="f_field_title">节点类型</span><span class="f_field_required">*</span></div>
                <div class="f_field_ctrl clear">                  
                    <select name="prcs_type" id="prcs_type" class="span2">
                        <option value="0" <?php if(isset($data['prcs_type'])){
                            if($data['prcs_type'] == 0 ){echo 'selected';}
                        }else{
                            echo "selected";
                        } ?> >步骤节点</option>				  
                        <option value="1" <?php if(isset($data['prcs_type'])){
                            if($data['prcs_type'] == 1 ){echo 'selected';}
                        }else{
                            
                        }?> >子流程节点</option>
                        <option value="2" <?php if(isset($data['prcs_type'])){
                            if($data['prcs_type'] == 2 ){echo 'selected';}
                        }else{
                            
                        } ?> >柔性节点</option>
                    </select>
                </div>
            </div>
            <!--子流程类型开始-->
<!--            <div class="f_field_block" style="display:none;" id="body1">
                <div class="f_field_label"><span class="f_field_title">子流程类型</span><span class="f_field_required">*</span></div>
                <div class="f_field_ctrl">
                    <select id="child_flow_id" name="child_flow_id" onchange="sub_form(this.value)" style="display: none;">
                    </select>
                    <span class="ui-combobox" style="margin-right: 16px;">
                        <input type="text" id="flow_name" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" style="width: 120px; text-align: left; background-color: rgb(255, 255, 255); border-radius: 4px 0px 0px 4px;">
                        <button type="button" tabindex="-1" class="btn dropdown-toggle" style="margin-left: -1px; border-radius: 0px 4px 4px 0px; vertical-align: top;">
                            <span class="caret"></span>
                        </button>
                    </span>
                    <input type="hidden" id="child_flow_hidden_id" name="child_flow_hidden_id" value="">
                </div>
            </div>-->
            <!--子流程类型 结束-->
            <!--步骤名称开始--> 	       		
            <div class="f_field_block" id="step_name">
                <div class="f_field_label"><span class="f_field_title">步骤名称</span><span class="f_field_required">*</span></div>
                <div class="f_field_ctrl clear"><input type="text" style="height: 30px;" id="prcs_name" name="prcs_name" value="{{$data['prcs_name'] or ''}}">
                </div>
            </div>
            <!-- 步骤名称结束--> 
            <!-- 是否拷贝公共附件 开始-->        
<!--            <div class="f_field_block" style="display:none;" id="add_attach">
                <div class="f_field_label"><span class="f_field_title">是否拷贝公共附件</span></div>
                <div class="f_field_ctrl">	
                    <label class="radio" style="float:left;">
                        <input type="radio" name="ADD_ATTACH" id="attach0" value="0" checked=""> 否</label>
                    <label class="radio" style="float:left;margin-left:15px;">
                        <input type="radio" name="ADD_ATTACH" id="attach1" value="1">是</label>
                </div>
            </div>-->
            <!-- 是否拷贝公共附件结束--> 
            <!-- 结束后动作开始--> 
<!--            <div class="f_field_block" style="display:none;clear:both;" id="over_act">
                <div style="height:8px;"></div>
                <div class="f_field_label"><span class="f_field_title">结束后动作</span></div>
                <div class="f_field_ctrl">	
                    <label class="radio" style="float:left;">
                        <input type="radio" name="OVER_ACT" checked="" id="act0" value="0" onclick="sel_back(2)">结束并更新父流程节点为结束
                    </label>
                    <label class="radio" style="float:left;margin-left:15px;">
                        <input type="radio" name="OVER_ACT" id="act" value="1" onclick="sel_back(1)"> 结束并返回父流程步骤
                    </label>
                </div>
            </div>-->
            <!-- 结束后动作结束 --> 
            <!-- 返回步骤 开始 -->
<!--            <div class="f_field_block" style="clear:both;display:none;" id="back_step">
                <div style="height: 8px;"></div>
                <div class="f_field_label"><span class="f_field_title">返回步骤</span></div>
                <div class="f_field_ctrl">                  
                    <select name="PRCS_BACK" id="PRCS_BACK" class="span2" onchange="javascript:_id = this.value">
                        <option value="">请选择返回步骤</option>
                        <option value="1">1、1</option>
                        <option value="2">2、2</option>
                        <option value="3">3、3</option>
                    </select>
                </div>
            </div>-->
            <!-- 返回步骤 结束--> 
            <!-- 主办人 经办人添加开始 子流程-->
<!--            <div id="main_and_exp_1" style="clear:both;display:none;">             
                <div class="f_field_block">
                    <div style="height: 8px;"></div>
                    <div class="f_field_label"><span class="f_field_title">主办人</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="CHILD_AUTO_USER_OP" id="CHILD_AUTO_USER_OP" value="">
                        <input type="text" name="CHILD_AUTO_USER_OP_NAME" id="CHILD_AUTO_USER_OP_NAME" value="" size="10" class="SmallStatic" readonly="">
                    </div>
                </div>
                <div class="f_field_block">
                    <div class="f_field_label"><span class="f_field_title">经办人</span></div>
                    <div class="f_field_ctrl">					
                        <input type="hidden" name="CHILD_AUTO_USER" id="CHILD_AUTO_USER" value="">
                        <textarea cols="40" name="CHILD_AUTO_USER_NAME" id="CHILD_AUTO_USER_NAME" rows="4" class="span5" wrap="yes" readonly=""></textarea>
                        <a href="javascript:;" class="orgAdd" onclick="LoadWindowBasis()" title="指定经办人和主办人">指定经办/主办人</a>
                        <a href="javascript:;" class="orgClear" onclick="ClearUser('CHILD_AUTO_USER_OP', 'CHILD_AUTO_USER_OP_NAME');ClearUser('CHILD_AUTO_USER', 'CHILD_AUTO_USER_NAME')" title="清空经办人和主办人">清空</a>
                    </div>
                </div>
            </div> -->
        </div>	

        <!-- 主办人 经办人添加结束--> 
        <!-- 右侧部分开始 返回步骤默认经办人  外部节点-->
        <div class="flow_main_body_left" style="margin-left:20px;">
            <!-- 主办人 经办人添加开始-->
<!--            <div id="main_and_exp" style="clear:both;display:none;">             
                <div class="f_field_block">
                    <div class="f_field_label"><span class="f_field_title">主办人</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="EXT_BACK_USER_OP" id="EXT_BACK_USER_OP" value="">
                        <input type="text" name="EXT_BACK_USER_OP_NAME" id="EXT_BACK_USER_OP_NAME" style="height:20px;" class="span2" wrap="yes" value="" readonly="">
                    </div>
                </div>
                <div class="f_field_block">
                    <div class="f_field_label"><span class="f_field_title">经办人</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="EXT_BACK_USER" id="EXT_BACK_USER" value="">
                        <textarea style="width:280px;height:40px;" name="EXT_BACK_USER_NAME" id="EXT_BACK_USER_NAME" wrap="yes" readonly=""></textarea>  
                        <a href="javascript:;" class="orgAdd" onclick="LoadWindowBasis()" title="指定经办人和主办人">指定经办/主办人</a>
                        <a href="javascript:;" class="orgClear" onclick="ClearUser('EXT_BACK_USER', 'EXT_BACK_USER_NAME');ClearUser('EXT_BACK_USER_OP', 'EXT_BACK_USER_OP_NAME')" title="清空经办人和主办人">清空</a>
                    </div>
                </div>
            </div> -->
            <!-- 主办人 经办人添加结束--> 
            <!-- 下一步骤的开始 -->
            <div id="basis_next">
                <div class="f_field_label" style="clear:both;margin-bottom:5px;"><span class="f_field_title">下一步骤</span></div>
                <div class="plug_body">
                    <div class="plug_body_center_small">
                        <div class="change_up">
                            <img id="change_up" src="/css/workflow/images/steps/top.png">
                        </div>
                        <div class="change_down">
                            <img id="change_down" src="/css/workflow/images/steps/down.png">
                        </div>
                    </div>
                    <div class="plug_body_left_small">
                        <div class="list_title">下一步骤</div>
                        <div class="list_infomation_small ui-selectable" id="next_step_div">
                            <table cellspacing="0px" cellpadding="0px" width="100%" id="next_step_tab">
                                <tbody id="alternative_next">
                                    @if(isset($allSteps['next_prcs_to_all']))
                                    @foreach($allSteps['next_prcs_to_all'] as $k=>$v)
                                    <tr style="cursor: pointer;">
                                        <td>{{$v['prcs_id']}}:{{$v['prcs_name']}}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>		
                            </table>
                        </div>
                        <div style="text-align:center;margin-top:10px;">
                            <button class="btn" id="left_btn" type="button" onclick="selectAll(this,'alternative_next');">全选</button>
                        </div>
                    </div>
                    <!--<input type="hidden" name="PRCS_TO" id="PRCS_TO_FLAG" value="">-->
                    <div class="plug_body_center">
                        <div class="change_right">
                            <img src="/css/workflow/images/steps/right.png" id="change_right" onclick="selectPlug(this,'next_step_tab','alternative_tab','left_btn');">
                        </div>
                        <div class="change_left">
                            <img src="/css/workflow/images/steps/left.png" id="change_left" onclick="selectPlug(this,'alternative_tab','next_step_tab','right_btn');">
                        </div>
                    </div>
                    <div class="plug_body_right">
                        <div class="list_title">备选步骤</div>
                        <div class="list_infomation_small ui-selectable" id="alternative_div">
                            <table cellspacing="0px" cellpadding="0px" width="100%" id="alternative_tab">
                                <tbody id="alternative_tr">
                                     @if(isset($allSteps['alternative_steps']))
                                    @foreach($allSteps['alternative_steps'] as $k=>$v)
                                    <tr style="cursor: pointer;">
                                        <td>{{$v['prcs_id']}}:{{$v['prcs_name']}}</td>
                                    </tr>
                                    @endforeach
                                    @endif

                                    <tr style="cursor: pointer;">
                                        <td>0:[结束]</td>
                                    </tr>

                                </tbody>									
                            </table>
                        </div>
                        <div style="text-align:center;margin-top:10px;">
                            <button class="btn" id="right_btn" type="button" onclick="selectAll(this,'alternative_tr');">全选</button>
                        </div>
                    </div>
                </div>
            </div>	
            <!-- 下一步骤的结束-->
            <!-- 设置表单映射字段开始-->
<!--            <div style="display:none;float:left;" id="set_form_size">
                <div style="float:left;width:260px;">
                    <div class="list_title">父流程字段</div>
                    <select name="FLD_PARENT" id="FLD_PARENT" size="10" style="width:260px;height:200px;">
                        <option value="到货部门">到货部门</option>
                        <option value="单据编号">单据编号</option>
                        <option value="制表日期">制表日期</option>
                        <option value="制表人">制表人</option>
                        <option value="到包明细">到包明细</option>
                        <option value="总合计">总合计</option>
                        <option value="实付合计">实付合计</option>
                        <option value="未付合计">未付合计</option>
                        <option value="制表备注">制表备注</option>
                        <option value="审核人">审核人</option>
                        <option value="审核意见">审核意见</option>
                        <option value="审核备注">审核备注</option>
                    </select>
                    <input type="button" id="fathertoson" class="btn btn-small btn-primary" value="添加父-子映射" onclick="map_relation('IN')" style="margin-left:80px;">
                </div>

                <div style="float:left;width:260px;margin-left:20px;" id="field_sub">

                </div>

            </div>
            <div class="f_field_block mappings clear" style="display: none;">
                <div class="f_field_label"><span class="f_field_title">父流程-&gt;子流程映射关系</span></div>
                <div class="f_field_ctrl" id="RELATION_IN"></div>
                <input type="hidden" name="MAP_IN" id="MAP_IN" value="">
            </div>
            <div class="f_field_block mappings" style="display: none;">
                <div class="f_field_label"><span class="f_field_title">子流程-&gt;父流程映射关系</span></div>
                <div class="f_field_ctrl" id="RELATION_OUT"></div>
                <input type="hidden" name="MAP_OUT" id="MAP_OUT" value="">
            </div>

             设置表单映射字段结束- 
             主办人 经办人添加开始- 
            <div id="main_and_exp" style="clear:both;display:none;">             
                <div class="f_field_block">
                    <div style="height: 23px;"></div>
                    <div class="f_field_label"><span class="f_field_title">主办人</span></div>
                    <div class="f_field_ctrl"><input type="text" style="height:20px;" class="span2" wrap="yes" readonly=""></div>
                </div>
                <div class="f_field_block">
                    <div class="f_field_label"><span class="f_field_title">经办人</span></div>
                    <div class="f_field_ctrl">
                        <input type="hidden" name="TO_ID" value="">
                        <textarea style="width:300px;height:40px;" name="TO_NAME" wrap="yes" readonly=""></textarea>   
                        <a href="javascript:;" class="orgAdd">添加</a>
                        <a href="javascript:;" class="orgClear">清空</a>
                    </div>
                </div>
            </div> -->
            <!-- 主办人 经办人添加结束- --> 
        </div> 
        <!-- 右侧部分结束-->
<!--        <input type="hidden" name="ID" id="prcs_key_id" value="">
        <input type="hidden" name="TYPE" value="new">
        <input type="hidden" name="ADDRESS_ID" value="">
        <input type="hidden" value="步骤名称::" id="hidId">
        <input type="hidden" name="dropdown_filter2" value="">
        <input type="hidden" name="operator_auto_type2" value="">
        <input type="hidden" name="operator_base_user2" value="">
        <input type="hidden" name="operator_prcs_user2" value="">
        <input type="hidden" name="operator_item_id2" value="">
        <input type="hidden" value="" id="hrefs">
        <input type="hidden" value="" id="parent_targetTab">
        <input type="hidden" value="131" name="FLOW_ID" id="FLOW_ID">
        <input type="hidden" value="4" id="PRCS_ID">
        <input type="hidden" value="" name="CHILD_FLOW" id="CHILD_FLOW">
        <input type="hidden" value="" name="CHILD_FLOW_NAME" id="CHILD_FLOW_NAME">-->
    </div>
</div>