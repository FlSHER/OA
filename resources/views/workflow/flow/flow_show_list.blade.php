<!--flow_show_list-->
<style>
	#flow_tree a{color:#1687cb;font-size: 14px;}
	#flow_tree ul li ul{margin: 0 0 10px 25px;}
	#right_body a{text-decoration:none;}
	.flow-show-list-div-input{display:block;}
	.flow-show-list-div-input>input{display:inline-block;border-radius:4px;border: 1px solid #cccccc;margin-top:10px;padding:4px 6px;margin-bottom:10px;font-size:13px;line-height:20px;width:194px;}
	/*.bottom_info_right_top1>span{float:right;}*/
	/*.bottom_info_right_top1>button{float:right;}*/
	
	.workflow-welcome-list {list-style-type: none;}
	ul, ol {margin-top: 0;margin-bottom: 10px;}
	.workflow-welcome-msg {overflow: hidden;height: 80px;line-height: 22px;background: #fafafa;padding: 10px;margin-right: 12px;}

	.guide_definition {width: 356px;float: left;margin-bottom: 7px;height: 80px;margin-left: 20px;cursor: pointer;}
	.guide_definition:hover{background: #c4e1fc;}
	.icon64-guide_img_1{background: url(/css/workflow/images/guide_diyilc_1.png) no-repeat;width: 64px;height: 64px;margin-left: 20px;margin-top: 10px;float: left;}
	.icon64-guide_img_2 {background: url(/css/workflow/images/guide_diyilc_2.png) no-repeat;width: 64px;height: 64px;margin-left: 20px;margin-top: 10px;float: left;}
	.icon64-guide_img_3 {background: url(/css/workflow/images/guide_diyilc_3.png) no-repeat;width: 64px;height: 64px;margin-left: 20px;margin-top: 10px;float: left;} 
	.icon64-guide_img_4 {background: url(/css/workflow/images/guide_diyilc_4.png) no-repeat;width: 64px;height: 64px;margin-left: 20px;margin-top: 10px;float: left;}
	.guide_title {width: 240px;height: 60px;margin-left: 10px;margin-top: 12px;float: left;}
	.guide_title a {font-size: 16px;color: #000000;font-weight: bold;margin-top: 5px;}
</style>
<div class="col-lg-12" id="flow_tree">
	<section class="panel" style="margin-top: -19px;">
		<div id="left_tree" style="width: 14%;height:720px;background-color:#ffffff;float: left;padding:15px;">
			<!--流程左侧树形列表文件菜单-->
			<div class="flow-show-list-div-input">
				<input type="text" name="flow_search" placeholder="流程检索..." id="flow_search">
				<img src="/css/workflow/images/search.png" style="position:absolute;top:14px;left:195px;display: block;width:15px;height:15px; " id="img_serach"/>
			</div>
			<ul id="flow_tree_menu" style="height:100%;overflow:hidden;overflow-y:visible;display:block;">
			</ul>
		</div>
		<div id="right_body" style="width: 86%;height:720px;float:right;background-color:#ffffff;">
			<div id="explain" style="margin-top:0px;">
				<div style="height:25px;padding-bottom:10px;">
					<div style="float:left;width:60%"></div>
					<div style="color:green;float:right;margin-right:15px;line-height:25px;"><font style="font-size: 14px;font-weight:bold;color:red;">温馨提醒：</font>您还没有选中流程，请您先选中一条流程!</div>
				</div>
				<ul class="workflow-welcome-list">
					<li class="workflow-welcome-list-item">
						<div class="workflow-welcome-msg">
							<div style="font-size:16px;font-weight:bold;color:#3f9bca;"><img src="/css/workflow/images/first.png"><font style=" margin-left:8px;vertical-align: middle;">定义流程基本属性</font></div>
							<ul>
								<li style="margin-top:3px;color:#9da9b4;;list-style-type:disc;"><font style="color:#2d374b;font-size:12px;">用于定义流程的基本属性,工作名称和文号,流程说明,扩展字段。 可配置内容包括:流程名称,表单,流程类型等。</font></li>
								<li style="margin-top:3px;color:#9da9b4;;list-style-type:disc;margin-top:3px;"><font style="color:red;font-size:12px;">注:</font><font style="color:green;font-size:12px;">1.有流程发起之后表单类型和流程类型不可更换。 2.只有自由委托才允许定义委托规则，委托后更新自己步骤为办理完毕，主办人变为经办人。</font></li>
							</ul>
						</div>
					</li>
					<li class="workflow-welcome-list-item" style="margin-top:30px;">
						<div class="workflow-welcome-msg">
							<div style="font-size:16px;font-weight:bold;color:#3f9bca;"><img src="/css/workflow/images/second.png"><font style=" margin-left:8px;vertical-align: middle;">设计流程步骤</font></div>
							<ul>
								<li style="margin-top:3px;color:#9da9b4;list-style-type:disc;"><font style="color:#2d374b;font-size:12px;">用于设计流程在实际应用中的模型。 可配置内容包括:步骤基本属性,经办权限,字段权限,条件设置等。</font></li>
								<li style="margin-top:3px;color:#9da9b4;list-style-type:disc;margin-top:3px;"><font style="color:red;font-size:12px;">注:</font><font style="color:green;font-size:12px;">序号必须为数字，流程的开始步骤序号必须为1。</font></li>
							</ul>
						</div>
					</li>
					<li class="workflow-welcome-list-item" style="margin-top:30px;">
						<div class="workflow-welcome-msg">
							<div style="font-size:16px;font-weight:bold;color:#3f9bca;"><img src="/css/workflow/images/third.png"><font style=" margin-left:8px;vertical-align: middle;">新建流程管理权限说明</font></div>
							<ul>
								<li style="margin-top:3px;color:#9da9b4;list-style-type:disc;"><font style="color:#2d374b;font-size:12px;">用于管理流程在实际应用中的相关权限。 权限类型包括:管理 ,监控 ,查询 ,编辑 ,点评 。</font></li>
								<li style="margin-top:3px;color:#9da9b4;list-style-type:disc;margin-top:3px;"><font style="color:red;font-size:12px;">注:</font><font style="color:green;font-size:12px;">如果不是从业务引擎发起的工作，授权范围（人员）（部门）（角色）至少要先选择其中的一项。</font></li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
			{{-- @include('workflow.flow.flow_data_page') --}}
		</div>
	</section>
</div>