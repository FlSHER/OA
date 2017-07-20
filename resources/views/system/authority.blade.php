@extends('layouts.admin')

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <section class="panel">
            <header class="panel-heading custom-tab dark-tab">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#list" data-toggle="tab" id="tableTab">权限列表</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="list">
                        <table class="table table-striped table-bordered dataTable no-footer" id="datatable_list"></table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-md-6">
        <section class="panel">
            <header class="panel-heading custom-tab dark-tab">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#treeview" data-toggle="tab" id="tableTab">树形图</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="treeview">
                        <div class="btn-group" id="nestable_list_menu">
                            <button type="button" class="btn btn-sm btn-default" onclick="expandAll()">全部展开</button>
                            <button type="button" class="btn btn-sm btn-default" onclick="collapseAll()">全部收起</button>
                        </div>
                        <div class="ztree" id="authority_tree_view" style="max-height:746px;overflow:auto;">

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">添加权限</h4>
            </div>
            <div class="modal-body">
                <form class="cmxform form-horizontal adminex-form" id="commentForm" method="get" action="">
                    <div class="form-group ">
                        <label for="C_auth_name" class="control-label col-lg-2">权限名称</label>
                        <div class="col-lg-10">
                            <input class=" form-control" id="C_auth_name" name="name" maxlength="20" minlength="2" type="text" required />
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="C_access_url" class="control-label col-lg-2">URL</label>
                        <div class="col-lg-10">
                            <input class=" form-control" id="C_access_url" name="name" maxlength="20" type="text" required />
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="C_menu_name" class="control-label col-lg-2">菜单名称</label>
                        <div class="col-lg-10">
                            <input class=" form-control" id="C_menu_name" name="name" minlength="2" maxlength="20" type="text" required />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-success">确认</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!--data table-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<!-- zTree js -->
<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!--script for this view-->
<script type="text/javascript" src="{{asset('js/system/authority.js')}}"></script>
<script>
                                var columns = [
                                    {"data": "id", "title": "编号"},
                                    {"data": "auth_name", "title": "权限名称"},
                                    {"data": "full_url_tmp", "title": "关联URI", "searchable": false, "sortable": false},
                                    {"data": "is_menu", "title": "是否为菜单",
                                        "render": function (data) {
                                            return data ? "是" : "否";
                                        }
                                    },
                                    {"data": "menu_name", "title": "菜单名称"},
                                    {"data": "menu_logo", "title": "菜单图标",
                                        "render": function (data) {
                                            return '<i class="fa ' + data + '"></i>';
                                        }
                                    }
                                ];
</script>
@endsection