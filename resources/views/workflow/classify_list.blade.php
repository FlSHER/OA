@extends('layouts.admin')
@section('css')
@parent
<!--classify_list-->
<!-- data table -->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
@stop
@section('content')
@include('workflow.common.common')
<style>
    .table td,.table th{
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
<div class="col-lg-8">
    <section class="panel">
        <header class="panel-heading">
            工作流程设置<span style="color: #65cea7;"> / </span>分类设置
        </header>
        <header class="panel-heading custom-tab dark-tab">
            <ul class="nav nav-tabs">

                <li class="active">
                    <a data-toggle="tab" href="#formTab">
                        表单分类
                    </a>
                </li>
                <li class="">
                    <a data-toggle="tab" href="#flowTab">
                        流程分类
                    </a>
                </li>
            </ul>
        </header>
        <div class="panel-body">
            <div class="tab-content">
                <!--表单分类start-->
                <div id="formTab" class="tab-pane active">

                    @include('workflow.form_classify_list')
                </div>
                <!--表单分类end-->

                <!--流程分类start-->
                <div id="flowTab" class="tab-pane ">
                    @include('workflow.flow_classify_list')
                </div>
                <!--流程分类end-->
            </div>
        </div>
    </section>
</div>
@endsection
@section('js')
@parent
<!--data table-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>

<!--表单分类js-->
<script type="text/javascript" src="{{asset('js/workflow/form/formClassifyList.js')}}"></script>
<!--流程分类js-->
<script type="text/javascript" src="{{asset('js/workflow/form/flowClassifyList.js')}}"></script>
<script>
//验证名称是否重复
function checkRepetition(vesselId, field, url) {
    var val = $('#' + vesselId).val();
    var text = $('#' + vesselId).parent().prev().text().match(/[\u4e00-\u9fa5]+/g);
    var data = {};
    data[field] = val;
    var id = $('.deleteId').find('input[name="id"]').val();
    if (id != null) {
        data.id = id;
    }
    var _this = $('#' + vesselId);
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        success: function (msg) {
            if (msg == 'error') {
                _this.val(text + '不能重复');
                _this.css('color', 'red');
                $('#validateTijiao').val('0');
            } else if (val === '') {
                _this.val(text + '不能为空');
                _this.css('color', 'red');
                $('#validateTijiao').val('0');
            } else {
                $('#validateTijiao').val('1');
            }
        }
    });
}

//分类的点击事件刷新table表
$('.nav-tabs li a').on("click", function () {
    var link = $(this).attr("href");
    switch (link) {
        case "#formTab":
            formTable.fnDraw();
            break;
        case "#flowTab":
            flowTable.fnDraw();
            break;
    }
});

//---------------------------------------------流程分类end-----------------------------------------
var wAlert = window.alert;
window.alert = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        var realert = iwindow.alert(message);
        iframe.parentNode.removeChild(iframe);
        return realert;
    } catch (exc) {
        return wAlert(message);
    }
};
var wConfirm = window.confirm;
window.confirm = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        var reConfirm = iwindow.confirm(message);
        iframe.parentNode.removeChild(iframe);
        return reConfirm;
    } catch (exc) {
        return wConfirm(message);
    }
};
</script>
@stop