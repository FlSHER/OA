@extends('layouts.admin')

@inject('authority','Authority')
@inject('HRM','HRM')

@section('css')
<!-- data table  -->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
<!-- validity -->
<link rel="stylesheet" href="{{asset('plug_in/validity/jquery.validity.css')}}" />
<!-- checkbox -->
<link rel="stylesheet" href="{{asset('css/checkbox.css')}}" />
<!-- datetimepicker  -->
<link rel="stylesheet" href="{{asset('plug_in/datetimepicker/bootstrap-datetimepicker.css')}}" />
@endsection



@section('content')
<div class="row">
	<div class="col-sm-8">
		<section class="panel">
			<header class="panel-heading">
			员工考勤统计
			</header> 
			<!-- 筛选 start -->  
			 {{-- @include('hr/staff_filter') --}}
			<!-- 筛选 end -->
			<!-- 列表 start -->
			<div class="panel-body">
				<table class="table table-striped table-bordered dataTable no-footer" id="transfer">
				</table>
			</div>
			<!-- 列表  end -->
		</section>
	</div>
	<section id="board-right"></section>
</div>
 

@endsection


@section('js')
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<!-- zTree js -->
<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
<!-- validity -->
<script type="text/javascript" src="{{asset('plug_in/validity/jquery.validity.js')}}"></script>
<!-- datetimepicker -->
<script type="text/javascript" src="{{asset('plug_in/datetimepicker/bootstrap-datetimepicker.js')}}"></script>
<!--script for this view-->
<script type="text/javascript" src="{{asset('js/HR/statistic.js')}}"></script>

<script>
var TRANSFER = {
    list:"{{config('api.url.statistic.getlist')}}",
    export:"{{config('api.url.statistic.export')}}",
};

var columns = [
	{"data": "staff_sn", "title": "员工编号"},
	{"data": "staff_name", "title": "员工姓名"},
	{"data": "attendance", "title": "出勤", "width": "30px"},
	{"data": "holiday", "title": "请假", "width": "30px"},
	{"data": "achievement", "title": "总业绩", "width": "30px"},
	{"data": "id", "title": "操作","sortable": false, "width": "50px","createdCell": function (nTd, sData, oData, iRow, iCol) {
	    delete oData.password;
	    delete oData.salt;
	    var html = '';
	  
	    <?php if ($authority->checkAuthority(40)): ?>
	    html +='<button class="btn btn-sm btn-default" title="详情" onclick=\'showStaffInfo(' + JSON.stringify(oData) + ')\'><i class="fa fa-address-card fa-fw"></i></button>';
	    <?php endif; ?>
	 
	
		
	    $(nTd).html(html).css({"padding": "5px", "text-align": "center"});

	    }
	},
 
];

var buttons = [
    {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "titleAttr": "可见字段"},
    {"text": "<i class='fa fa-refresh fa-fw'></i>", "titleAttr": "刷新", "action": function () {
            table.fnDraw();
        }
    },
    {"text": "<i class='fa fa-download fa-fw'></i>", "action": function(){addShop()}, "titleAttr": "数据导出"}
];

</script>
@endsection