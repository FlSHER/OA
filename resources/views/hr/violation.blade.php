@extends('layouts.admin')

@inject('authority','Authority')
@inject('HRM','HRM')

@section('css')
<!-- data table -->
<link rel="stylesheet" href="{{source('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{source('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{source('plug_in/datatables/css/buttons.bootstrap.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{source('plug_in/ztree/css/metroStyle.css')}}" />
<!-- checkbox -->
<link rel="stylesheet" href="{{source('css/checkbox.css')}}" />
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                大爱单列表
            </header>
			<!-- 筛选 start -->
            @include('hr/violation_filter')
            <!-- 筛选 end -->
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="shop_list">
                </table>
            </div>
        </section>
    </div>
</div>

<!--     编辑大爱单    start-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form class="form-horizontal" action="" id="violationform"method="post">
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
                                <input class="form-control" name="brand" value=""type="hidden" id="jsform8"/>
                                <input class="form-control" name="department" value=""type="hidden" id="jsform9"/>
                                <input class="form-control" name="position" value=""type="hidden" id="jsform10"/>
                                <input class="form-control" name="id" value=""type="hidden" id="jsform12"/>
                            </div>
                            <div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">类型：</label>
                                    <div class="col-sm-10" id="jsform2">
                                        <select class="form-control pull-left" name="type" style="width:85%" id="violationtype">
                                            @foreach($pods as $pod )
                                            <option  value="{{$pod->id}}">{{$pod->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--data-toggle="modal" data-target=".bs-example-modal-lg"--> 
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">原因：</label>
                                    <div class="col-sm-10 ">
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
                                    <input type="text" class="form-control jsform3" name="reason" placeholder="请输入其他原因。。。。。。" style="width:85%" disabled id="violationotherreason">
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">时间：</label>
                                <div class="col-sm-9 date  fl" id="to">
                                    <input type="text" class="form-control date-check" name="committed_at" value="" id="jsform4"placeholder="请输入犯错时间。。。。。。" style="width:85%">
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
        
<!--        编辑大爱单    end-->
    
</div>


@endsection


@section('js')
<!-- zTree js -->
<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!--data table-->
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<!-- validity -->
<script type="text/javascript" src="{{asset('plug_in/validity/jquery.validity.js')}}"></script>
<script type="text/javascript" src="{{asset('js/HR/violation.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/jedate/jedate.js')}}"></script>
<!--<script type="text/javascript" src="{{asset('plug_in/doubledate/jquery-1.11.3.min.js')}}"></script>-->
<script>
var iication="";
var columns = [
    {"data": "staff_name", "title": "姓名"},
    {"data": "staff_sn", "title": "编号"},
    {"data": "brand", "title": "品牌"},
    {"data": "department", "title": "部门"},
    {"data": "fruit", "title": "市场与后台", visible: false, searchable: false},
    {"data": "position", "title": "职位", visible: false, searchable: false},
    {"data": "type.name", "title": "类型", visible: false, searchable: false},
    {"data": "reason", "title": "原因", sortable: false,
       
    },
    {"data": "committed_at", "title": "违纪时间"},
	{"data": "times", "title": "次数"},
	{"data": "price", "title": "金额(扣款为正,奖励为负)"},
	{"data": "supervisor_name", "title": "开单人"},
	{"data": "maker_name", "title": "提交人", visible: false},
	{"data": "submitted_at", "title": "提交时间", visible: false},
	{"data": "paid_at", "title": "付款时间"},
	{"data": "id", "title": "操作",defaultContent: "",
				"render": function (datas, nTd,type, row, meta) {
					<?php if ($authority->checkAuthority(104)) { ?>
                return '<a href="#" onclick="addDataTolsfla(' + datas + ')"  style="margin-right:10px; display:inline-block;"data-toggle="modal" data-target="#myModal" vid="' + datas + '" title="编辑" class="edit btn btn-sm btn-primary" ><i class="fa fa-edit fa-fw"></i></a><a href="#" onclick="delivery(' + datas + ')"  vid="' + datas + '" title="确认交钱"style="margin-right:10px;display:inline-block;"class="delete_config btn btn-sm btn-success" ><i class="fa fa-cny fa-fw"></i></a><a href="#" title="删除"class="btn btn-sm btn-danger"style="display:inline-block" vid="' + datas + '" onclick="deleteviolation(' + datas + ')" ><i class="fa fa-trash-o fa-fw"></i></a></div>';
             <?php } ?>
			 $(nTd).html().css({"text-align": "center"});
			 return false;
			 }
    }
	
];
var buttons = [
    {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "titleAttr": "可见字段", "className": "btn-primary"},
    {"text": "<i class='fa fa-refresh fa-fw'></i>", "titleAttr": "刷新", "className": "btn-primary", "action": function () {
            iication.fnDraw();
        }
    },
    {"text": "<i class='fa fa-filter fa-fw' id='filter_icon'></i>", "titleAttr": "筛选", "className": "btn-primary", "action": function () {
            $("#filter").slideToggle();
        }
    },
	<?php if ($authority->checkAuthority(104)) { ?>
	{"text": "<i class='fa fa-download fa-fw'></i>", "action":ertae , "titleAttr": "导出为Excel"},
	<?php } ?>
];
$(function(){
        	/*大爱单列表*/
iication = $("#shop_list").dataTable({
	"columns": columns,
        "ajax": "/hr/violation/list",
        "scrollX": 1545,
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