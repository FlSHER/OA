<!--flow_attribute-->
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">
        <meta name="_token" content="{{csrf_token()}}">
        <link rel="shortcut icon" href="#" type="image/png">

        <title>定义流程基本属性</title>
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        <link href="{{asset('css/style-responsive.css')}}" rel="stylesheet">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/_admin/js/html5shiv.js"></script>
        <script src="/_admin/js/respond.min.js"></script>
        <![endif]-->
        <!--<link href="{{asset('css/workflow/demo_page.css')}}" rel="stylesheet"/>
        <link href="{{asset('css/workflow/demo_table.css')}}" rel="stylesheet"/>
        <link rel="stylesheet" href="{{asset('css/workflow/DT_bootstrap.css')}}"/>-->
        <link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
        <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}"/>
        <!-- zTree css -->
        <link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
        <!-- tab css -->
        <link rel="stylesheet" href="{{asset('css/workflow/tab/css/style.css')}}" />
        <!--self css-->
        <link rel="stylesheet" href="{{asset('css/workflow/flow/style.css')}}" />

        


</head>

@inject('HRM','HRM')

<!--创建工作流start-->
<div class="modal fade in" style="display: block;background-color: rgba(0,0,0,0);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button> --}}
                <h4 class="modal-title" id="formConfigTitle">修改流程属性</h4>
            </div>
            <div class="modal-body" style="padding-bottom:0px;">
                @include('workflow.flow.flow_new_tabs')
            </div>
            <hr>
            <div class="modal-body">
                <div class="form-group" style="margin-top: -25px;">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button type="button" class="btn btn-primary" style="background-color: #424F63;" id="lastStep">上一步</button>
                        <button type="button" class="btn btn-primary" id="nextStep" style="background-color: #424F63;margin-left:20px;">下一步</button>
                        @if(isset($skip))
                        <button type="button" class="btn btn-primary" id="updateSave" style="margin-left:20px;background-color: #424F63;">保存</button>
                        @else
                        <button type="button" class="btn btn-primary" id="save" style="margin-left:20px;background-color: #424F63;">保存</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--创建工作流end-->



<!--<script type="text/javascript" language="javascript" src="{{asset('js/workflow/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/DT_bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/dynamic_table_init.js')}}"></script>-->
<script type="text/javascript" src="{{asset('js/jquery-3.1.1.min.js')}}"></script>
<!--<script src="{{asset('js/jquery-1.10.2.min.js')}}"></script>-->
<!--<script type="text/javascript" src="{{asset('js/jquery-migrate-1.2.1.min.js')}}"></script>-->
<script type="text/javascript" src="{{asset('js/bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('js/modernizr.min.js')}}"></script>

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
