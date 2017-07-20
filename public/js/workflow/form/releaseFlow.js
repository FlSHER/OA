
//已发布流程运行列表
var token = $('meta[name="_token"]').attr('content');
var releaseFlowTable = $("#releaseFlowTable").dataTable({
    "columns": [
        {"data": "flow_id", "title": "id"},
        {"data": "flow_name", "title": "流程名称"},
        {"data": "flowclassify.flow_classifyname", "title": "流程分类","sortable":false},
        {"data": "flow_desc", "title": "流程说明"},
        {"data": "form_name", "title": "表单名称"},
        {"data": "flow_crtime", "title": "创建时间"},
        {"data": "flow_mdtime", "title": "修改时间"},
        {"data": "flow_status", "title": "流程状态","defaultContent":"",
            "render": function (data, type, row, meta) {
                var statusName =[
                    '停用',
                    '启用',
                    '已删除'
                ];
                return statusName[data];
//                return data = data == 0 ? "停用" : data == 1 ? "启用" : data == 2 ? "已删除" : '';
            }
        }

    ],
    "ajax": "/workflow/releaseFlowTable?_token=" + token
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

