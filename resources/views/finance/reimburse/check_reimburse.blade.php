@inject('authority','App\Services\AuthorityService')

@extends('layouts.admin')

@section('css')
    <!--dynamic table-->
    <link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}" />
    <!--daterangepicker-->
    <link rel="stylesheet" href="{{source('plug_in/daterangepicker/daterangepicker.css')}}" />
    <!--viewerjs-->
    <link rel="stylesheet" href="{{source('plug_in/viewerjs/css/viewer.css')}}" />
    <!-- checkbox -->
    <link rel="stylesheet" href="{{source('css/checkbox.css')}}" />
    <!-- zTree css -->
    <link rel="stylesheet" href="{{source('plug_in/ztree/css/metroStyle.css')}}" />
@endsection

@section('content')
    <style>
        .print:focus, .print:visited {
            color: #fff;
            background-color: #666;
            border-color: #666;
        }

        td.details > table {
            max-width: 980px;
        }

        td.details p {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            width: 800px;
            font-weight: 700;
            padding-left: 50px;
        }

        .show_expense.fa-plus-circle {
            color: #65CEA7;
        }

        #bill_pic {
            padding: 0;
        }

        #bill_pic li {
            list-style: none;
            width: 50%;
            height: 200px;
            margin: 0;
            padding: 10px;
            overflow: hidden;
        }

        #bill_pic li img {
            width: 100%;
        }
    </style>
    <div class="row">
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-12">
                    <section class="panel">
                        <header class="panel-heading custom-tab dark-tab">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#history" data-toggle="tab">已审核报销单</a>
                                </li>
                            </ul>
                        </header>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="history">
                                    {{--搜索列表--}}
                                    @include('finance.reimburse.check_reimburse_search')
                                    <table class="display table table-striped table-bordered reim_table"
                                           id="history-table"></table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <section class="panel">
                <header class="panel-heading">
                    发票
                </header>
                <div class="panel-body">
                    <div id="gallery" class="media-gal">

                    </div>
                </div>
            </section>
        </div>
    </div>
    {{--驳回界面start--}}
    <div role="dialog" tabindex="-1" id="myModals" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header danger">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h4 class="modal-title">驳回</h4>
                </div>
                <div class="modal-body" style="font-size:24px;">
                    <form class="">
                        <textarea onkeyup="check_remarks(this)" id="remarks" class="form-control" rows="4"
                                  placeholder="请输入驳回原因" style="resize:none;"></textarea>
                    </form>
                </div>
                <div class="modal-footer" style="margin-top:0;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-success" disabled id="confirm_reject"
                            onclick="confirm_reject(this)">确认
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{--驳回界面end--}}
@endsection

@section('js')
    <!--dynamic table-->
    <script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
    <!--daterangepicker-->
    <script type="text/javascript" src="{{source('plug_in/daterangepicker/moment.js')}}"></script>
    <script type="text/javascript" src="{{source('plug_in/daterangepicker/daterangepicker.js')}}"></script>
    <!--viewerjs-->
    <script src="{{source('plug_in/viewerjs/js/viewer.js')}}"></script>
    <!-- zTree js -->
    <script type="text/javascript" src="{{source('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
    <!-- 报销相关功能 -->
    <script src="{{source('js/finance/reimburse/check_reimburse.js')}}"></script>
    <script>
      /**
       * 详情图片调用
       *获取图片的绝对路径
       * @returns {string}
       */
      function getExpensesTypeImgPath() {
        return '{{config("api.url.reimburse.base")}}';
      }

      var auditedColumns = [
              @if ($authority->checkAuthority(134))
        {
          data: "id", name: "id", class: "text-center multi-select", sortable: false,
          createdCell: function (nTd, sData, oData) {
            var select = $('<label class="frame check frame-sm" unselectable="on" onselectstart="return false;" />');
            var selectInput = $('<input type="checkbox" name="id" value="' + sData + '"/>');
            if (oData.status_id !== 4) {
              selectInput.prop('disabled', true);
            }
            selectInput.change(function () {
              var checked = this.checked;
              if (checked) {
                var allSelect = $('td.multi-select [name=id]:checkbox:not(:disabled)');
                checked = allSelect.length > 0 && allSelect.length === allSelect.filter(':checked').length;
              }
              $('.dataTables_scrollHead th.multi-select :checkbox').prop('checked', checked);
            });
            select.append(selectInput);
            select.append('<span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;');
            $(nTd).html(select);
          }
        },
              @endif
        {
          title: "详情", data: "id", name: "id", class: "text-center", sortable: false,
          render: function (data, type, row, meta) {
            return '<i class="fa fa-plus-circle show_expense" style="font-size: 20px; cursor:pointer;" onclick="show_expenses(' + data + ',this)"></i>';
          }
        },
        { title: "订单编号", data: "reim_sn", name: "reim_sn", sortable: true },
        { title: "申请人", data: "realname", name: "realname", sortable: true },
        {
          title: "部门", data: "department_name", name: "department_name", sortable: true,
          createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html = (sData.length > 6) ? sData.substring(0, 6) + '..' : sData;
            $(nTd).html(html).attr('title', sData);
          }
        },
        { title: "审批人", data: "approver_name", name: "approver_name", sortable: true },
        { title: "资金归属", data: "reim_department.name", name: "reim_department.name", sortable: true },
        {
          title: "申请时间", data: "send_time", name: "send_time", sortable: true, searchable: false, visible: false,
          createdCell: function (nTd, sData, oData, iRow, iCol) {
            if (sData) $(nTd).html(sData.substring(0, 10)).attr("title", sData);
          }
        },
        {
          title: "审批时间", data: "approve_time", name: "approve_time", sortable: true, searchable: false, visible: false,
          createdCell: function (nTd, sData, oData, iRow, iCol) {
            if (sData) $(nTd).html(sData.substring(0, 10)).attr("title", sData);
          }
        },
        {
          title: "审核时间", data: "audit_time", name: "audit_time", sortable: true, searchable: false,
          createdCell: function (nTd, sData, oData, iRow, iCol) {
            if (sData) $(nTd).html(sData.substring(0, 10)).attr("title", sData);
          }
        },
        {
          title: "转账时间", data: "paid_at", name: "paid_at", sortable: true, searchable: false,
          createdCell: function (nTd, sData, oData, iRow, iCol) {
            if (sData) $(nTd).html(sData.substring(0, 10)).attr("title", sData);
          }
        },
        {
          title: "审核人",
          data: "accountant_name",
          name: "accountant_name",
          class: "text-center",
          sortable: true
        },
        {
          title: "总金额",
          data: "audited_cost",
          name: "audited_cost",
          class: "text-center",
          width: "100px",
          sortable: true,
          render: function (data, type, row, meta) {
            return '￥' + data;
          }
        },
        {
          title: "操作",
          data: 'id',
          name: 'id',
          class: 'text-center',
          sortable: false,
          createdCell: function (nTd, sData, oData, iRow, iCol) {
            var html = '';
            if (oData.print_count === 0) {
              html += '<a target="_blank" href="/finance/reimburse/print/' +
                sData + '" class="btn btn-sm btn-default print" title="打印">' +
                '<i class="fa fa-fw fa-print"></i></a>';
            }
            if (oData.status_id == 4) {
                @if ($authority->checkAuthority(119))
                  html += ' <button class = "btn btn-sm btn-danger" title="撤回" onclick="restore(' + sData + ')">' +
                  '<i class="fa fa-fw fa-reply"></i></button>';
                @endif
                        @if ($authority->checkAuthority(134))
                  html += ' <button class = "btn btn-sm btn-danger reject" ' +
                  'href="#myModals" data-toggle="modal" onclick="auditReject(' + sData + ')" title = "驳回">' +
                  '<i class = "fa fa-fw fa-times"></i></button>';
              html += ' <button class = "btn btn-sm btn-success" title="转账" onclick="pay(' + sData + ')">' +
                '<i class="fa fa-fw fa-yen"></i></button>';
                @endif
            }
            $(nTd).html(html).css("padding", "6px");
          }
        }
      ];

      var buttons = ['export:/finance/reimburse/excel?type=all'];
      @if ($authority->checkAuthority(134))
      buttons.push({ text: '<i class="fa fa-yen fa-fw"></i>', action: payByMultiple, titleAttr: '批量转账' });
        @endif
    </script>
@endsection