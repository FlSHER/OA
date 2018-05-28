<!DOCTYPE html>
<html>
<head>
	<title>设置传阅人员</title>
	<meta name="_token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
	<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" />
	<script type="text/javascript" src="{{asset('js/jquery-3.1.1.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/bootstrap.js')}}"></script>
	<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
	<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
	<!-- zTree css -->
	<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
	<!-- zTree js -->
	<script type="text/javascript" src="{{asset('plug_in/ztree/js/jquery.ztree.all.js')}}"></script>
</head>
<body>
	<div class="panel-body">
    	<div class="panel" id="divTableUser">
        	<table class="table table-hover table-bordered dataTable no-footer" id="searchTable">
        	</table>
    	</div>
	</div>
</body>
<script>
	var token = $('meta[name="_token"]').attr('content');
	$(function () {
    /* dataTables start */
    searchTable = $('#searchTable').dataTable({
        "columns": [
            {"data": "staff_sn", "title": "编号", "searchable": false},
            {"data": "realname", "title": "姓名"},
            {"data": "department.full_name", "title": "部门全称", "searchable": false},
            {"data": "position.name", "title": "职位", "searchable": false},
            {"data": "type.name", "title": "类型", "searchable": false},
            {"data": "status.name", "title": "状态", "searchable": false},
            {"data": "hired_at", "title": "入职时间", "searchable": false}
        ],
        "ajax": "/hr/staff/list?_token=" + token,
        "scrollY": 605,
        "order": [[0, "asc"]],
        "language": {"search": "输入姓名查找"},
        "createdRow": function (row, data, dataIndex) {
            if (typeof searchStaffClick === "function") {
                $(row).on("click", "", data, searchStaffClick);
            }
        }
    });
});

//点击员工
function searchStaffClick(event) {
    var data = event.data;
    var view_user_name = window.opener.document.getElementById("view_user_name").value;
    var view_user_id = window.opener.document.getElementById("view_user_id").value;
    if('' != view_user_name)
    {
    	//存在
    	var ex_realname = data.realname+',';
    	var ex_staff_sn = data.staff_sn+',';
    	if(-1 != view_user_name.indexOf(ex_realname))
    	{
    		view_user_name = view_user_name.replace(ex_realname, '');
    		view_user_id = view_user_id.replace(ex_staff_sn, '');
    		window.opener.document.getElementById("view_user_name").value = view_user_name;
    		window.opener.document.getElementById("view_user_id").value = view_user_id;
    	}
    	else
    	{
    		window.opener.document.getElementById("view_user_name").value += ex_realname;
    		window.opener.document.getElementById("view_user_id").value += ex_staff_sn;
    	}
    }
    else
    {
    	window.opener.document.getElementById("view_user_name").value = data.realname + ',';
    	window.opener.document.getElementById("view_user_id").value = data.staff_sn + ',';
    }
}
//点击人员获取数据对象
function getUserObject(data) {
    var _this = new Object();
    _this.staff_sn = data.staff_sn;
    _this.realname = data.realname;
    _this.position_name = data.position.name;
    _this.department_full_name = data.department.full_name;
    _this.department_name = data.department.name;
    _this.status = data.status.name;
    return _this;
}
</script>
</html>

