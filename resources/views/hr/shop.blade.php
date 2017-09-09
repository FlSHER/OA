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
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                店铺管理
            </header>
            <!-- 筛选 start -->
            @include('hr/shop_filter')
            <!-- 筛选 end -->
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="shop_list">
                </table>
            </div>
        </section>
    </div>
    <section id="board-right"></section>
</div>

<!-- AddByOne -->
<button id="openAddByOne" data-toggle="modal" href="#addByOne" class="hidden"></button>
<div id="addByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">新建店铺</h4>
        </div>
        <div class="modal-content">
            <form id="addForm" name="addForm" class="form-horizontal" method="post" action="{{source(route('hr.shop.submit'))}}">
                @inject('HRM','HRM')
                @include('hr/shop_form',['type'=>'add'])
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
            <h4 class="modal-title">编辑店铺</h4>
        </div>
        <div class="modal-content">
            <form id="editForm" name="editForm" class="form-horizontal" method="post" action="{{source(route('hr.shop.submit'))}}">
                @include('hr/shop_form',['type'=>'edit'])
                <input type="hidden" name="id" >
            </form>
        </div>
    </div>
</div>

@endsection


@section('js')
<!-- zTree js -->
<script type="text/javascript" src="{{source('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!--data table-->
<script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
<!--script for this view-->
<script type="text/javascript" src="{{source('js/HR/shop.js')}}"></script>
<script>
var columns = [
    {data: "shop_sn", title: "店铺代码"},
    {data: "name", title: "店铺名称"},
    {data: "department.name", title: "所属部门"},
    {data: "brand.name", title: "所属品牌"},
    {data: "{province.name}.'-'.{city.name}.'-'.{county.name}.' '.{address}", title: "店铺地址"},
    {data: "manager_name", title: "店长"},
    {data: "staff.realname", title: "店员", searchable: false, sortable: false, defaultContent: "",
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html = '';
            for (var i in oData.staff) {
                var staff = oData.staff[i];
                html += staff.realname + ',';
            }
            $(nTd).html(html);
        }
    },
    {data: "id", title: "操作", sortable: false, width: "50px",
        createdCell: function (nTd, sData, oData, iRow, iCol) {
            delete oData.password;
            delete oData.salt;
            var html = '';
<?php if (check_authority(72)): ?>
                html += '<button class="btn btn-sm btn-default" title="编辑" onclick=\'edit(' + sData + ')\'><i class="fa fa-edit fa-fw"></i></button> ';
<?php endif; ?>
<?php if (check_authority(73)): ?>
                html += '&nbsp;<button class="btn btn-sm btn-danger" title="删除" onclick="del(' + sData + ')"><i class="fa fa-trash-o fa-fw"></i></button>';
<?php endif; ?>
            $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
        }
    }
];
var buttons = [
    {"text": "<i class='fa fa-plus fa-fw'></i>", "action": addShop, "titleAttr": "添加"}
];
</script>


@endsection