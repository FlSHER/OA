@inject('authority','App\Services\AuthorityService') 

@extends('layouts.admin') 

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}" />
<!--daterangepicker--> 
<link rel="stylesheet" href="{{source('plug_in/daterangepicker/daterangepicker.css')}}"/> 
<!--viewerjs--> 
<link rel="stylesheet" href="{{source('plug_in/viewerjs/css/viewer.css')}}"/> 
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
    <div class="col-sm-8"> 
        <div class="row"> 
            <div class="col-sm-12"> 
                <section class="panel"> 
                    <header class="panel-heading custom-tab dark-tab"> 
                        <ul class="nav nav-tabs"> 
                            <li class="active"> 
                                <a href="#pending" data-toggle="tab">待审核报销单</a> 
                            </li> 
                            <li class=""> 
                                <a href="#history" data-toggle="tab">已审核报销单</a> 
                            </li> 
                            <li class=""> 
                                <a href="#reject" data-toggle="tab">已驳回报销单</a> 
                            </li> 
                        </ul> 
                    </header>
                    <div class="panel-body">
                        <div class="tab-content"> 
                            <div class="tab-pane active" id="pending"> 
                                <table class="display table table-striped table-bordered reim_table" 
                                       id="pending-table"></table> 
                            </div> 
                            <div class="tab-pane" id="history">
                                {{--搜索列表--}}
                                @include('finance.reimburse.search_approved')
                                <table class="display table table-striped table-bordered reim_table" 
                                       id="history-table"></table> 
                            </div> 
                            <div class="tab-pane" id="reject">
                                @include('finance.reimburse.search_reject')
                                <table class="display table table-striped table-bordered reim_table" 
                                       id="reject-table"></table> 
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
                    <textarea onkeyup="check_remarks(this)" id="remarks" class="form-control" rows="4" placeholder="请输入驳回原因" style="resize:none;"></textarea> 
                </form>
            </div> 
            <div class="modal-footer" style="margin-top:0;"> 
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button> 
                <button type="button" class="btn btn-success" disabled id="confirm_reject" onclick="confirm_reject(this)">确认</button> 
            </div> 
        </div> 
    </div> 
</div> 
{{--驳回界面end--}} 
@endsection 

@section('js')
<!--data table-->
<script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
<!--daterangepicker--> 
<script type="text/javascript" src="{{source('plug_in/daterangepicker/moment.js')}}"></script> 
<script type="text/javascript" src="{{source('plug_in/daterangepicker/daterangepicker.js')}}"></script> 
<!--viewerjs--> 
<script src="{{source('plug_in/viewerjs/js/viewer.js')}}"></script> 
<script>
                    /** 
                     * 详情图片调用 
                     *获取图片的绝对路径 
                     * @returns {string} 
                     */
                    function getExpensesTypeImgPath() {
                        return '{{config("api.url.reimburse.base")}}';
                    }
</script> 
<!-- 报销相关功能 --> 
<script src="{{source('js/finance/reimburse/reimburse.js')}}"></script>
@endsection