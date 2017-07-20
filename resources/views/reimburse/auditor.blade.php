@extends('layouts.admin')
@inject('HRM',HRM)
@section('css')
@parent
<!-- data table -->
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
                        <h4 class="modal-title" id="auditorTitle">添加审核</h4>
                    </div>
                    <div class="modal-body">
                        <form action="/app/reimburse/saveAuditor" method="post" id="auditorForm" class="form-horizontal" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="id" value=""/>
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">资金归属<span style="color: red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="name" placeholder="请输入资金归属名" maxlength="20">
                                </div>
                            </div>
                            <div class="form-group" isFormList>
                                <label  class="col-sm-2 control-label">审核人<span style="color: red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" readonly name="auditor[][auditor_realname]" oaSearchShow oaSearchColumn="realname" placeholder="请点击选择审核人" value=""/>
                                </div>
                                <input type="hidden" name="auditor[][auditor_staff_sn]"  value="" oaSearchColumn="staff_sn" />
                                <input type="hidden" name="auditor[][id]"  value="" />
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
                <table  class="display table table-bordered table-striped" id="auditorTable">

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
<!--审核人-->
<script type="text/javascript" src="{{source('js/reimburse/auditor.js')}}"></script>

<script type="text/javascript" src="{{source('js/scripts.js')}}"></script>
@stop
