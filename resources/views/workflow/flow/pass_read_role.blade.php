<!DOCTYPE html>
<html>
<head>
    <title>设置传阅角色</title>
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
        <div class="panel" id="divTableRole">
            <div style="border: 1px solid #dddddd;border-radius: 4px;">
                <section class="panel">
                    <header class="panel-heading">
                        职位列表
                    </header>
                    <div class="panel-body" id="roleShow">
                        <table class="table table-striped table-bordered dataTable no-footer" id="position_list"></table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
<script>
    var token = $('meta[name="_token"]').attr('content');
    $(function () {
    /* dataTables end */
    //角色
    table = $('#position_list').dataTable({
        "columns": roleColumns,
        "ajax": "/hr/position/list?_token=" + token,
        "scrollX": 605,
        "language": {"search": "输入查找"},
        "createdRow": function (row, data, dataIndex) {
            if (typeof clickRole === "function") {
                $(row).on("click", "", data, clickRole);
            }
        }
    });
});

var roleColumns = [
    {"data": "id", "title": "编号", "sortable": true, "width": "30px"},
    {"data": "name", "title": "职位名称", "sortable": true, "width": "120px"},
    // {"data": "level", "title": "职级", "sortable": true, "width": "30px"},
    // {"data": "is_public", "title": "是否共享", "sortable": true, "width": "60px",
    //     "render": function (data, type, row, meta) {
    //         return data === 0 ? '<span class="text-danger">否</span>' : '<span class="text-success">是</span>';
    //     }
    // }
];

//点击角色
function clickRole(event) {
    var data = event.data;
    var view_role_name = window.opener.document.getElementById("view_role_name").value;
    var view_role_id = window.opener.document.getElementById("view_role_id").value;
    if('' != view_role_name)
    {
        //存在
        var ex_name = data.name+',';
        var ex_id = data.id+',';
        if(-1 != view_role_name.indexOf(ex_name))
        {
            view_role_name = view_role_name.replace(ex_name, '');
            view_role_id = view_role_id.replace(ex_id, '');
            window.opener.document.getElementById("view_role_name").value = view_role_name;
            window.opener.document.getElementById("view_role_id").value = view_role_id;
        }
        else
        {
            window.opener.document.getElementById("view_role_name").value += ex_name;
            window.opener.document.getElementById("view_role_id").value += ex_id;
        }
    }
    else
    {
        window.opener.document.getElementById("view_role_name").value = data.name + ',';
        window.opener.document.getElementById("view_role_id").value = data.id + ',';
    }
}
//得到角色的当前对象数据
function getPrcsPrivThisObject(data) {
    var _this = new Object();
    _this.name = data.name;
    _this.id = data.id;
    return _this;
}

</script>
</html>
