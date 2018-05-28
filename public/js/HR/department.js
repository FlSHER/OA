var table, zTreeSetting;

var oaFormOption = {
    callback: {
        submitSuccess: oaFormSubmitSuccess
    }
};

$(function () {
    /* oaForm */
    $(".modal form").oaForm(oaFormOption);

    /* dataTables start */
    table = $('#department_list').oaTable({
        "columns": columns,
        ajax: {url: "/hr/department/list"},
        scrollY: 586,
        "buttons": buttons
    });
    /* dataTables end */

    /* zTree start */

    $.fn.zTree.init($("#department_tree_view"), departmentZTreeSetting);//部门排序

    departmentOptionsZTreeSetting = {
        async: {
            url: "/hr/department/tree",
            dataFilter: function (treeId, parentNode, responseData) {
                return [{"name": "无", "id": 0, "children": responseData, "open": true, "drag": true, "iconSkin": " _"}];
            }
        },
        view: {
            dblClickExpand: function (treeId, treeNode) {
                return treeNode.level > 0;
            }
        },
        callback: {
            onClick: function (event, treeId, treeNode) {
                if (treeNode.drag) {
                    var options = $(event.target).parents(".ztreeOptions");
                    options.prev().children("option[value=" + treeNode.id + "]").prop("selected", true);
                    options.hide();
                }
            }
        }
    };

    /* zTree End */
});

function addDepartment() {
    oaWaiting.show();
    var form = $("#addDepartmentForm");
    form[0].reset();
    oaWaiting.hide();
}

function editDepartment(id) {
    oaWaiting.show();
    var form = $("#editDepartmentForm");
    var url = "/hr/department/info";
    var data = {"id": id};
    form.oaForm()[0].fillData(url, data);
    oaWaiting.hide();
}

function deleteDepartment(id) {
    var _confirm = confirm("确认删除当前部门及所有子部门？");
    if (_confirm) {
        oaWaiting.show();
        var url = '/hr/department/delete';
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            async: false,
            dataType: 'json',
            success: function (msg) {
                if (msg['status'] === 1) {
                    table.draw(false);
                    $.fn.zTree.getZTreeObj("department_tree_view").reAsyncChildNodes(null, "refresh");
                    oaWaiting.hide();
                } else if (msg['status'] === -1) {
                    oaWaiting.hide(function () {
                        alert(msg['message']);
                    });
                }
            }
        });
    }
}

function reloadDepartmentOptions() {
    var url = "/hr/department/options";
    $.ajax({
        type: "POST",
        url: url,
        dataType: 'text',
        success: function (msg) {
            msg = '<option value="0">< 无 ></option>' + msg;
            $("select[name='parent_id']").html(msg);
        },
        error: function (err) {
            alert(err.responseText);
        }
    });

}

function updateOrder(event, treeId, treeNodes, targetNode, moveType) {
    if (moveType === null) {
        return false;
    }
    oaWaiting.show();
    var nodes = $.fn.zTree.getZTreeObj(treeId).getNodes();
    nodes = getNodesId(nodes);
    var url = '/hr/department/order';
    var data = {'info': nodes};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        success: function (msg) {
            if (msg['status'] === 1) {
                table.draw(false);
                oaWaiting.hide();
                return true;
            } else if (msg['status'] === -1) {
                oaWaiting.hide(function () {
                    alert(msg['message']);
                });
            }
        },
        error: function (err) {
            document.write(err.responseText);
        }
    });
}

function getNodesId(nodes) {
    return nodes.map(function (item) {
        return {"id": item.id, "children": getNodesId(item.children)};
    });
}

function expandAll() {
    $.fn.zTree.getZTreeObj("department_tree_view").expandAll(true);
}

function collapseAll() {
    $.fn.zTree.getZTreeObj("department_tree_view").expandAll();
}

function searchStaff(obj) {
    var name = $(obj).parent().prev().val();
    var url = "/hr/staff/search";
    var data = {"target": {"staff_sn": "manager_sn", "realname": "manager_name"}};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'text',
        success: function (msg) {
            $("body").append(msg);
            $("#openSearchStaffResult").click();
        },
        error: function (err) {
            document.write(err.responseText);
        }
    });
}

function showTreeViewOptions(obj) {
    var options = $(obj).next(".ztreeOptions");
    var width = $(obj).outerWidth();
    var id = $(obj).parents("form").find("input[name='id']").val();
    departmentTriger = obj;
    if (options.html().length == 0) {
        options.outerWidth(width);
    }
    $(obj).children("option").hide();
    if (id === undefined) {
        departmentOptionsZTreeSetting.async.otherParam = [];
    } else {
        departmentOptionsZTreeSetting.async.otherParam = ["without", [id]];
    }
    $.fn.zTree.init(options, departmentOptionsZTreeSetting);
    options.toggle();
    $("body").bind("click", hideTreeViewOptions);
    return false;
}

function hideTreeViewOptions(event) {
    if (!($(event.target).hasClass("ztreeOptions") || $(event.target).parents(".ztreeOptions").length > 0 || event.target == departmentTriger)) {
        $(".ztree.ztreeOptions").hide();
        $("body").unbind("click", hideTreeViewOptions);
    }
}

function oaFormSubmitSuccess(msg, obj) {
    table.draw(false);
    $.fn.zTree.getZTreeObj("department_tree_view").reAsyncChildNodes(null, "refresh");
    reloadDepartmentOptions();
    $(".close").click();
}