<!DOCTYPE html>
<html>
<head>
    <title>设置传阅部门</title>
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
        <div class="panel" id="divTableDept" style="height: 900px;">
            <div style="border: 1px solid #dddddd;border-radius: 4px;">
                <section class="panel">
                    <header class="panel-heading custom-tab dark-tab">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#list" data-toggle="tab" id="tableTab">部门列表</a>
                            </li>
                            <li class="">
                                <a href="#treeview" data-toggle="tab">部门结构</a>
                            </li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="list">
                                <table class="table table-striped table-bordered dataTable no-footer" id="department_list"></table>
                            </div>
                            <div class="tab-pane" id="treeview">
                                <div class="btn-group" id="nestable_list_menu">
                                    <a type="button" class="btn btn-sm btn-default" href="javascript:expandAll()">全部展开</a>
                                    <a type="button" class="btn btn-sm btn-default" href="javascript:collapseAll()">全部收起</a>
                                </div>
                                <div class="ztree" id="deptShow" style="overflow:auto;max-height:750px;"></div>
                            </div>
                        </div>
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
    /*部门*/
    $.fn.zTree.init($("#deptShow"), departmentOptionsZTreeSetting);

    /* dataTables start */
    table = $('#department_list').dataTable({
        "columns": deptColumns,
        "ajax": "/hr/department/list?_token=" + token,
        "scrollX": 605,
        "order": [[0, "asc"]],
        "language": {"search": "输入查找"},
        "createdRow": function (row, data, dataIndex) {
            if (typeof clickDept === "function") {
                $(row).on("click", "", data, clickDept);
            }
        }
    });
});

/* zTree start */
departmentOptionsZTreeSetting = {
    async: {
        url: "/hr/department/tree?_token=" + token
    },
    callback: {
        onClick: function (event, treeId, treeNode) {
            clickDept(treeNode);
        }
    }
};

var deptColumns = [
    {"data": "id", "title": "编号"},
    {"data": "name", "title": "部门名称"},
    {"data": "full_name", "title": "部门全称"},
    {"data": "brand.name", "title": "品牌"},
    {"data": "manager_name", "title": "部门主管"
    }];


function expandAll() {
    $.fn.zTree.getZTreeObj("deptShow").expandAll(true);
}

function collapseAll() {
    $.fn.zTree.getZTreeObj("deptShow").expandAll();
}

//点击部门
function clickDept(event) {
    var data = (event.data) ? event.data : event;
    var view_dept_name = window.opener.document.getElementById("view_dept_name").value;
    var view_dept_id = window.opener.document.getElementById("view_dept_id").value;
    if('' != view_dept_name)
    {
        //存在
        var ex_name = data.name+',';
        var ex_id = data.id+',';
        if(-1 != view_dept_name.indexOf(ex_name))
        {
            view_dept_name = view_dept_name.replace(ex_name, '');
            view_dept_id = view_dept_id.replace(ex_id, '');
            window.opener.document.getElementById("view_dept_name").value = view_dept_name;
            window.opener.document.getElementById("view_dept_id").value = view_dept_id;
        }
        else
        {
            window.opener.document.getElementById("view_dept_name").value += ex_name;
            window.opener.document.getElementById("view_dept_id").value += ex_id;
        }
    }
    else
    {
        window.opener.document.getElementById("view_dept_name").value = data.name + ',';
        window.opener.document.getElementById("view_dept_id").value = data.id + ',';
    }
}

//部门点击的当前数据对象
function getDeptThisObject(data) {
    var this_Object = new Object();
    this_Object.id = data.id;
    this_Object.name = data.full_name;
    return this_Object;
}
</script>
</html>
