@extends('layouts.admin')

@inject('authority','App\Services\AuthorityService')

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                员工管理操作列表
            </header>
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="main_table"></table>
            </div>
        </section>
    </div>
</div>
<!-- EditAuthorities -->
@include('system/edit_authority')

@endsection

@section('js')
<!--data table-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<!--script for this view-->
<script src="{{asset('js/log/staff_log.js')}}"></script>
<script>
var columns = [
    {"data": "staff_sn", "title": "员工编号", "width": "60"},
    {"data": "staff.realname", "title": "员工姓名", "width": "60", "sortable": false},
    {"data": "admin.realname", "title": "操作人员", "width": "60", "sortable": false,
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            var html = sData;
            if (oData.admin_sn == '999999') {
                html = '开发者';
            }
            nTd.innerHTML = html;
        }
    },
    {"data": "operation_type", "title": "操作类型", "width": "60"},
    {"data": "created_at", "title": "操作时间", "width": "115"},
    {"data": "operate_at", "title": "执行时间", "width": "115"},
    {"data": "operation_remark", "title": "操作备注", "sortable": false},
    {"data": "changes", "title": "操作详情", "sortable": false,
        "render": function (data) {
            var html = "";
            for (var i in data) {
                html += '<b>' + i + '</b>' + ':' + data[i][0] + '->' + data[i][1] + '<b>;</b> &nbsp;';
            }
            return html;
        }
    }
];

var buttons = [
    {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "titleAttr": "可见字段", "className": "btn-primary"},
    {"text": "<i class='fa fa-refresh fa-fw'></i>", "titleAttr": "刷新", "className": "btn-primary", "action": function () {
            table.fnDraw();
        }
    }
];
</script>
@endsection