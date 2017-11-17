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
                    打卡记录
                </header>
                <!-- 筛选 start -->
            @include('hr/attendance/clock_filter')
            <!-- 筛选 end -->
                <!-- 列表 start -->
                <div class="panel-body">
                    <table class="table table-striped table-bordered dataTable no-footer" id="clock_table">
                    </table>
                </div>
                <!-- 列表  end -->
            </section>
        </div>
        <section id="board-right"></section>
    </div>

    <!-- makeClock -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">打卡补签</h4>
            </div>
            <div class="modal-content">
                <form id="makeClock" class="form-horizontal" method="post"
                      action="{{route('hr.attendance.make_clock')}}">
                    @include('hr/attendance/make_clock_form')
                </form>
            </div>
        </div>
    </div>

    <!-- edit -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">编辑</h4>
            </div>
            <div class="modal-content">
                <form id="editForm" name="editForm" class="form-horizontal" method="post"
                      action="{{route('hr.clock.edit')}}">
                    @include('hr/attendance/clock_form')
                    <input type="hidden" name="id">
                    <input type="hidden" name="ym">
                </form>
            </div>
        </div>
    </div>

@endsection


@section('js')
    <!--data table-->
    <script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
    <!--script for this view-->
    <script type="text/javascript" src="{{source('js/HR/clock.js')}}"></script>

    <script>

        var columns = [
            {data: "id", title: "编号"},
            {data: "staff_sn", title: "员工编号"},
            {data: "staff.realname", title: "员工姓名", defaultContent: '资料缺失'},
            {data: "shop_sn", title: "店铺代码"},
            {data: "shop.name", title: "店铺名称", defaultContent: '资料缺失'},
            {
                data: "{attendance_type}.{type}", title: "打卡类型",
                render: function (data) {
                    switch (data) {
                        case '11':
                            return '上班';
                            break;
                        case '12':
                            return '下班';
                            break;
                        case '22':
                            return '调动出发';
                            break;
                        case '21':
                            return '调动到达';
                            break;
                        case '32':
                            return '请假开始';
                            break;
                        case '31':
                            return '请假结束';
                            break;
                    }
                }
            },
            {data: "clock_at", title: "打卡时间", searchable: false},
            {data: "punctual_time", title: "计划打卡时间", searchable: false, defaultContent: '无'},
            {data: "lng", title: "定位(经度 E)", searchable: false, visible: false},
            {data: "lat", title: "定位(纬度 N)", searchable: false, visible: false},
            {data: "address", title: "地址", searchable: false, visible: false},
            {data: "operator_sn", title: "操作人编号", visible: false},
            {data: "operator.realname", title: "操作人", defaultContent: '资料缺失'},
            {
                data: "id", title: "操作", searchable: false,
                createdCell: function (nTd, sData, oData, iRow, iCol) {
                    var html = '';
                    var month = $('.clock_month').val();
                    @if($authority->checkAuthority(131))
                        html += '<button class="btn btn-sm btn-default" title="编辑" onclick="edit(' + sData + ',\'' + month + '\')"><i class="fa fa-edit fa-fw"></i></button>';
                    @endif
                            @if($authority->checkAuthority(130))
                    if (oData.is_abandoned == 0) {
                        html += '&nbsp;<button class="btn btn-sm btn-danger" title="作废" onclick="abandon(' + sData + ',\'' + month + '\')"><i class="fa fa-trash-o fa-fw"></i></button>';
                    } else {
                        html += '&nbsp;<button class="btn btn-sm btn-default" title="作废" disabled><i class="fa fa-trash-o fa-fw"></i></button>';
                    }
                    @endif
                    $(nTd).html(html).css({"padding": "5px", "text-align": "center"});

                }
            }
        ];

        var buttons = [
            {"text": "{{date('Y-m')}}", className: "clock_month", "titleAttr": "选择月份"},
        ];
        @if($authority->checkAuthority(125))
        //        buttons.push({"text": '<i class="fa fa-plus fa-fw"></i>', "action": add, "titleAttr": "创建"});
        @endif

    </script>
@endsection