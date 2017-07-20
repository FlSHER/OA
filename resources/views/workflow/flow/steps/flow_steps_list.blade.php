<!DOCTYPE html>
<html>
    <!--flow_steps_list-->
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">
        <meta name="_token" content="{{csrf_token()}}">
        <meta charset="utf-8">
        <title>设计流程步骤</title>
        <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/bootstrap.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/navigate.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/style.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/autocomplete.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/guide.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/workflow/steps/ui.custom.css')}}">
        
        <!-- data table -->
        <link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
        <link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
        <link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />
        <!-- zTree css -->
        <link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />

        <script type="text/javascript" src="{{asset('js/jquery-3.1.1.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/workflow/flow/steps/flow_steps_list.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/workflow/bootstrap.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/workflow/bootstrap.js')}}"></script>
        
    </head>
    <body>
    <input type="hidden" id="id_name" value="{{isset($id_name) ? $id_name : ''}}"/>
        <div>	
            <!-- ======================-左start=========================== -->
            <div class="span3" id="nav_left">
                <ul class="nav nav-list bs-docs-sidenav" id="address">
                    <li class="active" item="first" index="1" id="basis_li">
                        <a href="#basis" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-basis_hover">基本设置</span>
                        </a>
                    </li>
                    <li class="" index="2" id="operator_li">
                        <a href="#operator" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-operator">经办人</span>
                        </a>
                    </li>
                    <li class="" index="3" id="intelligent_li">
                        <a href="#intelligent" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-intelligent">智能选人</span>
                        </a>
                    </li>
                    <li class="" index="4" id="circulation_li">
                        <a href="#circulation" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-circulation">流转设置</span>
                        </a>
                    </li>
                    <li class="" index="5" id="writable_li">
                        <a href="#writable" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-writable">可写字段</span>
                        </a>
                    </li>
                    <li class="" index="6" id="hidden_li">
                        <a href="#hidden" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-hidden">保密字段</span>
                        </a>
                    </li>
                    <li class="" index="12" id="required_li">
                        <a href="#required" data-toggle="tab"><i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-required">必填字段</span>
                        </a>
                    </li>
                    <li class="" index="7" id="condition_li" >
                        <a href="#condition" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-condition">条件设置</span>
                        </a>
                    </li>
                    <li class="" index="8" id="limit_li" style="display: none;">
                        <a href="#limit" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-limit">办理时限</span>
                        </a>
                    </li>
                    <li class="" index="9" id="unit_li" style="display: none;">
                        <a href="#unit" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-unit">触发器</span>
                        </a>
                    </li>
                    <li class="" index="10" id="remind_li" style="display: none;">
                        <a href="#remind" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-remind">提醒设置</span>
                        </a>
                    </li>
                    <li class="" item="last" index="11" id="aip_li" style="display: none;">
                        <a href="#aip" data-toggle="tab">
                            <i class="icon-chevron-right" style="margin-top:8px;"></i>
                            <span class="icon20-aip">呈批单设置</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!--====================-左end=============================-->

            <!--=======================内容start=========================-->
            @inject('HRM','HRM')
            <div class="nav-right-container" id="nav-right-container" style="height:900px">
                <form action="" method="post" name="flow_step_define" id="flow_step_define" target="iframe_prcs_new">	
                    <input type="hidden" id="submit_check_form" value="1"/>
                    <input type="hidden" id="flow_type" value="{{$flow_type or $data['flow_type']}}"/>
                    <input type="hidden" id="key_id" name="id" value="{{$data['id'] or ''}}"/>
                    <div class="tab-content nav-right">
                        <!--基本设置-->
                        @include('workflow.flow.steps.steps_basis')
                        <!--经办人-->
                        @include('workflow.flow.steps.steps_operator')
                        <!--流转设置-->
                        @include('workflow.flow.steps.steps_circulation')
                        <!--保密字段-->
                        @include('workflow.flow.steps.steps_hidden')
                        <!--可写字段-->
                        @include('workflow.flow.steps.steps_writable')
                        <!--必填字段-->
                        @include('workflow.flow.steps.steps_required')
                          <!--条件设置--> 
                        @include('workflow.flow.steps.steps_condition') 
                        <!--智能选人-->
                         @include('workflow.flow.steps.steps_intelligent') 
                        <!--触发器-->
                        {{-- @include('workflow.flow.steps.steps_unit') --}}
                        <!--呈批单设置--> 
                        {{-- @include('workflow.flow.steps.steps_aip') --}}
                        <!--办理时限-->
                        {{-- @include('workflow.flow.steps.steps_limit') --}}
                        <!--提醒设置--> 
                        {{-- @include('workflow.flow.steps.steps_remind') --}}
                      
                    </div>

                </form>
            </div>
            <!--====================内容end=======================================-->
        </div>
        <div class="work_bottom">

            <div class="work_center">

                <div class="btn_close" hidden>
                    <button class="btn btn-warning" type="button" onclick="check_back();">关闭</button>
                </div>
                <div class="btn_save">
                    <button class="btn btn-success" id="prcs_checkForm" type="button">保存</button>
                </div>
                <div class="btn_next" name="btn_next">
                    <button class="btn btn-primary next_step" id="next_step" type="button">下一步</button>
                </div>
                <div class="btn_next" name="btn_prev">
                    <button class="btn btn-primary" id="btn_prev" type="button" disabled="disabled">上一步</button>
                </div>
            </div>  

        </div>

    </body>
    
    <!--data table-->
    <script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
    <!-- zTree js -->
    <script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>

    
    <!--基本设置-->
    <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_basis.js')}}"></script>
    <!--经办人-->
    <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_operator.js')}}"></script>
    <!--可写字段-->
    <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_writable.js')}}"></script>
    <!--保密字段-->
    <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_hidden.js')}}"></script>
    <!--必填字段-->
    <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_required.js')}}"></script>
    <!--流转设置-->
    <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_circulation.js')}}"></script>
    <!--条件设置--> 
     <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_condition.js')}}"></script>
     <!--智能选人--> 
     <script type="text/javascript" src="{{asset('js/workflow/flow/steps/steps_interlligent.js')}}"></script>
</html>