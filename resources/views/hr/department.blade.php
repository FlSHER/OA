@extends('layouts.admin')

@inject('authority','App\Services\AuthorityService')

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />
<!-- validity css -->
<link rel="stylesheet" href="{{asset('plug_in/validity/jquery.validity.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
<!-- checkbox -->
<link rel="stylesheet" href="{{asset('css/checkbox.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <section class="panel">
            <header class="panel-heading">
                部门列表
            </header>
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="department_list"></table>
            </div>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="panel">
            <header class="panel-heading custom-tab dark-tab">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#treeview" data-toggle="tab">部门结构</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="treeview">
                        <div class="btn-group" id="nestable_list_menu">
                            <a type="button" class="btn btn-sm btn-default" href="javascript:expandAll()">全部展开</a>
                            <a type="button" class="btn btn-sm btn-default" href="javascript:collapseAll()">全部收起</a>
                        </div>
                        <div class="ztree" id="department_tree_view" style="overflow:auto;max-height:750px;"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- AddByOne -->
<button id="openAddByOne" data-toggle="modal" href="#addByOne" class="hidden"></button>
<div id="addByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">添加部门</h4>
        </div>
        <div class="modal-content">
            <form id="addDepartmentForm" name="addDepartmentForm" class="form-horizontal" method="post" action="{{asset(route('hr.department.add'))}}">
                @inject('HRM','HRM')
                @include('hr/department_form')
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EditByOne -->
<button id="openEditByOne" data-toggle="modal" href="#editByOne" class="hidden"></button>
<div id="editByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">编辑部门</h4>
        </div>
        <div class="modal-content">
            <form id="editDepartmentForm" name="editDepartmentForm" class="form-horizontal" method="post" action="{{asset(route('hr.department.edit'))}}">
                @include('hr/department_form')
                <input type="hidden" name="id" >
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
<!-- zTree js -->
<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!--data table-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<!-- validity -->
<script type="text/javascript" src="{{asset('plug_in/validity/jquery.validity.js')}}"></script>
<!--script for this view-->
<script type="text/javascript" src="{{asset('js/HR/department.js')}}"></script>
<script>
/* -- 列表字段 -- */
var columns = [
    {data: "id", title: "编号", width: "30px"},
    {data: "name", title: "部门名称", width: "120px",
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html;
            if (sData.length > 10) {
                html = sData.substring(0, 8) + "...";
            } else {
                html = sData;
            }
            $(nTd).html(html).attr("title", oData.full_name);
        }
    },
    {data: "full_name", title: "部门全称", sortable: false, searchable: false},
    {data: "brand.name", title: "品牌", width: "50px"},
    {data: "manager_name", title: "部门负责人", width: "60px",
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            $(nTd).attr("title", "员工编号：" + oData.manager_sn);
        }
    },
//    {"data": "position.name", "title": "关联职位", "sortable": false, "defaultContent": "",
//        "createdCell": function (nTd, sData, oData, iRow, iCol) {
//            var html = sData.join(','), htmlView;
//            if (html.length > 40) {
//                htmlView = html.substring(0, 38) + "...";
//            } else {
//                htmlView = html;
//            }
//            $(nTd).html(htmlView).attr("title", html);
//        }
//    },
    {data: "id", title: "操作", sortable: false, width: "130px",
        "createdCell": function (nTd, sData, oData, iRow, iCol) {
            delete oData.password;
            delete oData.salt;
            var html = '';
<?php if ($authority->checkAuthority(40)): ?>
                html += '<button class="btn btn-sm btn-primary" title="编辑" onclick=\'editDepartment(' + sData + ')\'><i class="fa fa-edit fa-fw"></i></button>';
<?php endif; ?>
<?php if ($authority->checkAuthority(60)): ?>
                html += ' <button class="btn btn-sm btn-default" title="权限管理" onclick=\'editAuthority({"department_id":' + sData + '})\'><i class="fa fa-key fa-fw"></i></button>';
<?php endif; ?>
<?php if ($authority->checkAuthority(41)): ?>
                html += '&nbsp;<button class="btn btn-sm btn-danger" title="删除" onclick="deleteDepartment(' + sData + ')"><i class="fa fa-trash-o fa-fw"></i></button>';
<?php endif; ?>
            $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
        }
    }
];
/* -- 列表按钮 -- */
var buttons = [
    {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "titleAttr": "可见字段", "className": "btn-primary"},
    {"text": "<i class='fa fa-refresh fa-fw'></i>", "titleAttr": "刷新", "className": "btn-primary", "action": function () {
            table.fnDraw();
            $.fn.zTree.getZTreeObj("department_tree_view").reAsyncChildNodes(null, "refresh");
        }
    }
];
<?php if ($authority->checkAuthority(39)): ?>
    buttons.push({"text": "<i class='fa fa-plus fa-fw'></i>", "action": addDepartment, "titleAttr": "添加"});
<?php endif; ?>

var departmentZTreeSetting = {
    async: {
        url: "/hr/department/tree?"
    },
    edit: {
        enable: true,
        showRemoveBtn: false,
        showRenameBtn: false,
        drag: {
<?php if (!$authority->checkAuthority(65)) echo 'isMove: false,'; ?>
            isCopy: false
        }
    },
    view: {
        addHoverDom: addHoverDom,
        removeHoverDom: removeHoverDom
    },
    callback: {
        onDrop: updateOrder
    }
};
function addHoverDom(treeId, treeNode) {
    var aObj = $("#" + treeNode.tId + "_a");
    if ($("#diyBtn_" + treeNode.id).length > 0)
        return;
    var editStr = '<span id="diyBtn_' + treeNode.id + '" class="pull-right" onfocus="this.blur();">';
<?php if ($authority->checkAuthority(40)): ?>
        editStr += '&nbsp;&nbsp;<button class="btn btn-xs btn-primary" title="编辑" onclick=\'editDepartment(' + treeNode.id + ')\'><i class="fa fa-edit fa-fw"></i></button> ';
<?php endif; ?>
<?php if ($authority->checkAuthority(60)): ?>
        editStr += '<button class="btn btn-xs btn-default" title="权限管理" onclick=\'editAuthority({"department_id":' + treeNode.id + '})\'><i class="fa fa-key fa-fw"></i></button> ';
<?php endif; ?>
<?php if ($authority->checkAuthority(41)): ?>
        editStr += '&nbsp;<button class="btn btn-xs btn-danger" title="删除" onclick="deleteDepartment(' + treeNode.id + ')"><i class="fa fa-trash-o fa-fw"></i></button>';
<?php endif; ?>
    editStr += '</span>';
    aObj.append(editStr);
}

function removeHoverDom(treeId, treeNode) {
    $("#diyBtn_" + treeNode.id).unbind().remove();
}
</script>
@endsection