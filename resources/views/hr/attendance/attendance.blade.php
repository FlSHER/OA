@extends('layouts.admin')

@inject('authority','Authority')
@inject('HRM','HRM')

@section('css')
    <!-- data table -->
    <link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}"/>
    <!-- zTree css -->
    <link rel="stylesheet" href="{{source('plug_in/ztree/css/metroStyle.css')}}"/>
    <!-- checkbox -->
    <link rel="stylesheet" href="{{source('css/checkbox.css')}}"/>
@endsection



@section('content')
    <div class="row">
        <div class="col-lg-8">
            <section class="panel">
                <header class="panel-heading">
                    考勤列表
                </header>
                <!-- 筛选 start -->
            @include('hr/attendance/attendance_filter')
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

    <!-- makeClock -->
    @include('hr/attendance/make_clock_form')

    <!-- bigPhoto -->
    <div class="modal fade" id="viewMore">
        <div class="modal-dialog modal-sm">
            <div class="thumbnail">
                <img src="" width="100%">
                <h4 style="font-weight:700;"></h4>
                <p></p>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <!--data table-->
    <script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
    <!-- zTree js -->
    <script type="text/javascript" src="{{source('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
    <!--script for this view-->
    <script type="text/javascript" src="{{source('js/HR/attendance.js')}}"></script>

    <script>

        var columns = [
            {data: "id", title: "编号", searchable: false},
            {data: "shop_sn", title: "店铺代码"},
            {data: "shop_name", title: "店铺名称"},
            {
                data: "sprintf('%.2f',{sales_performance_lisha}+{sales_performance_go}+{sales_performance_group}+{sales_performance_partner})",
                name: "sales_performance",
                title: "总业绩",
                className: 'text-right'
            },
            {data: "attendance_date", title: "考勤日期", className: 'text-center', searchable: false},
            {data: "manager_name", title: "店长"},
            {data: "submitted_at", title: "提交时间", className: 'text-center', searchable: false, visible: false},
            {data: "auditor_name", title: "审核人", searchable: false, visible: false},
            {
                data: "status", title: "状态", searchable: false,
                render: function (data) {
                    var h;
                    switch (data) {
                        case -1:
                            h = '<span class="text-danger">驳回</span>';
                            break;
                        case 0:
                            h = '<span>未提交</span>';
                            break;
                        case 1:
                            h = '<span>待审核</span>';
                            break;
                        case 2:
                            h = '<span class="text-success">已通过</span>';
                            break;
                    }
                    return h;
                }
            },
            {
                data: "id", title: "异常", searchable: false,
                createdCell: function (nTd, sData, oData) {
                    var h = '';
                    if (oData['is_missing']) {
                        h += '<span class="label label-danger" style="padding-top:0.3em;top:-1px;position:relative;">漏签</span> ';
                    }
                    if (oData['is_late']) {
                        h += '<span class="label label-danger" style="padding-top:0.3em;top:-1px;position:relative;">迟到</span> ';
                    }
                    if (oData['is_early_out']) {
                        h += '<span class="label label-danger" style="padding-top:0.3em;top:-1px;position:relative;">早退</span> ';
                    }
                    $(nTd).html(h);
                }
            },
            {
                data: "id", title: "操作", sortable: false, width: "50px", searchable: false,
                createdCell: function (nTd, sData, oData, iRow, iCol) {
                    var html;
                    if (oData.status != 0) {
                        html = '<button class="btn btn-sm btn-info" title="详细信息" onclick="showPersonalInfo(' + sData + ')"><i class="fa fa-address-card fa-fw"></i></button> ';
                    } else {
                        html = '';
                    }
                    $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
                }
            }
        ];

        var buttons = [];

        var multibutton = [];

        multibutton.push({
            "text": "月报表", "action": function () {
                //
            }
        });

        multibutton.push({
            "text": "结束报表", "action": function () {
                //
            }
        });

        @if($authority->checkAuthority(124))
        buttons.push({"text": '<i class="fa fa-clock-o fa-fw"></i>', "action": makeClock, "titleAttr": "补签"});
        @endif
        @if($authority->checkAuthority(123))
        buttons.push('export:/hr/attendance/export');
        @endif

//        if (multibutton.length > 0) {
//            buttons.push({
//                "text": "<i class='fa fa-table fa-fw'></i>", "titleAttr": "报表", "extend": "collection",
//                "buttons": multibutton
//            });
//        }

    </script>
@endsection