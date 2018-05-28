@extends('layouts.admin')
@inject('authority','Authority')
@section('content')
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />
<div class="row">
    <div class="col-lg-12">
        <!--      class="addition"  -->
        <section class="col-md-9">
            <section class="panel">
                 <header class="panel-heading">
                   大爱单添加列表
                 </header>
				 <!-- 筛选 start -->
            @include('hr/violation_filter')
            <!-- 筛选 end -->
                <div class="panel-body">
					<table class="table table-striped table-bordered dataTable no-footer" id="example">
					</table>
					<?php if ($authority->checkAuthority(104)) { ?>
						<div class=" text-right ">
							<button  class="btn btn-default pull-right"  onclick="violationlsf(this)" >提交</button>
						</div>
					<?php } ?> 
				</div>
            </section>
			<!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-sm">Small modal</button>-->

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-body "  style="Z-index:999;background:#fff;border:1px solid #ddd;">
							<a href="{{source('template/大爱市场信息.xlsx')}}">大爱市场信息模板</a>
							<a href="{{source('template/大爱后台信息.xlsx')}}"style="margin-left:20px;">大爱后台信息模板</a>
							<div class="row">
                        <div class="col-lg-2"></div>
                        <label class="control-label col-lg-8" style="margin-top:20px;">
                            <a class="btn btn-block btn-default">批量导入</a>
                            <input class="hidden" type="file" name="staff" id="import_staff">
                        </label>
                    </div>
					</div>
  </div>
</div>
        </section> 
		
		<?php if ($authority->checkAuthority(103)) { ?>
        <section class="col-md-3">
            <div class="panel">
                <header class="panel-heading">添加违纪</header>		                
                <!-- 列表 start -->
                <div class="panel-body">
                    <form class="form-horizontal" action="" id="jsformlsf"method="post">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="form-group">
                            <label class="control-label col-md-3">姓名：</label>
                            <div class="col-md-4 input-group">
                                <input class="form-control" name="staff_name" type="text"title="姓名" style="background-color:#fff;" readonly/>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" onclick="searchStaff(this)"><i class="fa fa-search" ></i></button>
                                </span>
                            </div>
                            <input class="form-control" name="staff_sn" value=""type="hidden"/>
                            
                        </div>
						<div class="form-group" id="choose">
                            <label  class="col-sm-3 control-label" >选择：</label>
                            <div class="col-sm-7 l-radio-html">
                               <label><input name="fruit" type="radio" value="市场" />市场 </label> 
							   <label><input name="fruit" type="radio" value="后台" />后台 </label> 
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">类型：</label>
                                <div class="col-sm-7">
                                    <select class="form-control pull-left" name="type_id" style="width:78%" id="violation_type">
                                        @foreach($pods as $pod )
                                        <option value="{{$pod->id}}">{{$pod->name}}</option>
                                        @endforeach
                                    </select></div>
                            </div>
                            <!--data-toggle="modal" data-target=".bs-example-modal-lg"--> 
                            <div class="form-group l-data-attr">
                                <label class="col-sm-3 control-label">原因：</label>
                                <div class="col-sm-7">
                                    <select class="form-control pull-left" name="reason_id" style="width:78%" id="violation_reason">
                                        @foreach($pods as $pod)
                                        @foreach($pod->reason as $reason)
                                        <option value="{{$reason->id}}" type_id="{{$reason->type_id}}">{{$reason->content}}</option>
                                        @endforeach
                                        @endforeach
                                        <option value="0" type_id="0">其他</option>
                                    </select> </div>
                            </div>
                        </div>
                        <div class="form-group" id="conceal">
                            <label  class="col-sm-3 control-label">其他：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="reason" placeholder="请输入其他原因。。。。。。" style="width:78%" disabled id="violation_other_reason">
                            </div>
                        </div>
                        <div class="form-group">
                            <label  class="col-sm-3 control-label">时间：</label>
                            <div class="col-sm-7 ">
                                <input type="text" class="form-control " id="dateinfo"name="committed_at" value="" placeholder="请输入违纪时间。。。。。。" style="width:78%">
                            </div>
                        </div>
                       <div class="form-group"id="reveal">
                            <label  class="col-sm-3 control-label">金额：</label>
                            <div class="col-sm-8">
                                <input type='text' class="form-control" name="price" disabled="disabled"value=""style="width:78%"   onkeyup="(this.v = function () { this.value = this.value.replace(/[^0-9-]+/, ''); }).call(this)" />
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="control-label col-md-3">开单人:</label>
                            <div class="col-md-4 input-group">
                                <input class="form-control" name="supervisor_name" type="text"  value="" title="姓名" style="background-color:#fff;" readonly/>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" onclick="searchStaf(this)"><i class="fa fa-search" ></i></button>
                                </span>
                            </div>
                            <input class="form-control" name="supervisor_sn" value=""type="hidden"/>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button type="submit" class="btn btn-default">保存</button>
                            </div>
                        </div>
                    </form>   
                </div>
                <!-- 列表 end -->
            </div>
        </section>
		<?php } ?>
        <!--编辑大爱单    start-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form class="form-horizontal" action="" id="lsform"method="post">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="form-group">
                                <label class="control-label col-md-2">姓名：</label>
                                <div class="col-md-4 input-group">
                                    <input class="form-control" name="staff_name" type="text" id="jsform1" value="" title="姓名" style="background-color:#fff;" readonly/>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="searchStaff(this)"><i class="fa fa-search" ></i></button>
                                    </span>
                                </div>
                                <input class="form-control" name="staff_sn" value=""type="hidden" id="jsform7"/>
                            
                            </div>
                            <div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">类型：</label>
                                    <div class="col-sm-10" id="jsform2">
                                        <select class="form-control pull-left" name="type_id" style="width:85%" id="violationtype">
                                            @foreach($pods as $pod )
                                            <option  value="{{$pod->id}}">{{$pod->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--data-toggle="modal" data-target=".bs-example-modal-lg"--> 
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">原因：</label>
                                    <div class="col-sm-10" id="jsform3">
                                        <select class="form-control pull-left" name="reason" style="width:85%" id="violationreason">
                                            @foreach($pods as $pod)
                                            @foreach($pod->reason as $reason)
                                            <option value="{{$reason->content}}"  type_id="{{$reason->type_id}}">{{$reason->content}}</option>
                                            @endforeach
                                            @endforeach
                                            <option value="0" type_id="0">其他</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">其他：</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="reason" placeholder="请输入其他原因。。。。。。" style="width:85%" disabled id="violationotherreason">
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">时间：</label>
                                <div class="col-sm-9 " >
                                    <input type="text" class="form-control " name="committed_at" value=""id="dateinf" id="jsform4"placeholder="请输入违纪时间。。。。。。" style="width:85%">
                                </div>
                            </div>
                           <div class="form-group">
                                <label  class="col-sm-2 control-label">金额：</label>
                                <div class="col-sm-9">
                                    <input type='text' class="form-control" name="price" value="" id="jsform5" style="width:85%" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2">开单人:</label>
                                <div class="col-md-4 input-group">
                                    <input class="form-control" name="supervisor_name" type="text"  value="" id="jsform6"title="姓名" style="background-color:#fff;" readonly/>
                                    <span class="input-group-btn">
                                        <button type="button" onclick="searchStaff(this)" class="btn btn-default"><i class="fa fa-search" ></i></button>
                                    </span>
                                </div>
                                <input class="form-control" name="supervisor_sn" value=""type="hidden"id="jsform11"/>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-default">保存</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--编辑大爱单    end-->

    </div>
</div>

@endsection

@section('js')
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<script type="text/javascript" src="{{asset('js/HR/violation.js')}}"></script>
<!-- datetimepicker -->
<script type="text/javascript" src="{{asset('plug_in/jedate/jedate.js')}}"></script>
<script type="text/javascript">
$(function(){
  jeDate({
        dateCell:"#dateinfo",
        format:"YYYY-MM-DD",
        isinitVal:false,
        isTime:false, //isClear:false,
        minDate:"2014-09-19 00:00:00",
        okfun:function(val){alert(val)}
    });
	jeDate({
        dateCell:"#dateinf",
        format:"YYYY-MM-DD",
        isinitVal:false,
        isTime:false, //isClear:false,
        okfun:function(val){alert(val)}
    });
})
var staffColumns="";
var columns = [
       {"data": "id", "title": "id"},
        {"data": "staff_name", "title": "姓名"},
        {"data": "staff_sn", "title": "编号"},
        {"data": "brand", "title": "品牌"},
        {"data": "department", "title": "部门"},
        {"data": "position", "title": "职位"},
        {"data": "type.name", "title": "类型"},
        {"data": "reason", "title": "原因"},
        {"data": "committed_at", "title": "违纪时间"},
		{"data": "times", "title": "次数"},
        {"data": "price", "title": "金额"},
        {"data": "supervisor_name", "title": "开单人"},
        {"data": "id", "title": "操作", "sortable": false,
            "render": function (datas, type, row, meta) {
                return '<a href="#" onclick="addDataTolsf(' + datas + ')" data-toggle="modal" vid="' + datas + '" title="编辑" data-target="#myModal"class="edit btn btn-sm btn-primary" ><i class="fa fa-edit fa-fw"></i></a><a href="#" title="删除"class="btn btn-sm btn-danger" vid="' + datas + '" onclick="deleteviolation(' + datas + ')" ><i class="fa fa-trash-o fa-fw"></i></a>';
            }
        }
    ];
var buttons = [
    {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "titleAttr": "可见字段", "className": "btn-primary"},
    {"text": "<i class='fa fa-refresh fa-fw'></i>", "titleAttr": "刷新", "className": "btn-primary", "action": function () {
            staffColumns.fnDraw();
        }
    },
    {"text": "<i class='fa fa-filter fa-fw' id='filter_icon'></i>", "titleAttr": "筛选", "className": "btn-primary", "action": function () {
            $("#filter").slideToggle();
        }
    },
	{"text": "<i class='fa fa-users fa-fw 'id='bulkimp'data-toggle='modal' data-target='.bs-example-modal-sm' ></i>", "titleAttr": "批量操作"},
];
	/*获取保存大爱列表*/
 $(function(){
staffColumns = $("#example").dataTable({
	"columns": columns,
        "ajax": "/hr/violation/enter/list",
        "scrollX": 1080,
		"text-align":"center",
        "order": [[1, "asc"]],
        "dom": "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": buttons,
		 //"autoWidth":true
});
})              
</script>

@endsection
