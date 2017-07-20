var table, zTreeSetting;
var csrfToken = $("meta[name='_token']").attr("content");

$(function () {
    $("#addForm").validity(function () {
        // $(this).find("input[name='shop_sn']").require().minLength(3).maxLength(10);
        // $(this).find("input[name='name']").require().minLength(3).maxLength(10);
    }, submitByAjax);
    /* dataTables start  */
    table = $('#leave_table').dataTable({
        "columns": columns,
        "ajax": {
            url: HOLIDAY.list,
            dataType: 'JSONP',
        },
        // "ajax":HOLIDAY.list,
        "scrollX": 746,
        "order": [[0, "asc"]],
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": buttons
    });


    /* dataTables end  */

    /* zTree start */
    departmentOptionsZTreeSetting = {
        async: {
            url: "/hr/department/tree?_token=" + csrfToken,
            dataFilter: function (treeId, parentNode, responseData) {
                if (treeId == "department_filter_option") {
                    return [{"name": "全部", "drag": true, "id": "0", "children": responseData, "iconSkin": " _", "open": true}];
                } else {
                    return responseData;
                }

            }
        },
        view: {
            dblClickExpand: false
        },
        callback: {
            onClick: function (event, treeId, treeNode) {
                if (treeNode.drag) {
                    var options = $(event.target).parents(".ztreeOptions");
                    if (treeNode.id == 0) {
                        options.prev().children("option").first().prop("selected", true);
                    } else {
                        options.prev().children("option[value=" + treeNode.id + "]").prop("selected", true);
                    }
                    options.hide();
                    options.prev().change();
                }
            }
        }
    };
    /* zTree End */
    $("select[name='province_id'],select[name='city_id']").on("change", getDistrictOptions);
});

function callbackfn(data) {
    console.log(data)
}

//添加店铺
function addShop() {
    oaWaiting.show();
    $("#addForm")[0].reset();
    oaWaiting.hide();
    $("#openAddByOne").click();
}

function imports() {
    oaWaiting.show();
    // $("#addForm")[0].reset();
    oaWaiting.hide();
    $("#openAddByOne").click();
}

//编辑店铺
function edit(detail) {
    var form = $("#editForm");
    oaWaiting.show();
    form[0].reset();
    form.find("input,select").each(function () {
        var value = detail[$(this).attr("name")];
        if (value !== undefined) {
            $(this).val(value);
        }
    });
    form.find(".validity-tooltip").remove();
    form.find("select[name='province_id']").change();
    form.find("select[name='city_id']").change();
    oaWaiting.hide();
    $("#openEditByOne").click();
}

function submitByAjax(form) {
    oaWaiting.show();
    var url = $(form).attr("action");
    // var data = $(form).serialize();  
    var data = new FormData($(form)[0]);
    var type = $(form).attr('method');

    $.ajax({
        type: type,
        url: url,
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (msg) {
            // console.log(msg['status']);
            console.log(msg);
            if (msg['status'] === 1) {
            }
            table.fnDraw();
            $("#wenjian").val('');
            $(".close").click();
            oaWaiting.hide();


        },
        error: function (err) {
            $("#wenjian").val('');
            $(".close").click();
            oaWaiting.hide();
            console.log(err.responseText);
        }
    });
    return false;
}

function del(id) {
    var _confirm = confirm("确认撤销？");
    if (_confirm) {
        oaWaiting.show();
        // var url = '/hr/shop/delete?_token=' + csrfToken;
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: HOLIDAY.cancel,
            data: data,
            async: false,
            dataType: 'json',
            success: function (msg) {
                console.log(msg);
                if (msg['status'] == 1) {
                    table.fnDraw();
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

function searchStaff(obj) {
    var name = $(obj).parent().prev().val();
    var url = "/hr/staff/search?_token=" + csrfToken;
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

function setStaff(id) {
    oaWaiting.show();
    var url = "/hr/shop/shop_modal_set?_token=" + csrfToken;
    var data = {"role_id": id};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'text',
        success: function (msg) {
            $("body").append(msg);
            $("#openSetStaff").click();
            $("#setStaff").bind("remove", function () {
                table.fnDraw();
            });
            oaWaiting.hide();
        },
        error: function (err) {
            document.write(err.responseText);
        }
    });
}

function showTreeViewOptions(obj) {
    var options = $(obj).next(".ztreeOptions");
    var width = $(obj).outerWidth();
    departmentTriger = obj;
    options.outerWidth(width);
    $(obj).children("option").hide();
    if (options.html().length == 0) {
        $.fn.zTree.init(options, departmentOptionsZTreeSetting);
    }
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

/**
 * 获取区划选项
 */
function getDistrictOptions() {
    var parentId = $(this).val();
    var nextTag = $(this).parent().next().children("select");
    if (parentId === "0" || parentId === null) {
        nextTag.html("");
        return false;
    }
    var optionId = nextTag.attr("origin_value");
    var url = "/hr/staff/district?_token=" + csrfToken;
    var data = {"parent_id": parentId};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        async: false,
        dataType: 'text',
        success: function (msg) {
            nextTag.html(msg);
            nextTag.val(optionId);
        }
    });
}