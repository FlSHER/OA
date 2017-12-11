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
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    请假列表
                </header>
                <!-- 筛选 start -->
            @include('hr/attendance/leave_filter')
            <!-- 筛选 end -->
                <!-- 列表 start -->
                <div class="panel-body">
                    <table class="table table-striped table-bordered dataTable no-footer" id="leave_table">
                    </table>
                </div>
                <!-- 列表  end -->
            </section>
        </div>
        <section id="board-right"></section>
    </div>

    <!-- Edit -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">提前结束</h4>
            </div>
            <div class="modal-content">
                <form id="editForm" class="form-horizontal" method="post" action="/hr/leave/edit">
                    @include('hr/attendance/leave_from')
                    <input type="hidden" name="id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-success" id="daoruid">确认</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@section('js')
    <!--data table-->
    <script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
    <!--script for this view-->
    <script type="text/javascript" src="{{source('js/HR/leave.js')}}"></script>

    <script>

        var columns = [
            {data: "id", title: "编号"},
            {data: "staff_sn", title: "员工编号"},
            {data: "staff_name", title: "员工姓名"},
            {data: "start_at", title: "开始时间", searchable: false},
            {data: "end_at", title: "结束时间", searchable: false},
            {data: "duration", title: "请假时长"},
            {data: "(['-2'=>'已撤回','-1'=>'已驳回','0'=>'审批中','1'=>'已通过'][{status}])", name: "status", title: "状态"},
            {data: "clock_out_at", title: "开始打卡时间", searchable: false},
            {data: "clock_in_at", title: "结束打卡时间", searchable: false},
            {data: "approver_name", title: "审批人"},
            {data: "created_at", title: "提交时间", visible: false, searchable: false},
            {
                data: "id", title: "操作",
                createdCell: function (nTd, sData, oData, iRow, iCol) {
                    var html = '';

                    @if($authority->checkAuthority(125))
                        html += '<button class="btn btn-sm btn-default" title="提前结束" onclick="edit(' + sData + ')"><i class="fa fa-edit fa-fw"></i></button>';
                    @endif

                            @if($authority->checkAuthority(126))
                    if (oData.status == 1) {
                        html += '&nbsp;<button class="btn btn-sm btn-danger" title="撤销" onclick="cancel(' + sData + ')"><i class="fa fa-times fa-fw"></i></button>';
                    }
                    @endif
                    $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
                }
            }
        ];

        var buttons = [];
        buttons.push('export:/hr/leave/export');

    </script>
@endsection