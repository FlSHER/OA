@extends('layouts.admin')

@inject('authority','Authority')
@inject('HRM','HRM')

@section('css')
    <!-- data table  -->
    <link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}"/>
    <!-- checkbox -->
    <link rel="stylesheet" href="{{source('css/checkbox.css')}}"/>
@endsection



@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    调动列表
                </header>
                <!-- 筛选 start -->
            @include('hr/attendance/transfer_filter')
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

    <!-- Import Start -->
    <div id="importModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">批量导入</h4>
            </div>
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group text-center">
                        <a href="{{source('template/店铺人员调动.xlsx')}}">点此下载导入模板</a>
                    </div>
                    <div class="row">
                        <div class="col-lg-2"></div>
                        <label class="control-label col-lg-8">
                            <a class="btn btn-block btn-default">批量导入</a>
                            <input class="hidden" type="file" name="import" id="import_input">
                        </label>
                    </div>
                </div>
                <div class="modal-body" id="import_result">

                </div>
            </div>
        </div>
    </div>
    <!-- Import End -->

    <!-- AddByOne -->
    <div id="addByOne" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">新建调动</h4>
            </div>
            <div class="modal-content">
                <form id="addForm" name="addForm" class="form-horizontal" method="post"
                      action="{{route('hr.transfer.submit')}}">
                    @inject('HRM','HRM')
                    @include('hr/attendance/transfer_form')
                </form>
            </div>
        </div>
    </div>

    <!-- EditByOne -->
    <div id="editByOne" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">编辑调动</h4>
            </div>
            <div class="modal-content">
                <form id="editForm" name="editForm" class="form-horizontal" method="post"
                      action="{{route('hr.transfer.submit')}}">
                    @include('hr/attendance/transfer_form')
                    <input type="hidden" name="id">
                </form>
            </div>
        </div>
    </div>

@endsection


@section('js')
    <script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
    <!--script for this view-->
    <script type="text/javascript" src="{{source('js/HR/transfer.js')}}"></script>

    <script>

        var columns = [
            {data: "id", title: "ID", searchable: false},
            {data: "staff_sn", title: "员工编号"},
            {data: "staff_name", title: "员工姓名"},
            {data: "staff_gender", title: "性别", visible: false, searchable: false},
            {data: "staff_department_name", title: "部门名称", defaultContent: "无"},
            {data: "current_shop.name", title: "所属店铺", visible: false, defaultContent: "无"},
            {data: "leaving_shop_sn", title: "调离店铺代码", visible: false},
            {data: "leaving_shop_name", title: "调离店铺名称", defaultContent: "无"},
            {
                data: "{leaving_shop.province.name}.'-'.{leaving_shop.city.name}.'-'.{leaving_shop.county.name}.' '.{leaving_shop.address}",
                name: "leaving_shop.address",
                title: "调离店铺地址",
                visible: false
            },
            {data: "arriving_shop_sn", title: "到达店铺代码", visible: false},
            {data: "arriving_shop_name", title: "到达店铺名称", defaultContent: "无"},
            {
                data: "{arriving_shop.province.name}.'-'.{arriving_shop.city.name}.'-'.{arriving_shop.county.name}.' '.{arriving_shop.address}",
                name: "arriving_shop.address",
                title: "到达店铺地址",
                visible: false
            },
            {data: "arriving_shop_duty.name", title: "到店职务", visible: false, defaultContent: "待定", searchable: false},
            {data: "leaving_date", title: "出发日期", searchable: false},
            {data: "left_at", title: "出发时间", searchable: false},
            {data: "arrived_at", title: "到达时间", searchable: false},
            {data: "created_at", title: "创建时间", searchable: false},
            {data: "maker_name", title: "建单人", visible: false},
            {data: "implode(',',{tag.*.name}).' '.{remark}", name: "remark", title: "备注"},
            {
                data: "id",
                title: "操作",
                "sortable": false,
                "width": "50px",
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    var html = '';
                    <?php if ($authority->checkAuthority(81)): ?>
                    if (oData.maker_sn == "{{app('CurrentUser')->staff_sn}}" || "{{app('CurrentUser')->staff_sn}}" == "999999") {
                        html += '<button class="btn btn-sm btn-default" title="编辑" onclick=\'edit(' + sData + ')\'><i class="fa fa-edit fa-fw"></i></button>';
                    }
                    <?php endif; ?>

                    //                html += '&nbsp;<button class="btn btn-sm btn-danger" title="取消行程" onclick="del(' + sData + ')"><i class="fa fa-trash-o fa-fw"></i></button>';

                    $(nTd).html(html).css({"padding": "5px", "text-align": "center"});
                }
            }
        ];
        var buttons = [];
        <?php if ($authority->checkAuthority(80)): ?>
        buttons.push(
            {"text": "<i class='fa fa-plus fa-fw'></i>", "action": add, "titleAttr": "添加"},
            {
                "text": "<i class='fa fa-upload fa-fw'></i>", "action": function () {
                $('#importModal').modal('show');
            }, "titleAttr": "导入"
            }
        );
        <?php endif; ?>
        buttons.push('export:/hr/transfer/export');
    </script>
@endsection