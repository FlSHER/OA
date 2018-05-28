var table, zTreeSetting;
$(function () {

    /* validity */
    $("#addForm").validity(function () {
        $(this).find("input[name='role_name']").require().maxLength("10");
    }, submitByAjax);
    $("#editForm").validity(function () {
        $(this).find("input[name='role_name']").require().maxLength("10");
    }, submitByAjax);
    /* dataTables start */
    table = $('#list').dataTable({
        "columns": dataTableColumns,
        "ajax": "/system/role/list",
        "scrollX": 746,
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": dataTableButtons
    });
    /* dataTables end */

    /* zTree start */

    departmentZTreeSetting = {
        async: {
            url: "/hr/department/tree"
        },
        check: {
            enable: true,
            chkboxType: {"Y": "s", "N": "ps"}
        }
    };
    /* zTree End */
});

function addByOne() {
    var form = $("#addForm");
    $("#waiting").fadeIn(200);
    form[0].reset();
    departmentTreeview = $.fn.zTree.init(form.find(".department_treeview"), departmentZTreeSetting);
    form.find(".validity-tooltip").remove();
    $("#waiting").fadeOut(300);
    $("#openAddByOne").click();
}

function editByOne(detail) {
    var form = $("#editForm");
    $("#waiting").fadeIn(200);
    form[0].reset();
    form.find("input[name='brand_id[]']:not(:disabled)").attr("checked", false);
    if (detail["brand"].length > 0) {
        form.find("input[name='brand_id[]']").each(function () {
            for (var i in detail["brand"]) {
                if ($(this).val() == detail["brand"][i]["id"]) {
                    $(this).attr("checked", true);
                }
            }
        });
    }
    form.find("input,select").each(function () {
        var value = detail[$(this).attr("name")];
        if (value !== undefined) {
            $(this).val(value);
        }
    });
    departmentZTreeSetting.async.otherParam = {"role_id": detail["id"]};
    departmentTreeview = $.fn.zTree.init(form.find(".department_treeview"), departmentZTreeSetting);
    form.find(".validity-tooltip").remove();
    $("#waiting").fadeOut(300);
    $("#openEditByOne").click();
}

function deleteByOne(id) {
    var _confirm = confirm("确认删除？");
    if (_confirm) {
        $("#waiting").fadeIn(200);
        var url = '/system/role/delete';
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            async: false,
            dataType: 'json',
            success: function (msg) {
                if (msg['status'] === 1) {
                    table.fnDraw();
                    $("#waiting").fadeOut(300);
                } else if (msg['status'] === -1) {
                    $("#waiting").fadeOut(300, function () {
                        alert(msg['message']);
                    });
                }
            }
        });
    }
}

function setStaff(id) {
    $("#waiting").fadeIn(200);
    var url = "/hr/staff/multi_set_modal";
    var data = {
        "eloquent": "App\\Models\\Role",
        "primary": {"key": "role_id", "value": id},
        "submit_url": null
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'text',
        success: function (msg) {
            $("body").append(msg);
            $("#openSetStaff").click();
            setStaffCallback = function () {
                table.fnDraw();
            };
            $("#waiting").fadeOut(300);
        },
        error: function (err) {
            document.write(err.responseText);
        }
    });
    $("#openEditStaff").click();
}

function submitByAjax(form) {
    $("#waiting").fadeIn(200);
    var url = $(form).attr("action");
    var data = $(form).serializeArray();
    var type = $(form).attr('method');
    var checkedNodes = departmentTreeview.getCheckedNodes(true);
    var uncheckedNodes = departmentTreeview.getCheckedNodes(false);
    checkedNodes.map(function (item) {
        data.push({"name": "department[]", "value": item.id});
    });
    if (uncheckedNodes.length == 0) {
        data.push({"name": "department[]", "value": "0"});
    }
    $.ajax({
        type: type,
        url: url,
        data: data,
        dataType: 'json',
        success: function (msg) {
            if (msg['status'] === 1) {
                table.fnDraw();
                $(".close").click();
                $("#waiting").fadeOut(300);
            }
        },
        error: function (err) {
            alert(err.responseText);
        }
    });
    return false;
}