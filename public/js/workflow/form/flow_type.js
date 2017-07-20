
//流程管理
var token = $('meta[name="_token"]').attr('content');
var flowTypeTable = $("#flowTypeTable").dataTable({
    "columns": [
        {"data": "flow_id", "title": "id"},
        {"data": "flow_name", "title": "流程名称"},
        {"data": "flowclassify.flow_classifyname", "title": "流程分类", "sortable": false},
        {"data": "flow_desc", "title": "流程说明"},
        {"data": "form_id", "title": "表单id"},
        {"data": "form_name", "title": "表单名称"},
        {"data": "flow_type", "title": "流程类型", "defaultContent": "",
            "render": function (data, type, row) {
                return (data==1)?"固定流程":(data==2)?"自由流程":"";
            }
        },
        {"data": "flow_crtime", "title": "创建时间"},
        {"data": "flow_mdtime", "title": "修改时间"},
        {"data": "flow_status", "title": "流程状态", "defaultContent": "",
            "render": function (data, type, row, meta) {
                var statusName = [
                    '<span class="text-danger">停用</span>',
                    '<span class="text-success">启用</span>',
                    '已删除'
                ];
                return statusName[data];
//                return data = data == 0 ? "停用" : data == 1 ? "启用" : data == 2 ? "已删除" : '';
            }
        },
        {"data": "flow_id", "title": "操作", "sortable": false,
            "createdCell": function (nTd, sData, oData, iRow, iCol) {
                var flow_status = oData.flow_status;
                var str = '';
                switch (flow_status) {
                    case 0:
                        str = '<a href="javascript:release(' + sData + ',' + flow_status + ')" >启用</a> | <a href="javascript:deleteFlow(' + sData + ',' + flow_status + ')" >删除</a>';
                        break;
                    case 1:
                        str = ' <a href="javascript:stop(' + sData + ',' + flow_status + ')">停用</a> ';
                        break;
                    default:
                        str = '';
                }

                $(nTd).html(str);
            }
        }
//        {"data": "aip_files", "title": "已相关的版式文件信息"},
//        {"data": "pre_set", "title": "待定"},
//        {"data": "view_user", "title": "传阅人ID串工作办理结束时选择的传阅人"},
//        {"data": "archive", "title": "是否归档"},
//        {"data": "force_over", "title": "强制结束信息"},
//        {"data": "work_level", "title": "工作等级"},
//        {"data": "del_time", "title": "删除时间"}
//        {"data": "formdesign.fields", "title": "字段数",
//            "render": function (data, type, row, meta) {
//                if (data == null) {
//                    return "0";
//                }
//                return data;
//            }
//        },
//        {"data": "updated_at", "title": "最后修改时间", "searchable": false},
//        {"data": "sort", "title": "排序"},
//        {"data": "formdesign.is_use", "title": "使用状态", "defaultContent": "未设计",
//            "render": function (data, type, row, meta) {
//                if (data === 0) {
//                    return "否";
//                } else if (data == '') {
//                    return "未设计";
//                } else if (data == 1) {
//                    return '是';
//                }
//            }
//        },
//        {"data": "id", "title": "表单操作", "sortable": false,
//            "render": function (data, type, row, meta) {
//                return '<a href="javascript:formDesign(' + data + ')"  title="智能设计">智能设计</a> | <a href="javascript:phoneDesign(' + data + ')"  title="移动设计">移动设计</a> | <a href="javascript:preview(' + data + ')"  title="预览">预览</a> | <a href="javascript:excelBlade(' + data + ')" title="导出">导出</a>';
//            }
//        },
//        {"data": "id", "title": "操作", "sortable": false,
//            "render": function (data, type, row, meta) {
//                return '<a href="#myModal-1" data-toggle="modal" title="编辑" class="edit" urlid="' + data + '">编辑 </a> | <a href="javascript:void(0);" title="删除"class="delete_config" deleteId="' + data + '">删除</a>';
//            }
//        }
    ],
    "ajax": "/workflow/flowTypeTableList?_token=" + token
});
//流程管理启用
function release(flow_id, flow_status) {
    if (confirm("确认启用该流程？")) {
        var url = './flowTypeRelease';
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'text',
            data: {flow_id: flow_id, flow_status: flow_status},
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (msg) {
                if (msg == 'success') {
                    flowTypeTable.fnDraw();
                } else if (msg == 'error') {
                    alert('流程启用失败');
                }
            }
        });
    }
}
//流程管理停用
function stop(flow_id, flow_status) {
    if (confirm("确认停用该流程？")) {
        var url = './flowTypeStop';
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'text',
            data: {flow_id: flow_id, flow_status: flow_status},
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (msg) {
                if (msg == 'success') {
                    flowTypeTable.fnDraw();
                } else if (msg == 'error') {
                    alert('流程停用失败');
                }
            }
        });
    }
}
//流程管理删除
function deleteFlow(flow_id, flow_status) {
    if (confirm("确认删除该流程？")) {
        var url = './flowTypeDelete';
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'text',
            data: {flow_id: flow_id, flow_status: flow_status},
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (msg) {
                if (msg == 'success') {
                    flowTypeTable.fnDraw();
                } else if (msg == 'error') {
                    alert('流程删除失败');
                }
            }
        });
    }
}



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

