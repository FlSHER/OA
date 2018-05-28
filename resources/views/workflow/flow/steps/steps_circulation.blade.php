<!--流转设置-->
<!--steps_circulaton-->
<div class="tab-pane" id="circulation">
    <div class="flow_top_type clearfix">
        <div class="float_left"><span class="icon20-circulation" id="titlecirculation">流转设置</span><span class="icon20-circulation" id="titlesoftcirculation" style="display: none;">流转设置(柔性节点)</span></div>
        <!--<div class="step_name_intro">步骤名称:</div>-->
        <div class="step_name_intro">
            <span class="flow_intro">步骤名称:</span>
            <span class="flow_name">{{$data['prcs_name'] or ''}}</span>

        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#entrust').click(function () {
                $('.entrust_tip').toggleClass('active');
            });
        });
        $(document).ready(function () {
            $('#entrust1').click(function () {
                $('.entrust_tip1').toggleClass('active');
            });
        });
    </script> 
    <style>
        .entrust{
            color: #3f9bca;
            position: relative;
        }
        .entrust_tip{
            display: none;
            top: -120px;
            width: 400px;
            left: 25px;
            position: absolute;
            border: 1px solid rgb(220, 220, 220);
            background: #FFFBD7;;
            padding: 10px;
            border-radius: 3px;
        }
        .entrust1{
            color: #3f9bca;
            position: relative;
        }
        .entrust_tip1{
            display: none;
            top: -52px;
            width: 260px;
            left: 25px;
            position: absolute;
            border: 1px solid rgb(220, 220, 220);
            background: #FFFBD7;;
            padding: 10px;
            border-radius: 3px;
        }
        .active.entrust_tip{
            display: block;
        }
        .active.entrust_tip1{
            display: block;
        }
    </style>
    <div style="clear: both;">
        <div style="width:436px;float:left;">
            <div class="f_field_block" id="operatorBy">
                <div class="f_field_label">
                    <span class="f_field_title">主办人相关选项</span>
                </div>
                <div class="f_field_ctrl_circulation" id="top_default">
                    @if(isset($data))
                    <!--编辑-->
                     <span>
                         <input type="radio" name="top_default" value="0" @if($data['top_default'] == 0) checked @endif /> 明确指定主办人
                     </span>	
                    <span>
                        <input type="radio" style="margin-left:15px;" name="top_default" value="2" @if($data['top_default'] == 2) checked @endif /> 无主办人会签
                    </span>		
                    <span>
                        <input type="radio" style="margin-left:15px;" name="top_default" value="1" @if($data['top_default'] == 1) checked @endif > 先接收者为主办人	
                    </span>
                    @else
                    <!--新增-->
                    <span><input type="radio" name="top_default" value="0" checked> 明确指定主办人</span>	
                    <span>
                        <input type="radio" style="margin-left:15px;" name="top_default" value="2" > 无主办人会签
                    </span>		
                    <span><input type="radio" style="margin-left:15px;" name="top_default" value="1"  > 先接收者为主办人</span>	
                    @endif
                    <div><span class="f_field_explain_title_green">默认设置为：明确指定主办人</span></div>
                </div>
            </div> 
<!--            <div class="f_field_block clear" id="modify">
                <div class="f_field_label" id="circulation_no_user"><span class="f_field_title">是否允许修改主办人相关选项</span></div>
                <div class="f_field_label" id="circulation_intelligent_user" style="display:none"><span class="f_field_title">是否允许修改主办人相关选项及默认经办人</span></div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="user_lock" value="0" <?php if(isset($data['user_lock'])){
                                  if($data['user_lock'] == 0){ echo "checked";}
                           } ?> > 不允许		
                    <input type="radio" style="margin-left:15px;" name="user_lock" value="1" <?php if(isset($data['user_lock'])){
                                  if($data['user_lock'] == 1){ echo "checked";}
                           }else{ echo "checked";} ?>> 允许     
                </div>
            </div>-->
            <div class="f_field_block clear">
                <div class="f_field_label">
                    <span class="f_field_title">是否允许会签</span>	
                </div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="feedback" value="0" <?php if(isset($data['feedback'])){
                        if($data['feedback'] == 0){ echo "checked";}
                    }else{ echo "checked";}?>> 允许会签		
                    <input type="radio" style="margin-left:15px;" name="feedback"  value="1" <?php if(isset($data['feedback'])){
                        if($data['feedback'] == 1){ echo "checked";}
                    }?>> 禁止会签		
                    <input type="radio" style="margin-left:15px;" name="feedback"  value="2" <?php if(isset($data['feedback'])){
                        if($data['feedback'] == 2){ echo "checked";}
                    }?>> 强制会签			
                    <div><span class="f_field_explain_title_green">如设置强制会签，则不会签不能进行办理完毕操作</span></div>
                </div>
            </div>
            <div class="f_field_block clear" id="signlook">
                <div class="f_field_label clear"><span class="f_field_title">会签意见可见性</span></div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="signlook" value="0" <?php if(isset($data['signlook'])){
                        if($data['signlook'] == 0){ echo "checked";}
                    }else{ echo "checked";}?>> 总是可见
                    <input type="radio" style="margin-left:15px;" name="signlook" value="1" <?php if(isset($data['signlook'])){
                        if($data['signlook'] == 1){ echo "checked";}
                    }?>> 本步骤经办人之间不可见
                    <input type="radio" style="margin-left:15px;" name="signlook" value="2" <?php if(isset($data['signlook'])){
                        if($data['signlook'] == 2){ echo "checked";}
                    }?>> 针对其他步骤不可见
                </div>
            </div>
<!--            <div class="f_field_block clear">
                <div class="f_field_label"><span class="f_field_title">经办人未办理完毕时是否允许主办人强制转交</span></div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="turn_priv" value="1"<?php if(isset($data['turn_priv'])){
                        if($data['turn_priv'] == 1){ echo "checked";}
                    }else{ echo "checked";}?>> 允许
                    <input type="radio" style="margin-left:15px;" name="turn_priv" value="0" <?php if(isset($data['turn_priv'])){
                        if($data['turn_priv'] == 0){ echo "checked";}
                    }?>> 不允许
                </div>
            </div>-->
<!--            <div class="f_field_block clear" id="autonextwork">
                <div class="f_field_label"><span class="f_field_title">自动选择下一步骤</span>
                    <a href="javascript:void(0);" class="entrust" id="entrust">
                        <span class="icon18-illustration"></span>
                        <div id="entrust_tip" class="entrust_tip">
                            <label><b>[</b>说明<b>]</b></label>
                            <label><b>1.</b>【经办权限】按【角色】或【部门】授权，如果此【角色】或【部门】中无人员时，自动选择下一步骤。</label>
                            <label><b>2.</b>上一步骤和下一步骤的主办人(不包含经办人)相同时，自动选择下一步骤。</label>
                        </div>
                    </a>
                </div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="AUTO_NEXT_PRCS" value="0" checked=""> 否		
                    <input type="radio" style="margin-left:15px;" name="AUTO_NEXT_PRCS" value="1"> 是
                    <div><span class="f_field_explain_title_green">说明：</span></div>
                    <div><span class="f_field_explain_title_green">1)【经办权限】按【角色】或【部门】授权，如果此【角色】或【部门】中无人员时，自动选择下一步骤</span></div>
                    <div><span class="f_field_explain_title_green">2)只有主办人(不包含经办人)相同时，自动选择下一步骤</span></div>
                </div>
            </div>-->

            <!--<div class="f_field_block clear">
<div class="f_field_label"><span class="f_field_title">当前步骤的主办人和下一步骤主办人相同时,是否自动转交下一步</span></div>
<div class="f_field_ctrl_circulation">
            <input type="radio" name="IS_MYSELF" value="0" checked > 否				<input type="radio" style="margin-left:15px;" name="IS_MYSELF" value="1" > 是                <div><span class="f_field_explain_title_green">注:只有主办人(不包含经办人)相同时，此设置起作用</span></div>
</div>
    </div>-->
        </div>
        <div class="right_body">
            <div class="f_field_block">
                <div class="f_field_label"><span class="f_field_title">是否允许退回</span></div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="allow_back" value="0" <?php if(isset($data['allow_back'])){
                        if($data['allow_back'] == 0){ echo "checked";}
                    }else{ echo "checked";}?>> 不允许				
                    <input type="radio" style="margin-left:15px;" name="allow_back"  value="1" <?php if(isset($data['allow_back'])){
                        if($data['allow_back'] == 1){ echo "checked";}
                    }?>> 允许退回上一步骤		
                    <input type="radio" style="margin-left:15px;" name="allow_back"  value="2"  <?php if(isset($data['allow_back'])){
                        if($data['allow_back'] == 2){ echo "checked";}
                    }?>> 允许退回之前步骤
                </div>
            </div>
<!--            <div class="f_field_block clear" id="backflow" style="display: none;">
                <div class="f_field_label clear"><span class="f_field_title">是否重新走流程</span></div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="BACKFLOW" value="0" checked=""> 是				
                    <input type="radio" style="margin-left:15px;" name="BACKFLOW" value="1"> 否            
                </div>
            </div>-->
<!--            <div class="f_field_block clear" id="bingfa">
                <div class="f_field_label"><span class="f_field_title">是否允许并发</span></div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="sync_deal" value="0" <?php if(isset($data['sync_deal'])){
                        if($data['sync_deal'] == 0){ echo "checked";}
                    }else{ echo "checked";}?>> 禁止并发				
                    <input type="radio" style="margin-left:15px;" name="sync_deal" value="1"  <?php if(isset($data['sync_deal'])){
                        if($data['sync_deal'] == 1){ echo "checked";}
                    }?>> 允许并发				
                    <input type="radio" style="margin-left:15px;" name="sync_deal" value="2" <?php if(isset($data['sync_deal'])){
                        if($data['sync_deal'] == 2){ echo "checked";}
                    }?>> 强制并发            
                </div>
            </div>-->

<!--            <div class="f_field_block clear" id="merge">
                <div class="f_field_label">
                    <span class="f_field_title">并发合并选项</span>
                </div>
                <div class="f_field_ctrl_circulation clear">
                    <input type="radio" name="gather_node" value="0" <?php if(isset($data['gather_node'])){
                        if($data['gather_node'] == 0){ echo "checked";}
                    }else{ echo "checked";}?>> 非强制合并	
                    <input type="radio" style="margin-left:15px;" name="gather_node" value="1" <?php if(isset($data['gather_node'])){
                        if($data['gather_node'] == 1){ echo "checked";}
                    }?>> 强制合并		
                    <div><span class="f_field_explain_title_green">非强制合并：此步骤主办人在并发分支中任意分支转至后即可进行转交</span></div>
                    <div><span class="f_field_explain_title_green">强制合并：所有可能转至此步骤的分支步骤必须全部办理完毕，此步骤才可以转交</span></div>
                </div>
            </div>-->
            @if(isset($data))
            <!--编辑start-->
                @if($data['flow_type_view_priv'] ==1)
                    <div class="f_field_block clear">
                        <div class="f_field_label"><span class="f_field_title">传阅设置</span></div>
                        <div class="f_field_ctrl_circulation" id="view_priv">
                            <input type="radio" name="view_priv" value="1"  @if($data['view_priv'] == 1)checked @endif /> 允许		
                            <input type="radio" style="margin-left:15px;" name="view_priv" value="0" @if($data['view_priv'] == 0) checked @endif /> 不允许       
                        </div>
                    </div>
                @endif
                 <!--编辑end-->
            @else
            <!--新增start-->
                @if($view_priv ==1)
                <div class="f_field_block clear">
                    <div class="f_field_label"><span class="f_field_title">传阅设置</span></div>
                    <div class="f_field_ctrl_circulation" id="view_priv">
                        <input type="radio" name="view_priv" value="1" > 允许		
                        <input type="radio" style="margin-left:15px;" name="view_priv" value="0" checked> 不允许            
                    </div>
                </div>
                @endif
            <!--新增end-->
            @endif
            
<!--            <div class="f_field_block clear">
                <div class="f_field_label"><span class="f_field_title">结束整个流程</span>
                    <a href="javascript:void(0);" class="entrust1" id="entrust1">
                        <span class="icon18-illustration"></span>
                        <div id="entrust_tip1" class="entrust_tip1">
                            <label><b>[</b>说明<b>]</b></label>
                            <label>针对一个流程多个结束步骤时起作用</label>
                        </div>
                    </a>
                </div>
                <div class="f_field_ctrl_circulation">
                    <input type="radio" name="over_flow" value="0"  <?php if(isset($data['over_flow'])){
                        if($data['over_flow'] == 0){ echo "checked";}
                    }else{ echo "checked";}?>> 否				
                    <input type="radio" style="margin-left:15px;" name="over_flow" value="1"  <?php if(isset($data['over_flow'])){
                        if($data['over_flow'] == 1){ echo "checked";}
                    }?>> 是            
                </div>
            </div>-->
        </div>
    </div>
</div>
