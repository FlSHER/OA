@extends('layouts.admin')
@inject('HRM',HRM)
@section('css')
@parent
<link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}" />
@stop
@section('content')
<div class="col-lg-7">
    <section class="panel">
        <!--编辑视图 start-->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h4 class="modal-title" id="statisticTitle">添加统计人员配置</h4>
                    </div>
                    <div class="modal-body">
                        <form action="/app/work_mission/statistic/save" method="post" id="statisticForm" class="form-horizontal" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="id" value=""/>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">选择员工<span style="color: red;">*</span></label>
                                <div class="col-lg-8" oaSearch="staff">
                                    <input type="text" class="form-control" placeholder="点击选择员工" readonly name="realname" oaSearchShow oaSearchColumn="realname" value=""/>
                                    <input type="hidden" name="staff_sn" value="" oaSearchColumn="staff_sn"/>
                                </div>
                            </div>

                            <div class="form-group" isFormList>
                                <label  class="col-sm-2 control-label">统计部门</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="statistic_department[][department_id]" title="部门">
                                        {!!$HRM->getDepartmentOptionsById()!!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-primary">保存</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--编辑视图end-->
        <!--列表数据start-->
        <div class="panel-body">
            <div class="adv-table">
                <table  class="display table table-bordered table-striped" id="statisticTable">

                </table>
            </div>
        </div>
        <!--列表数据end-->
    </section>
</div>
@endsection
@section('js')
@parent
<!--data table-->
<script type="text/javascript" src="{{source('plug_in/datatables/datatables.min.js')}}"></script>
<script type="text/javascript" src="{{source('js/scripts.js')}}"></script>
<!---->
<script type="text/javascript" src="{{source('js/app/work_mission/statistic.js')}}"></script>
@stop