@inject('authority','App\Services\AuthorityService')

@extends('layouts.admin')

@section('css')
<!--dynamic table-->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<!--daterangepicker-->
<link rel="stylesheet" href="{{asset('plug_in/daterangepicker/daterangepicker.css')}}" />
<!--viewerjs-->
<link rel="stylesheet" href="{{asset('plug_in/viewerjs/css/viewer.css')}}" />
@endsection

@section('content')
<style>
    .print:focus,.print:visited{
        color:#fff;
        background-color:#666;
        border-color:#666;
    }
    td.details>table{
        max-width:980px;
    }
    td.details p{
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        width:800px;
        font-weight:700;
        padding-left:50px;
    }
    .dt-buttons{
        padding-top:12px;
    }
    .show_expense.fa-plus-circle{
        color:#65CEA7;
    }
    #bill_pic{
        padding:0;
    }
    #bill_pic li{
        list-style: none;
        width:50%;
        height:200px;
        margin:0;
        padding:10px;
        overflow:hidden;
    }
    #bill_pic li img{
        width:100%;
    }
</style>
<div class="row">
    <div class="col-sm-8">
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
                                <table class="display table table-striped table-bordered reim_table" id="history-table"></table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
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
@endsection

@section('js')
<!--dynamic table-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<!--<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.fixedColumns.js')}}"></script>-->
<!--daterangepicker-->
<script type="text/javascript" src="{{asset('plug_in/daterangepicker/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/daterangepicker/daterangepicker.js')}}"></script>
<!--viewerjs-->
<script src="{{asset('plug_in/viewerjs/js/viewer.js')}}"></script>
<script>
$(document).ready(function () {

    $(document).on("click", ".bills", function () {
        var bills = $(this).attr("bills");
        bills = bills.split(',');
        var description = $(this).attr("description");
        var cost = $(this).attr("cost");
        var html = '<div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<h3 class="panel-title">金额<span class="pull-right">￥' + cost + '</span></h3>' +
                '</div>' +
                '<div class="panel-footer">' + description + '</div>' +
                '</div>' +
                '<ul class="row" id="bill_pic">';
        for (var i in bills) {
            var img = "{{config('api.url.reimburse.base')}}" + bills[i];
            html += '<li class="col-lg-5" ><img src="' + img + '" alt="" /></li>';
        }
        html += '</ul>';
        if ($("#gallery").children().length === 0) {
            $("#gallery").parents(".panel").find(".tools .fa").click();
        }
        $("#gallery").html(html);
        var viewer = new Viewer($("#gallery")[0], {"title": false});
    });
});



function fnFormatDetails(table, nTr, reim_id) {
    var url = "{{asset(route('finance.reimburse.expense'))}}";
    $.ajax({
        type: "POST",
        url: url,
        async: false,
        data: {reim_id: reim_id},
        dataType: "json",
        success: function (msg) {
            sOut = '<p> 描述：' + msg.description + '</p>';
            sOut += '<p title="' + msg.remark + '" style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;max-width:800px;font-weight:700;"> 备注：' + msg.remark + '</p>';
            sOut += '<table class="col-lg-12" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"><tr style="background:#566986;">';
            sOut += '<th class="col-lg-1"></th>';
            sOut += '<th class="text-center col-lg-3">描述</th>' +
                    '<th class="text-center">消费时间</th>' +
                    '<th class="text-center">消费类型</th>' +
                    '<th class="text-center">&nbsp;金额</th>';
            sOut += '<th class="text-center">发票详情</th>' +
                    '</tr>';
            for (var i in msg.expenses) {
                var expense = msg.expenses[i];
                sOut += '<tr>';
                sOut += '<td class="text-center"><div class="single-row"><div class="radio "><input type="checkbox"';
                if (expense.is_audited) {
                    sOut += ' checked ';
                }
                sOut += 'disabled></div></div></td>';
                sOut += '<td title="' + expense.description + '">';
                if (expense.description.length >= 26) {
                    sOut += expense.description.substring(0, 24) + '...';
                } else {
                    sOut += expense.description;
                }
                sOut += '</td><td class="text-center">' + expense.date.substring(0, 10) + '</td>' +
                        '<td class="text-center" title="' + expense.type.name + '"><img width="30" src="{{config("api.url.reimburse.base")}}' + expense.type.pic_path + '"></td>';
                /* 总金额 Start */
                if (expense.audited_cost != expense.approved_cost) {
                    sOut += '<td class="text-right" style="color:red" title="原金额：￥' + expense.approved_cost + '">￥';
                    sOut += expense.audited_cost ? expense.audited_cost : 0;
                } else {
                    sOut += '<td class="text-right">￥';
                    sOut += expense.audited_cost ? expense.audited_cost : 0;
                }
                sOut += '</td>';
                sOut += '<td class="text-center">';
                /* 发票按钮 Start */
                if (expense.bills && expense.bills.pic_path.length > 0) {
                    var bills = expense.bills.pic_path;
                    sOut += '<a class="btn btn-success bills" bills="' + bills + '" description="' + expense.description + '" cost="';
                    sOut += expense.audited_cost ? expense.audited_cost : 0;
                    if (expense.audited_cost != expense.approved_cost) {
                        sOut += ' ( ￥' + expense.approved_cost + ' )';
                    }
                    sOut += '">' + bills.length + '</a>';
                } else {
                    sOut += '<a class="btn btn-danger disabled" >0</a>';
                }
                /* 发票按钮 End */
                sOut += '</td></tr>';
            }
            sOut += '</table>';
        }
    });
}
</script>
<!-- 报销相关功能 -->
<script src="{{asset('js/finance/check_reimburse.js')}}"></script>
@endsection