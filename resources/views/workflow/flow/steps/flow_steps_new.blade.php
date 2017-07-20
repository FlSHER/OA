<!DOCTYPE html>
<html>
    <!--flow_steps_new-->
    <head>
        <title>流程设计器</title>
        <meta name="_token" content="{{csrf_token()}}"/>
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/theme/index.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/intro/show_guide.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/bootstrap.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/list.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/models/style.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/guide.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/design.css')}}">
        <script type="text/javascript" src="{{asset('js/jquery-3.1.1.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/workflow/flow/steps/flow_steps_new.js')}}"></script>
    </head>
    {{-- <script type="text/javascript" src="{{asset('js/bootstrap.min.js')}}"></script> --}}

<body class="win8_module_system" style="background-color:#ffffff;">
    <div id="center">
        <div id="tabs_w0_panel" class="tabs-panel selected" style="height:100%;">
            <div data-spy="scroll" data-target=".bs-docs-sidebar" marginwidth="0" marginheight="0">
                <div class="tabbable design-wrap" style="min-width:700px;">
                    <ul id="myTab" class="nav nav-tabs">
                        <li class="active" hidden>
                            <a href="#designer" data-toggle="tab" onclick="showSaveLayout('pic');" id="pic_list">图形视图</a>
                        </li>
                        <li class="">
                            <a href="#list" data-toggle="tab" onclick="showSaveLayout('list');" id="list_pic">列表视图</a>
                        </li>
                        <li id="result_block" style="color:red;"></li>
                        <div style="float:right;">
                            <button class="btn btn-primary" onclick="add_new_prcs('./AddFlowStepsList?flow_id={{$tmp['flow_id']}}')" style="cursor: pointer;margin-top:3px;margin-right:5px;" id="createStep" type="button">新建步骤</button>

                            {{-- <button class="btn btn-success" style="cursor: pointer; margin-top: 3px; display: none;" id="saveLayout" type="button" onclick="SavePosition();">保存布局</button>
							
							<button class="btn btn-info" style="cursor: pointer;margin-top:3px;" type="button" onclick="windowRefresh();">刷新</button>
							
							<button class="btn btn-info" style="cursor: pointer;margin-top:3px;" type="button" onclick="document.execCommand('Print');" title="直接打印流程页面">打印</button>
							
							<button class="btn btn-info" style="cursor: pointer; margin-top:3px;" type="button" onclick="copy_main();" title="复制至剪贴版，可粘贴至Word">复制</button>
							
							<button class="btn btn-warning" style="cursor: pointer;margin-right:5px;margin-top:3px;" type="button" onclick="cancel('135');">关闭</button> --}}
                            <div></div>
                        </div>
                    </ul>
                    <div class="tab-content" style="overflow:auto;">
                        <div class="tab-pane" id="designer" style="height: 309px;"></div>	
                        <div class="tab-pane active" id="list" style="background-color: #ffffff">
                            <table border="0" width="100%" cellspacing="0" cellpadding="3" class="small">
                                <tbody>
                                    <tr>
                                        <td class="Big">
                                            <span class="big3" style="margin-left:30px;">
                                                <font style="color:#94918c;font-weight:bold;font-size:14px;">流程名称：</font>
                                                <font style="color:rgb(60, 40, 143);font-weight:normal;font-size:12px;">{{$tmp['flow_name']}}</font>
                                            </span>
                                            <br>
                                            <span class="small1" style="color:green;margin-left:30px;">请设定好各步骤的可写字段和经办权限（此经办权限是经办人员、经办部门、经办角色的合集）</span>
                                            <span style="display:none;">
                                                <a href="#" onclick="loadTip()" id="tip_step"></a>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="f_table_list_div">
                                <table id="f_list_table" class="table table-bordered f_table_list">
                                    <thead>
                                        <tr class="f_prcs_list_thead b_color_odd">
                                            <td style="text-align:center;">步骤id</td>
                                            <td style="text-align:center;">名称</td>
                                            <td style="text-align:center;">下一步骤</td>
                                            <td style="text-align:center;">编辑该步骤的各项属性</td>
                                            <td style="text-align:center;">操作</td>
                                        </tr>
                                    </thead>
                                    <tbody id="guide_list">
                                        @foreach($data as $d => $v)
                                        <tr>
                                            <td style="text-align:center">{{$v['prcs_id']}}</td>
                                            <td style="text-align:center">{{$v['prcs_name']}}</td>
                                            <td style="text-align:center">{{($d+1)>count($data)-1?'结束':$data[($d+1)>count($data)-1?$d:$d+1]['prcs_id']}}</td>
                                            <td style="text-align:center">
                                                <a href="javascript:;" onclick="add_prcs('{{$v['id']}}', 'basis')">基本属性</a>&nbsp;
                                                <a href="javascript:;" onclick="add_prcs('{{$v['id']}}', 'operator')">经办权限</a>&nbsp;
                                                <a href="javascript:;" onclick="add_prcs('{{$v['id']}}', 'writable')">可写字段</a>&nbsp;
                                                <a href="javascript:;" onclick="add_prcs('{{$v['id']}}', 'hidden')">保密字段</a>&nbsp;
                                                <a href="javascript:;" onclick="add_prcs('{{$v['id']}}', 'required')">必填字段</a>&nbsp;
                                                <a href="javascript:;" onclick="add_prcs('{{$v['id']}}', 'condition')" style="display: none;">条件设置</a>&nbsp;
                                            </td>
                                            <td style="text-align:center">
                                                <a href="javascript:clone_item({{$v['id']}})">克隆</a>
                                                <a href="javascript:delete_item({{$v['id']}});">删除</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="parent_targetTab" id="parent_targetTab" value="">
                            <input type="hidden" name="reloadFlag" id="reloadFlag" value="">
                        </div>	
                    </div>
                    <input type="hidden" name="flow_id" value="{{$tmp['flow_id']}}">
                </div>
            </div>
        </div>	
    </div>
</body>
</html>

