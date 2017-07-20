
//流程运行列表
var token = $('meta[name="_token"]').attr('content');
var flowRunTable = $("#flowRunTable").dataTable({
    "columns": [
        {"data": "id", "title": "运行流程id"},
        {"data": "run_id", "title": "流程实例ID"},
        {"data": "run_name", "title": "流程实例名称", "render": function (data, type, row, meta) {
                if (data.length > 30) {
                    return data.substring(0, 30) + "...";
                }
                return data;
            }, "createdCell": function (cell, cellData, rowData, rowIndex, colIndex ) {
                  $(cell).attr('title',cellData);
            }
        },
        {"data": "flow_id", "title": "流程id"},
        {"data": "begin_user", "title": "流程发起人ID"},
        {"data": "begin_dept", "title": "流程发起人部门ID"},
        {"data": "begin_time", "title": "流程实例创建时间"},
        {"data": "end_time", "title": "流程实例结束时间"},
//        {"data": "attachment_id", "title": "附件id串"},
//        {"data": "attachment_name", "title": "附件名称串"},
//        {"data": "focus_user", "title": "关注该流程的用户"}
    ],
    "ajax": "/workflow/flowRunTableList?_token=" + token
});


var wAlert = window.alert;
window.alert = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        var realert = iwindow.alert(message);
        iframe.parentNode.removeChild(iframe);
        return realert;
    } catch (exc) {
        return wAlert(message);
    }
};
var wConfirm = window.confirm;
window.confirm = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        var reConfirm = iwindow.confirm(message);
        iframe.parentNode.removeChild(iframe);
        return reConfirm;
    } catch (exc) {
        return wConfirm(message);
    }
};

