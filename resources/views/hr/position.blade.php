@extends('layouts.admin')

@inject('authority','App\Services\AuthorityService')

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />
<!-- validity -->
<link rel="stylesheet" href="{{asset('plug_in/validity/jquery.validity.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
<!-- checkbox -->
<link rel="stylesheet" href="{{asset('css/checkbox.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8">
        <section class="panel">
            <header class="panel-heading">
                职位列表
            </header>
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="position_list"></table>
            </div>
        </section>
    </div>
</div>

<!-- AddStaffByOne -->
<button id="openAddPositionByOne" data-toggle="modal" href="#addPositionByOne" class="hidden"></button>
<div id="addPositionByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">添加职位</h4>
        </div>
        <div class="modal-content">
            <form id="addPositionForm" class="form-horizontal" method="post" action="{{asset(route('hr.position.add'))}}">
                <div class="modal-body">
                    @include('hr/position_form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EditStaffByOne -->
<button id="openEditPositionByOne" data-toggle="modal" href="#editPositionByOne" class="hidden"></button>
<div id="editPositionByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">编辑职位</h4>
        </div>
        <div class="modal-content">
            <form id="editPositionForm" class="form-horizontal" method="post" action="{{asset(route('hr.position.edit'))}}">
                <div class="modal-body">
                    @include('hr/position_form')
                    <input type="hidden" name="id" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">确认</button>
                </div>
            </form>
        </div>
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
<!-- validity -->
<script type="text/javascript" src="{{asset('plug_in/validity/jquery.validity.js')}}"></script>
<!-- zTree js -->
<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!--script for this view-->
<script src="{{asset('js/HR/position.js')}}"></script>
<script>
var columns = [
    {"data": "id", "title": "编号", "sortable": true, "width": "30px"},
    {"data": "name", "title": "职位名称", "sortable": true, "width": "120px"},
    {"data": "level", "title": "职级", "sortable": true, "width": "30px"},
    {"data": "is_public", "title": "是否共享", "sortable": true, "width": "60px",
        "render": function (data, type, row, meta) {
            return data === 0 ? '<span class="text-danger">否</span>' : '<span class="text-success">是</span>';
        }
    },
    {"data": "brand.name", "title": "关联品牌", "sortable": false, "defaultContent": "",
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            var html = sData.join(','), htmlView;
            if (html.length > 40) {
                htmlView = html.substring(0, 38) + "...";
            } else {
                htmlView = html;
            }
            $(nTd).html(htmlView).attr("title", html);
        }
    },
    {"data": "id", "title": "操作", "sortable": false, "width": "130px",
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            delete oData.password;
            delete oData.salt;
            var html = '';
<?php if ($authority->checkAuthority(63)): ?>
                html += '<button class="btn btn-sm btn-default" title="编辑" onclick=\'editPosition(' + JSON.stringify(oData) + ')\'><i class="fa fa-edit fa-fw"></i></button>';
<?php endif; ?>
<?php if ($authority->checkAuthority(61)): ?>
                html += ' <button class="btn btn-sm btn-default" title="权限管理" onclick=\'editAuthority({"position_id":' + sData + '})\'><i class="fa fa-key fa-fw"></i></button>';
<?php endif; ?>
<?php if ($authority->checkAuthority(64)): ?>
                html += '&nbsp;<button class="btn btn-sm btn-danger" title="删除" onclick="deletePosition(' + sData + ')"><i class="fa fa-trash-o fa-fw"></i></button>';
<?php endif; ?>
            $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
        }
    }
];

var buttons = [
    {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "titleAttr": "可见字段"},
    {"text": "<i class='fa fa-refresh fa-fw'></i>", "titleAttr": "刷新", "action": function () {
            table.fnDraw();
        }
    }
];
<?php if ($authority->checkAuthority(62)): ?>
    buttons.push({"text": "<i class='fa fa-plus fa-fw'></i>", "action": addPosition, "titleAttr": "添加"});
<?php endif; ?>
</script>
@endsection