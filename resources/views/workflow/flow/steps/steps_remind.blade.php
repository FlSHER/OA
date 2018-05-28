<!--提醒设置-->
<!--steps_remind-->
<!--<div class="tab-pane" id="remind">
	<div class="flow_top_type clearfix">
		<div style="float:left"><span class="icon20-remind_2">事务提醒设置</span></div>
		<div style="float:right;margin-top:5px;margin-right:5px;" class="step_name_intro">步骤名称:</div>
		<div class="step_name_intro">
			<span class="flow_intro">步骤名称:</span>
			<span class="flow_name">1</span>

		</div>
	</div>	
	<div class="circulation_sponsor" style="clear: both;">		
		<div class="remind_sponsor_content">
			<div class="remind_sponsor_checkbox">
				<label class="checkbox inline">
					<input type="checkbox" name="REMIND_ORNOT" id="REMIND_ORNOT" onclick="remind_fld.disabled= (this.checked? false : true);"> <b>此步骤是否独立设置提醒方式</b>
				</label>
			</div>
		</div>
		<fieldset id="remind_fld" style="margin-top:5px;">
			<div class="remind_sponsor_content2">
				<div class="remind_sponsor_revise_left guide_clear">提醒开启状态</div>
				<div class="remind_sponsor_revise_left2">下一步骤</div>
				<div class="remind_checkbox">
					<label class="checkbox inline">
						<input type="checkbox" name="SMS_REMIND_NEXT" id="SMS_REMIND_NEXT"><span class="icon16-remind_3" title="事务提醒"></span>
					</label>
					<label class="checkbox inline">
						<input type="checkbox" name="SMS2_REMIND_NEXT" id="SMS2_REMIND_NEXT"><span class="icon16-remind_2" title="手机短信提醒"></span>
					</label>
					<label class="checkbox inline">
						<input type="checkbox" name="WEBMAIL_REMIND_NEXT" id="WEBMAIL_REMIND_NEXT"><span class="icon16-remind_1" title="Internet邮件提醒"></span>
					</label>	
				</div>
				<div class="remind_sponsor_revise_left2">发起人</div>
				<div class="remind_checkbox">
					<label class="checkbox inline">
						<input type="checkbox" name="SMS_REMIND_START" id="SMS_REMIND_START"><span class="icon16-remind_3" title="事务提醒"></span>
					</label>
					<label class="checkbox inline">
						<input type="checkbox" name="SMS2_REMIND_START" id="SMS2_REMIND_START"><span class="icon16-remind_2" title="手机短信提醒"></span>
					</label>
					<label class="checkbox inline">
						<input type="checkbox" name="WEBMAIL_REMIND_START" id="WEBMAIL_REMIND_START"><span class="icon16-remind_1" title="Internet邮件提醒"></span>
					</label>	
				</div>
				<div class="remind_sponsor_revise_left2">全部经办人</div>
				<div class="remind_checkbox">
					<label class="checkbox inline">
						<input type="checkbox" name="SMS_REMIND_ALL" id="SMS_REMIND_ALL"><span class="icon16-remind_3" title="事务提醒"></span>
					</label>
					<label class="checkbox inline">
						<input type="checkbox" name="SMS2_REMIND_ALL" id="SMS2_REMIND_ALL"><span class="icon16-remind_2" title="手机短信提醒"></span>
					</label>
					<label class="checkbox inline">
						<input type="checkbox" name="WEBMAIL_REMIND_ALL" id="WEBMAIL_REMIND_ALL"><span class="icon16-remind_1" title="Internet邮件提醒"></span>
					</label>	
				</div>
			</div>
		</fieldset>
	</div>

	<div class="flow_top_type clearfix">
		<div style="float:left"><span class="icon20-remind_3">转交时内部邮件通知以下人员</span></div>
		<div style="float:right;margin-top:5px;margin-right:5px;"></div>
	</div>
	<div class="f_field_block" style="clear:both;">
		<div class="f_field_label"><span class="f_field_title">通知范围（人员）</span></div>
		<div class="f_field_ctrl">
			<input type="hidden" name="MAIL_TO" value="">
			<textarea name="MAIL_TO_NAME" wrap="yes" style="width:500px;height:60px;" readonly=""></textarea>
			<a href="javascript:;" class="orgAdd" onclick="SelectUser('5','','MAIL_TO', 'MAIL_TO_NAME','','flow_step_define')">添加</a>
			<a href="javascript:;" class="orgClear" onclick="ClearUser('MAIL_TO', 'MAIL_TO_NAME')">清空</a>
		</div>
	</div>
	<div class="f_field_block">
		<div class="f_field_label"><span class="f_field_title">通知范围（部门）</span></div>
		<div class="f_field_ctrl">
			<input name="MAIL_TO_DEPT" id="F_MAIL_TO_DEPT" type="hidden" value="">
			<textarea name="MAIL_TO_DEPT_NAME" style="width: 500px; height: 60px;" wrap="yes" readonly=""></textarea>
			<a class="orgAdd" onclick="SelectDept('','MAIL_TO_DEPT','MAIL_TO_DEPT_NAME','','flow_step_define')" href="javascript:;">添加</a>
			<a class="orgClear" onclick="ClearUser('MAIL_TO_DEPT', 'MAIL_TO_DEPT_NAME')" href="javascript:;">清空</a>
		</div>
	</div>
	<div class="f_field_block">
		<div class="f_field_label"><span class="f_field_title">通知范围（角色）</span></div>
		<div class="f_field_ctrl">
			<input name="MAIL_TO_PRIV" id="F_MAIL_TO_PRIV" type="hidden" value="">
			<textarea name="MAIL_TO_PRIV_NAME" style="width: 500px; height: 60px;" wrap="yes" readonly=""></textarea>
			<a class="orgAdd" onclick="SelectPriv('','MAIL_TO_PRIV','MAIL_TO_PRIV_NAME','','flow_step_define')" href="javascript:;">添加</a>
			<a class="orgClear" onclick="ClearUser('MAIL_TO_PRIV', 'MAIL_TO_PRIV_NAME')" href="javascript:;">清空</a>
		</div>
	</div>
</div>-->
