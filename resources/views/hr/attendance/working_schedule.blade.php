@extends('layouts.admin')

@inject('authority','Authority')
@inject('HRM','HRM')

@section('css')
    <!-- data table -->
    <link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}"/>
    <!-- checkbox -->
    <link rel="stylesheet" href="{{source('css/checkbox.css')}}"/>
@endsection



@section('content')
    <div class="row">
        <div class="col-lg-8">
            <section class="panel">
                <header class="panel-heading">
                    店铺排班表
                </header>
                <!-- 筛选 start -->
            @include('hr/attendance/working_schedule_filter')
            <!-- 筛选 end -->
                <!-- 列表 start -->
                <div class="panel-body">
                    <table class="table table-striped table-bordered dataTable no-footer" id="transfer">
                    </table>
                </div>
                <!-- 列表  end -->
            </section>
        </div>
        <section id="board-right"></section>
    </div>

    <!-- AddByOne -->
    <div id="addByOne" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">添加排班</h4>
            </div>
            <div class="modal-content">
                <form id="addForm" name="addForm" class="form-horizontal" method="post"
                      action="{{route('hr.working_schedule.submit')}}">
                    @include('hr/attendance/working_schedule_form',['type'=>'add'])
                </form>
            </div>
        </div>
    </div>

    <!-- EditByOne -->
    <div id="editByOne" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">编辑排班</h4>
            </div>
            <div class="modal-content">
                <form id="editForm" name="editForm" class="form-horizontal" method="post"
                      action="{{route('hr.working_schedule.submit')}}">
                    @include('hr/attendance/working_schedule_form',['type'=>'edit'])
                    <input type="hidden" name="id">
                </form>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <!--data table-->
    <script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
    <!--script for this view-->
    <script type="text/javascript" src="{{source('js/HR/working_schedule.js')}}"></script>

    <script>

        var columns = [
            {data: "staff_sn", title: "员工编号"},
            {data: "staff_name", title: "员工姓名"},
            {data: "shop_sn", title: "店铺代码"},
            {data: "shop.name", title: "店铺名称", searchable: false},
            {data: "shop_duty.name", title: "当日职务", searchable: false},
            {data: "clock_in", title: "上班时间", searchable: false},
            {data: "clock_out", title: "下班时间", searchable: false},
            {
                data: "{staff_sn}.'-'.{shop_sn}", title: "操作", sortable: false, width: "50px", searchable: false,
                createdCell: function (nTd, sData, oData, iRow, iCol) {
                    var html = '<button class="btn btn-sm btn-default" title="编辑" onclick="edit(\'' + sData + '\')"><i class="fa fa-edit fa-fw"></i></button>';
                    html += ' <button class="btn btn-sm btn-danger" title="删除" onclick="deleteByOne(\'' + sData + '\')"><i class="fa fa-trash-o fa-fw"></i></button>';
                    $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
                }
            }
        ];

        var buttons = [
            {"text": "{{date('Y-m-d')}}", className: "working_schedule_date", "titleAttr": "选择日期"},
            {"text": "<i class='fa fa-plus fa-fw'></i>", "action": add, "titleAttr": "添加"},
        ];

    </script>
@endsection