@extends('layouts.admin')

@inject('authority','Authority')
@inject('HRM','HRM')

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{source('plug_in/ztree/css/metroStyle.css')}}" />
<!-- checkbox -->
<link rel="stylesheet" href="{{source('css/checkbox.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                员工列表
            </header>
            <!-- 筛选 start -->
            @include('hr/staff/staff_filter')
            <!-- 筛选 end -->
            <!-- 列表 start -->
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="example">
                </table>
            </div>
            <!-- 列表 end -->
        </section>
    </div>
    <section id="board-right"></section>
</div>
<?php if ($authority->checkAuthority(89)) { ?>
    <!-- AddStaffByList -->
    <button id="openAddStaffByList" data-toggle="modal" href="#addStaffByList" class="hidden"></button>
    <div id="addStaffByList" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">批量导入</h4>
            </div>
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group text-center">
                        <a href="{{source('template/批量入职.xlsx')}}">员工入职导入模板</a>
                        <a href="{{source('template/批量变动.xlsx')}}" style="margin-left:20px;">人事变动导入模板</a>
                    </div>
                    <div class="row">
                        <div class="col-lg-2"></div>
                        <label class="control-label col-lg-8">
                            <a class="btn btn-block btn-default">批量导入</a>
                            <input class="hidden" type="file" name="staff" id="import_staff">
                        </label>
                    </div>
                </div>
                <div class="modal-body" id="import_result">

                </div>
            </div>
        </div>
    </div>
<?php } ?>
@if($authority->checkAuthority(54))
<!-- EntryStaff -->
@include('hr/staff/staff_form',['type'=>'entry'])
@endif
@if($authority->checkAuthority(82))
<!-- EditByOne -->
@include('hr/staff/staff_form',['type'=>'edit'])
@endif
@if($authority->checkAuthority(55))
<!-- EmployByOne -->
@include('hr/staff/staff_form',['type'=>'employ'])
@endif
@if($authority->checkAuthority(56))
<!-- TransferByOne -->
@include('hr/staff/staff_form',['type'=>'transfer'])
@endif
@if($authority->checkAuthority(57))
<!-- LeaveByOne -->
@include('hr/staff/staff_form',['type'=>'leave'])
@endif
@if($authority->checkAuthority(58))
<!-- ReinstateByOne -->
@include('hr/staff/staff_form',['type'=>'reinstate'])
@endif

@endsection

@section('js')
<!--data table-->
<script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
<!-- zTree js -->
<script type="text/javascript" src="{{source('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!--script for this view-->
<script type="text/javascript" src="{{source('js/HR/staff.js')}}"></script>
<script>
var staffColumns = [
    {data: "staff_sn", title: "编号", type: 'text'},
    {data: "realname", title: "姓名", type: 'text',
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html;
            if (sData.length > 5) {
                html = sData.substring(0, 3) + "...";
            } else {
                html = sData;
            }
            $(nTd).html(html).attr("title", sData);
        }
    },
    {data: "mobile", title: "电话号码", type: 'text'},
    {"data": "username", "title": "用户名", visible: false, searchable: false,
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html;
            if (sData && sData.length > 10) {
                html = sData.substring(0, 8) + "...";
            } else {
                html = sData;
            }
            $(nTd).html(html).attr("title", sData);
        }
    },
    {data: "brand.name", title: "品牌", type: 'select', searchable: false},
    {data: "department.full_name", title: "部门全称",
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html;
            if (sData.length > 15) {
                html = "..." + sData.substring(sData.length - 15);
            } else {
                html = sData;
            }
            $(nTd).html(html).attr("title", sData);
        }
    },
    {data: "shop.name", title: "所属店铺", type: 'search:shop', visible: false, defaultContent: "",
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html = sData.length > 0 ? sData + "(" + oData.shop.shop_sn + ")" : "";
            $(nTd).html(html);
        }
    },
    {data: "position.name", title: "职位", type: 'select', searchable: false},
    {data: "status.name", title: "状态", type: 'select', searchable: false},
    {data: "gender.name", title: "性别", type: 'select', visible: false, searchable: false},
    {data: "birthday", title: "生日", type: 'date', visible: false, searchable: false},
    {data: "hired_at", title: "入职时间", type: 'date', sortable: true, visible: false, searchable: false},
    {data: "employed_at", title: "转正时间", type: 'date', sortable: true, visible: false, searchable: false},
    {data: "left_at", title: "离职时间", type: 'date', sortable: true, visible: false, searchable: false},
    {data: "staff_sn", title: "操作", sortable: false, searchable: false,
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            var availableBrands = <?php echo json_encode(array_flatten(Authority::getAvailableBrands())); ?>;
            var availableDepartments = <?php echo json_encode(array_flatten(Authority::getAvailableDepartments())); ?>;
            if (($.inArray(oData.brand_id, availableBrands) == -1 || $.inArray(oData.department_id, availableDepartments) == -1) && oData.status_id > 0) {
                $(nTd).html('无操作权限').css({"text-align": "center"});
                return false;
            }
            var html = '<button class="btn btn-sm btn-info" title="个人信息" onclick="showPersonalInfo(' + sData + ')"><i class="fa fa-address-card fa-fw"></i></button> ';
            html += '<div class="btn-group btn-group-sm">';
            if (oData.is_active == 0) {
<?php if ($authority->checkAuthority(66)) { ?>
                    html += ' <button class="btn btn-sm btn-default" title="激活" onclick="activeStaff(' + sData + ')"><i class="fa fa-unlock fa-fw"></i></button>';
<?php } ?>
            } else {
                if (oData.status_id == 1) {
<?php if ($authority->checkAuthority(55)) { ?>
                        html += ' <button class="btn btn-sm btn-default" title="转正" onclick="editStaff(' + sData + ',\'employ\')"><i class="fa fa-user-check fa-fw"></i></button>';
<?php } ?>
                }
                if (oData.status_id > 0) {
<?php if ($authority->checkAuthority(56)) { ?>
                        html += ' <button class="btn btn-sm btn-default" title="人事变动" onclick="editStaff(' + sData + ',\'transfer\')"><i class="fa fa-user-transfer fa-fw"></i></button>';
<?php } ?>
<?php if ($authority->checkAuthority(57)) { ?>
                        html += ' <button class="btn btn-sm btn-default" title="离职" onclick="editStaff(' + sData + ',\'leave\')"><i class="fa fa-user-times fa-fw"></i></button>';
<?php } ?>
                }
                if (oData.status_id === 0) {
<?php if ($authority->checkAuthority(107)) { ?>
                        html += ' <button class="btn btn-sm btn-default" title="离职交接" onclick="showStaffLeavingPage(' + sData + ')"><i class="fa fa-user-times fa-fw"></i></button>';
<?php } ?>
                }
                if (oData.status_id < 0) {
<?php if ($authority->checkAuthority(58)) { ?>
                        html += ' <button class="btn btn-sm btn-default" title="再入职" onclick="editStaff(' + sData + ',\'reinstate\')"><i class="fa fa-user-plus fa-fw"></i></button>';
<?php } ?>
                }
            }
            html += '</div>';
<?php if ($authority->checkAuthority(82)) { ?>
                html += ' <button class="btn btn-sm btn-primary" title="编辑" onclick="editStaff(' + sData + ',\'edit\')"><i class="fa fa-edit fa-fw"></i></button>';
<?php } ?>
<?php if ($authority->checkAuthority(59)) { ?>
                html += ' &nbsp;<button class="btn btn-sm btn-danger" title="删除" onclick="deleteStaff(' + sData + ')"><i class="fa fa-trash-o fa-fw"></i></button>';
<?php } ?>
            $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
        }
    }
];
var buttons = [];
<?php if ($authority->checkAuthority(54)) { ?>
    buttons.push({"text": '<i class="fa fa-user-plus fa-fw"></i>', "action": entryStaff, "titleAttr": "入职"});
<?php } ?>

var multibutton = [];
<?php if ($authority->checkAuthority(59)) { ?>
    multibutton.push({"text": "批量变动", "action": function () {
            var checked = [];
            $("input[name=check]:checked").each(function () {
                checked.push($(this).val());
            });
            editStaff(checked, 'transfer');
        }
    });
<?php } ?>
<?php if ($authority->checkAuthority(89)) { ?>
    multibutton.push({
        "text": "批量导入", "action": function () {
            $("#openAddStaffByList").click();
        }
    });
<?php } ?>
if (multibutton.length > 0) {
    buttons.push({"text": "<i class='fa fa-users fa-fw'></i>", "titleAttr": "批量操作", "extend": "collection",
        "buttons": multibutton
    });
}


<?php if ($authority->checkAuthority(84)) { ?>
    buttons.push({"text": "<i class='fa fa-download fa-fw'></i>", "action": exportStaff, "titleAttr": "导出为Excel"});
<?php } ?>
</script>
@endsection