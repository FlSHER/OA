@extends('layouts.admin')
@section('css')
@parent
<!--<link href="{{asset('css/workflow/demo_page.css')}}" rel="stylesheet"/>
<link href="{{asset('css/workflow/demo_table.css')}}" rel="stylesheet"/>
<link rel="stylesheet" href="{{asset('css/workflow/DT_bootstrap.css')}}"/>-->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<!-- <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}"/> -->
<!-- zTree css -->
<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
<!-- tab css -->
<link rel="stylesheet" href="{{asset('css/workflow/tab/css/style.css')}}" />
<!--self css-->
<link rel="stylesheet" href="{{asset('css/workflow/flow/style.css')}}" />

@stop
@section('content')
@include('workflow.common.common')
@inject('HRM','HRM')
<!--flow_config_list-->
<div class="col-lg-12">
    <section class="panel">
        <header class="panel-heading">
            工作流程设置<span style="color: #65cea7;"> / </span>工作流设置
        </header>
        <div style="float: right;position: relative;">
            <a href="#myModal-1" data-toggle="modal" class="btn btn-success" type="button" id="createClassify" style="border: 1px solid #3079ed;color: #ffffff;background-color:#4b8cf7;position:relative;top:8px;right:15px;font-family: Simsun, Arial, sans-serif;font-weight: bold;font-size: 13px;">新建流程
            </a>
            <div hidden class="formConfigHidden"style="width:100%;float:right;color:red; text-align:center;margin-top:-20px;"></div>
        </div>
        <!--创建工作流start-->
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-1" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h4 class="modal-title" id="formConfigTitle">新建流程</h4>
                    </div>
                    <div class="modal-body" style="padding-bottom:0px;">
                        @include('workflow.flow.flow_new_tabs')
                    </div>
                    <hr>
                    <div class="modal-body">
                        <div class="form-group" style="margin-top: -25px;">

                            <div class="col-lg-offset-2 col-lg-10">
                                <button type="button" class="btn btn-primary" id="lastStep">上一步</button>
                                <button type="button" class="btn btn-primary" id="nextStep" style="margin-left:20px;">下一步</button>
                                <button type="button" class="btn btn-primary" id="save" style="margin-left:20px;">保存</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--创建工作流end-->

        

        <!--列表数据start-->
        <div class="panel-body">
            <div class="adv-table">
                
            </div>
        </div>
        <!--列表数据end-->
    </section>
</div>
@include('workflow.flow.flow_show_list')
@endsection
@section('js')
@parent
<!--<script type="text/javascript" language="javascript" src="{{asset('js/workflow/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/DT_bootstrap.js')}}"></script>
<script src="{{asset('js/workflow/dynamic_table_init.js')}}"></script>-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>

<!-- zTree js -->
<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/tab/js/functions.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/tab/js/lanren.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/flow/flow_config_list.js')}}"></script>

<!--flow_show_list-->
<script src="/js/workflow/flow/flow_left_menu.js"></script>
<script>
//初始化列表
$(function(){
    ulListInit();
});
</script>

@stop
