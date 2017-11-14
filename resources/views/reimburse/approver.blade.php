@extends('layouts.admin')
@inject('HRM',HRM)
@section('css')
@parent
<link rel="stylesheet" href="{{source('plug_in/datatables/datatables.min.css')}}" />
@stop
@section('content')
<div class="col-lg-8">
    <section class="panel">
        <!--编辑视图 start-->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h4 class="modal-title" id="apporverTitle">添加审批</h4>
                    </div>
                    <div class="modal-body">
                        <form action="/app/reimburse/saveApprover" method="post" id="approverForm" class="form-horizontal" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="id" value=""/>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">部门<span style="color: red;">*</span></label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="department_id" title="部门">
                                        {!!$HRM->getDepartmentOptionsById()!!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">资金归属<span style="color: red;">*</span></label>
                                <div class="col-lg-10">
                                    <select name="reim_department_id" class="form-control">
                                        {!! get_options('App\Models\Reimburse\ReimDepartment','name','id')!!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" isFormList>
                                <label  class="col-sm-2 control-label">一级审批</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" readonly name="approver1[][realname]" oaSearchShow oaSearchColumn="realname" placeholder="请点击选择审批人" value=""/>
                                </div>
                                <input type="hidden" name="approver1[][staff_sn]"  value="" oaSearchColumn="staff_sn" />
                                <input type="hidden" name="approver1[][priority]"  value="1" locked="true" />
                                <input type="hidden" name="approver1[][id]"  value="" />
                            </div>
                            <div class="form-group" isFormList>
                                <label  class="col-sm-2 control-label">二级审批</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" readonly name="approver2[][realname]" oaSearchShow oaSearchColumn="realname" placeholder="请点击选择审批人" value=""/>
                                </div>
                                <input type="hidden" name="approver2[][staff_sn]"  value="" oaSearchColumn="staff_sn" />
                                <input type="hidden" name="approver2[][priority]"  value="2" locked="true" />
                                <input type="hidden" name="approver2[][id]"  value="" />
                            </div>
                            <div class="form-group" isFormList>
                                <label  class="col-sm-2 control-label">三级审批</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" readonly name="approver3[][realname]" oaSearchShow oaSearchColumn="realname" placeholder="请点击选择审批人" value=""/>
                                </div>
                                <input type="hidden" name="approver3[][staff_sn]"  value="" oaSearchColumn="staff_sn" />
                                <input type="hidden" name="approver3[][priority]"  value="3" locked="true" />
                                <input type="hidden" name="approver3[][id]"  value="" />
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
                <table  class="display table table-bordered table-striped" id="approverTable">

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
<!--审批人-->
<script type="text/javascript" src="{{source('js/reimburse/approver.js')}}"></script>
@stop