@inject('authority','App\Services\AuthorityService')

@extends('layouts.admin')

@section('css')
    <!--dynamic table-->
    <link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}"/>
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

        .dt-buttons {
            padding-top: 12px;
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
@endsection

@section('js')
    <!--dynamic table-->
    <script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
    <!--daterangepicker-->
    <script type="text/javascript" src="{{source('plug_in/daterangepicker/moment.js')}}"></script>
    <script type="text/javascript" src="{{source('plug_in/daterangepicker/daterangepicker.js')}}"></script>
    <!--viewerjs-->
    <script src="{{source('plug_in/viewerjs/js/viewer.js')}}"></script>
    <script>
        //查看报销单的撤回权限
        var reply_button = false;
        <?php if ($authority->checkAuthority(119)) { ?>
            reply_button = true;
        <?php } ?>
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
    <script src="{{source('js/finance/reimburse/check_reimburse.js')}}"></script>
@endsection