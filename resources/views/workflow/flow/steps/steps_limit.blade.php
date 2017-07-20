<!--办理时限-->
<!--steps_limit-->
<!--<div class="tab-pane" id="limit">
	<style>
    .entrust2{
        color: #3f9bca;
        position: relative;
    }
    .entrust_tip2{
        display: none;
        top: -130px;
        width: 400px;
        left: 25px;
        position: absolute;
        border: 1px solid rgb(220, 220, 220);
        background: #FFFBD7;;
        padding: 10px;
        border-radius: 3px;
    }
    .active.entrust_tip2{
        display: block;
    }
	</style>
	<div class="flow_top_type clearfix">
		<div style="float:left"><span class="icon20-limit">办理时限</span></div>
		<div style="float:right;margin-top:5px;margin-right:5px;"class="step_name_intro">步骤名称:</div>
		<div class="step_name_intro">
			<span class="flow_intro">步骤名称:</span>
			<span class="flow_name">rr</span>

		</div>
	</div>
	<div style="clear: both;">
		<div class="f_field_block">
			<div class="f_field_ctrl"> 
				<input type="text" name="TIME_OUT" style="width:60px;margin-top:10px;" value=""><button class="btn" type="button" style="color:inherit;">小时</button>                 
			</div>
		</div>
		<div class="f_field_block">
			<div class="f_field_label"><span class="f_field_title">是否允许转交时设置办理时限</span></div>
			<div class="f_field_ctrl">                  
				<label class="radio" style="float: left;">
					<input type="radio" id="time_out_modify1" name="TIME_OUT_MODIFY" value="1">允许 
				</label>
				<label class="radio" style="float: left;margin-left:15px;">
					<input type="radio" id="TIME_OUT_MODIFY0" name="TIME_OUT_MODIFY" value="0" checked="">不允许 
				</label>
			</div>
		</div>
		<div style="clear:both;height:10px;"></div>
		<div class="f_field_block">
			<div class="f_field_label"><span class="f_field_title">超时计算方法</span></div>
			<div class="f_field_ctrl">                  
				<label class="radio" style="float: left;">
					<input type="radio" name="TIME_OUT_TYPE" id="time_out_type1" value="0">本步骤接收后开始计时			</label>
					<label class="radio" style="float: left;margin-left:15px;">
						<input type="radio" name="TIME_OUT_TYPE" id="time_out_type2" value="1" checked="">上一步骤转交后开始计时			</label>
						<input type="hidden" name="TIME_OUT_TYPE" value="1">
			</div>
		</div>
		<div style="clear:both;height:10px;"></div>
		<div class="f_field_block">
			<div class="f_field_label">
				<span class="f_field_title">工作天数换算方式</span>
				<a href="javascript:void(0);" class="entrust2" id="entrust2">
					<span class="icon18-illustration"></span>
					<div id="entrust_tip2" class="entrust_tip2">
						<label><b>[</b>说明<b>]</b></label>
						<label>工作超时时间或停留时间等换算成天数时的换算方式。</label>
						<label><b>1.</b>以24小时为一天的方式进行换算，所有办理人统一。</label>
						<label><b>2.</b>以个人排班类型工作时长为一天进行换算。如果设置为排除非工作时段并假设办理人一天工作时长为8小时，则按8小时为一天进行换算；如果设置为不排除非工作时段，则按24小时为一天进行换算。</label>
					</div>
				</a>
			</div>
			<div class="f_field_ctrl">                  
				<label class="radio" style="float: left;">
				<input type="radio" name="WORKINGDAYS_TYPE" id="workingdays_type1" value="0" checked="">24小时为一天</label>
				<label class="radio" style="float: left;margin-left:15px;">
				<input type="radio" name="WORKINGDAYS_TYPE" id="workingdays_type2" value="1">按个人排班类型工作时长为一天</label>
			</div>
		</div>
		<div style="clear:both;height:10px;"></div>
		<div class="f_field_block">
				<div class="f_field_label"><span class="f_field_title">是否排除非工作时段(按排班类型)</span></div>
				<div class="f_field_ctrl">                  
					<label class="radio" style="float: left;">
					<input type="radio" name="TIME_OUT_ATTEND" value="0" id="time_out_attend1" checked="">否</label>
					<label class="radio" style="float: left;margin-left:15px;">
					<input type="radio" name="TIME_OUT_ATTEND" value="1" id="time_out_attend2">是</label>
				</div>
			</div>
	</div>					
</div>-->
