var token = $('meta[name="_token"]').attr('content');

//授权范围 人员添加按钮
function SelectUser(id) {
    $("#" + id).siblings('div').css('display', 'none');
    $("#" + id).css('display', 'block');
}
//授权范围  清空按钮
function ClearUser(tagID_1, tagID_2) {
    $('#' + tagID_1).text('');
    $('#' + tagID_2).text('');
}
//授权范围（部门） 添加按钮
function SelectDept(id) {
    $("#" + id).siblings('div').css('display', 'none');
    $("#" + id).css('display', 'block');
}
//授权范围（角色） 添加按钮
function SelectPriv(id) {
    $("#" + id).siblings('div').css('display', 'none');
    $("#" + id).css('display', 'block');
}
//点击员工
function searchStaffClick(event) {
    var data = event.data;
    if ('' != $("#copy_prcs_user_name").text())
    {
        if (-1 != String($("#copy_prcs_user_name").text()).indexOf(String(data.realname)))
        {
            var pattern = data.realname + ',';
            var str = $("#copy_prcs_user_name").text();
            str = str.replace(pattern, "");
            $("#copy_prcs_user_name").text(str);
            //隐藏
            var strtmp = getUserObject(data);
            var val = $("#prcs_user").text();
            var strObject = JSON.parse(val);
            for (var i = 0; i <= strObject.length - 1; i++) {
                if (JSON.stringify(strObject[i]) == JSON.stringify(strtmp)) {
                    strObject.splice(i, 1);
                }
            }
            var dataStr = JSON.stringify(strObject);
            if (strObject.length == 0) {
                dataStr = '';
            }
            $("#prcs_user").text(dataStr);
        } else
        {
            $("#copy_prcs_user_name").append(data.realname + ',');
            //隐藏
            var strtmp = getUserObject(data);
            var val = $("#prcs_user").text();
            var valObject = JSON.parse(val);
            valObject.push(strtmp);
            $("#prcs_user").text(JSON.stringify(valObject));
        }
    } else
    {
        $("#copy_prcs_user_name").append(data.realname + ',');
        //隐藏
        var strtmp = getUserObject(data);
        var dataObject = new Array();
        dataObject.push(strtmp);
        $("#prcs_user").text(JSON.stringify(dataObject));
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
//点击部门
function clickDept(event) {
    var data = (event.data) ? event.data : event;
    if ('' != $("#copy_prcs_dept_name").text())
    {
        if (-1 != String($("#copy_prcs_dept_name").text()).indexOf(String(data.full_name)))
        {
            var pattern = data.full_name + ',';
            var str = $("#copy_prcs_dept_name").text();
            str = str.replace(pattern, "");
            $("#copy_prcs_dept_name").text(str);
            //隐藏的值
            var one_Object = getDeptThisObject(data);
            var val = $("#prcs_dept").text();
            var valObject = JSON.parse(val);
            for (var i = 0; i <= valObject.length - 1; i++) {
                if (JSON.stringify(valObject[i]) == JSON.stringify(one_Object)) {
                    valObject.splice(i, 1);
                }
            }
            var data = JSON.stringify(valObject);
            if (valObject.length == 0) {
                data = '';
            }
            $("#prcs_dept").text(data);
        } else
        {
            $("#copy_prcs_dept_name").append(data.full_name + ',');
            //隐藏的值
            var val = $("#prcs_dept").text();
            var valObject = JSON.parse(val);
            var one_Object = getDeptThisObject(data);
            valObject.push(one_Object);
            var str = JSON.stringify(valObject);
            $("#prcs_dept").text(str);
        }
    } else
    {
        $("#copy_prcs_dept_name").append(data.full_name + ',');
        //隐藏的值
        var one_Object = getDeptThisObject(data);
        var deptObject = new Array();
        deptObject.push(one_Object);
        $("#prcs_dept").text(JSON.stringify(deptObject));
    }
}

//部门点击的当前数据对象
function getDeptThisObject(data) {
    var this_Object = new Object();
    this_Object.id = data.id;
    this_Object.name = data.full_name;
    return this_Object;
}
//点击角色
function clickRole(event) {
    var data = event.data;
    if ('' != $("#copy_prcs_priv_name").text())
    {
        if (-1 != String($("#copy_prcs_priv_name").text()).indexOf(String(data.name)))
        {
            var pattern = data.name + ',';
            var str = $("#copy_prcs_priv_name").text();
            str = str.replace(pattern, "");
            $("#copy_prcs_priv_name").text(str);
            //隐藏
            var one_Object = getPrcsPrivThisObject(data);
            var str = $("#prcs_priv").text();
            var valObject = JSON.parse(str);
            for (var i = 0; i <= valObject.length - 1; i++) {
                if (JSON.stringify(valObject[i]) == JSON.stringify(one_Object)) {
                    valObject.splice(i, 1);
                }
            }
            var data = JSON.stringify(valObject);
            if (valObject.length == 0) {
                data = '';
            }
            $("#prcs_priv").text(data);
        } else
        {
            $("#copy_prcs_priv_name").append(data.name + ',');
            //隐藏
            var val = $("#prcs_priv").text();
            var valObject = JSON.parse(val);
            var one_Object = getPrcsPrivThisObject(data);
            valObject.push(one_Object);
            var str = JSON.stringify(valObject);
            $("#prcs_priv").text(str);
        }
    } else
    {
        $("#copy_prcs_priv_name").append(data.name + ',');
        //隐藏
        var one_Object = getPrcsPrivThisObject(data);
        var priv_Object = new Array();
        priv_Object.push(one_Object);
        $("#prcs_priv").text(JSON.stringify(priv_Object));
    }
}
//得到角色的当前对象数据
function getPrcsPrivThisObject(data) {
    var _this = new Object();
    _this.name = data.name;
    _this.id = data.id;
    return _this;
}

$(function () {
    /* dataTables start */
    searchTable = $('#searchTable').dataTable({
        "columns": [
            {"data": "staff_sn", "title": "编号", "searchable": false},
            {"data": "realname", "title": "姓名"},
            {"data": "department.full_name", "title": "部门全称", "searchable": false},
            {"data": "position.name", "title": "职位", "searchable": false},
            {"data": "status.name", "title": "状态", "searchable": false},
            {"data": "hired_at", "title": "入职时间", "searchable": false}
        ],
        "ajax": "/hr/staff/list?_token=" + token,
        "scrollY": 900,
        "info": false,
        "pageLength": 15,
        "bAutoWidth": false,
        "lengthChange": false,
        "pagingType": "numbers",
        "stateSave": false,
        "order": [[0, "asc"]],
        "language": {"search": "输入姓名查找"},
        //"search": {"search": "{{$realname}}"},
        "createdRow": function (row, data, dataIndex) {
            if (typeof searchStaffClick === "function") {
                $(row).on("click", "", data, searchStaffClick);
            }
        }
    });
    /* dataTables end */
    /*部门*/
    $.fn.zTree.init($("#deptShow"), departmentOptionsZTreeSetting);

    /* dataTables start */
    table = $('#department_list').dataTable({
        "columns": deptColumns,
        "ajax": "/hr/department/list?_token=" + token,
        "scrollX": 746,
        "order": [[0, "asc"]],
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "createdRow": function (row, data, dataIndex) {
            if (typeof clickDept === "function") {
                $(row).on("click", "", data, clickDept);
            }
        }
    });

    /* dataTables end */
    //角色
    table = $('#position_list').dataTable({
        "columns": roleColumns,
        "ajax": "/hr/position/list?_token=" + token,
        "scrollX": 746,
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "createdRow": function (row, data, dataIndex) {
            if (typeof clickRole === "function") {
                $(row).on("click", "", data, clickRole);
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
    {"data": "id", "title": "编号", "width": "5px"},
    {"data": "name", "title": "部门名称", "width": "85px"},
    {"data": "full_name", "title": "部门全称", "width": "130"},
    {"data": "brand.name", "title": "品牌", "width": "30px"},
    {"data": "manager_name", "title": "部门主管", "width": "10px"
    }];
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


function expandAll() {
    $.fn.zTree.getZTreeObj("deptShow").expandAll(true);
}

function collapseAll() {
    $.fn.zTree.getZTreeObj("deptShow").expandAll();
}