@extends('layouts.admin')
@inject('HRM',HRM)
@section('css')
@parent
<!--form_config_list-->
<!--<link href="{{asset('css/workflow/demo_page.css')}}" rel="stylesheet"/>
<link href="{{asset('css/workflow/demo_table.css')}}" rel="stylesheet"/>
<link rel="stylesheet" href="{{asset('css/workflow/DT_bootstrap.css')}}"/>-->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
@stop
@section('content')
@include('workflow.common.common')
<div class="col-lg-12">
    <section class="panel">
        <header class="panel-heading">
            工作流程设置<span style="color: #65cea7;"> / </span>表单设置
        </header>
        <a href="#myModal-1" data-toggle="modal" class="btn btn-success" type="button" id="createClassify">创建表单 </a><div hidden class="formConfigHidden"style="width:100%;float:right;color:red; text-align:center;margin-top:-20px;"></div>
        <!--创建表单start-->
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-1" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h4 class="modal-title" id="formConfigTitle">创建表单</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{asset(route('workflow.formConfigSubmit'))}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                            <input type="hidden" id="validateTijiao" value="0"/>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">表单名称<span style="color: red;">*</span></label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="form_name" maxlength="30" name="form_name" placeholder="请输入名称">
                                    <!--<p id="validateForm_name" style="color:red;"></p>-->
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">表单描述</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" maxlength="255" name="form_describe" placeholder="请输入描述">
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">表单分类<span style="color: red;">*</span></label>
                                <div class="col-lg-10">
                                    <select name="form_classify_id" class="form-control"id="classifyId_select" >

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">所属部门<span style="color: red;">*</span></label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="form_classify_department_id" title="所属部门" onmousedown="showTreeViewOptions(this)">
                                        {!!$HRM->getDepartmentOptionsById()!!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">排序</label>
                                <div class="col-lg-10">
                                    <input type="number" class="form-control" maxlength="5" name="sort" placeholder="请输入排序">
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-lg-2 col-sm-2 control-label">导入表单</label>
                                <div class="col-lg-10">
                                    <input type="file" name="excelForm" id="excelForm"/>
                                    <p>导入表单(请选择表单的HTML,txt文件)</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button type="button" class="btn btn-primary">保存</button>
                                </div>
                            </div>
                            <div class="deleteId" hidden>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--创建表单end-->

        <!--列表数据start-->
        <div class="panel-body">
            <div class="adv-table">
                <table  class="display table table-bordered table-striped" id="formConfigTable">

                </table>
                <form name="form_formDesign" method="post" hidden>
                    {{csrf_field()}}
                    <input type="hidden" name="id" value=""/>
                </form>
            </div>
        </div>
        <!--列表数据end-->
    </section>
</div>
@endsection
@section('js')
@parent
<!--<script type="text/javascript" language="javascript" src="{{asset('js/workflow/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/DT_bootstrap.js')}}"></script>
<script src="{{asset('js/workflow/dynamic_table_init.js')}}"></script>-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/form/formConfigList.js')}}"></script>
<!-- zTree js -->
<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
@stop