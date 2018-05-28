@extends('layouts.admin')

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{source('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{source('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{source('plug_in/datatables/css/buttons.bootstrap.css')}}" />
<!-- validity -->
<link rel="stylesheet" href="{{source('plug_in/validity/jquery.validity.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{source('plug_in/ztree/css/metroStyle.css')}}" />
<!-- checkbox -->
<link rel="stylesheet" href="{{source('css/checkbox.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8">
        <section class="panel">
            <header class="panel-heading">
                角色列表
            </header>
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="list"></table>
            </div>
        </section>
    </div>
    <!--    <div class="col-sm-4">
            <section class="panel">
                <header class="panel-heading">
                    详细信息
                </header>
                <div class="panel-body">
    
                </div>
            </section>
        </div>-->
</div>

<!-- Add By One -->
<button id="openAddByOne" data-toggle="modal" href="#addByOne" class="hidden"></button>
<div id="addByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">添加角色</h4>
        </div>
        <div class="modal-content">
            <form id="addForm" class="form-horizontal" method="post" action="{{asset(route('system.role.add'))}}">
                <div class="modal-body">
                    @include('system/role_form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit By One -->
<button id="openEditByOne" data-toggle="modal" href="#editByOne" class="hidden"></button>
<div id="editByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">编辑职位</h4>
        </div>
        <div class="modal-content">
            <form id="editForm" class="form-horizontal" method="post" action="{{asset(route('system.role.edit'))}}">
                <div class="modal-body">
                    @include('system/role_form')
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

<!-- Edit Authorities -->
@include('system/edit_authority')

@endsection

@section('js')
<!--data table-->
<script type="text/javascript" src="{{source('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{source('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{source('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{source('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{source('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<!-- validity -->
<script type="text/javascript" src="{{source('plug_in/validity/jquery.validity.js')}}"></script>
<!-- zTree js -->
<script type="text/javascript" src="{{source('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!--script for this view-->
<script src="{{source('js/system/role.js')}}"></script>
<script>
var dataTableColumns = [
    {"data": "id", "title": "编号", "width": "30px"},
    {"data": "role_name", "title": "角色名称", "width": "120px"},
    {"data": "staff.realname", "title": "关联员工", "sortable": false, "defaultContent": "",
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            var html = sData.join(','), htmlView;
            if (html.length > 20) {
                htmlView = html.substring(0, 18) + "...";
            } else {
                htmlView = html;
            }
            $(nTd).html(htmlView).attr("title", html);
        }
    },
    {"data": "brand.name", "title": "配属品牌", "sortable": false,
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            var html = sData.length == 0 ? "" : sData.join(','), htmlView;
            if (html.length > 15) {
                htmlView = html.substring(0, 13) + "...";
            } else {
                htmlView = html;
            }
            $(nTd).html(htmlView).attr("title", html);
        }
    },
    {"data": "department.name", "title": "配属部门", "sortable": false,
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            var html = sData.length == 0 ? "" : sData.join(','), htmlView;
            if (html.length > 20) {
                htmlView = html.substring(0, 18) + "...";
            } else {
                htmlView = html;
            }
            $(nTd).html(htmlView).attr("title", html);
        }
    },
    {"data": "id", "title": "操作", "sortable": false, "width": "130px",
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            delete oData.staff;
            var html = '<button class="btn btn-sm btn-default" title="编辑" onclick=\'editByOne(' + JSON.stringify(oData) + ')\'><i class="fa fa-edit fa-fw"></i></button>' +
                    ' <button class="btn btn-sm btn-default" title="权限管理" onclick=\'editAuthority({"role_id":' + sData + '})\'><i class="fa fa-key fa-fw"></i></button>' +
                    ' <button class="btn btn-sm btn-default" title="关联员工" onclick=\'setStaff(' + sData + ')\'><i class="fa fa-users fa-fw"></i></button>' +
                    '&nbsp;<button class="btn btn-sm btn-danger" title="删除" onclick="deleteByOne(' + sData + ')"><i class="fa fa-trash-o fa-fw"></i></button>';
            $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
        }
    }
];

var dataTableButtons = [
    {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "titleAttr": "可见字段", "className": "btn-primary"},
    {"text": "<i class='fa fa-refresh fa-fw'></i>", "titleAttr": "刷新", "className": "btn-primary", "action": function () {
            table.fnDraw();
        }
    },
    {"text": "<i class='fa fa-plus fa-fw'></i>", "titleAttr": "添加", "action": addByOne}
];
</script>
@endsection