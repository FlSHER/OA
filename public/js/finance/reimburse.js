var oTable, hTable, rTable;
var auditStartTime, auditEndTime;
$(function () {

    createDataTable();
    /*
     * Initialse DataTables, with no sorting on the 'details' column
     */
    function createDataTable() {
        oTable = $('#pending-table').dataTable({
            "columns": [
                {"title": "详情", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                    "render": function (data, type, row, meta) {
                        return '<i class = "fa fa-plus-circle show_expense" style = "font-size:20px;cursor:pointer;" reim-id = "' + data + '"></i>';
                    }
                },
                {"title": "订单编号", "data": "reim_sn", "name": "reim_sn", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).css({"max-width": "120px", "overflow": "hidden"}).attr("title", sData);
                    }
                },
                {"title": "申请人", "data": "user_name", "name": "user_name", "sortable": true},
                {"title": "审批人", "data": "approver_name", "name": "approver_name", "sortable": true},
                {"title": "部门", "data": "custom_department.name", "name": "custom_department_id", "sortable": true},
                {"title": "资金归属", "data": "reim_department.name", "name": "reim_department_id", "sortable": true},
                {"title": "申请时间", "data": "send_time", "name": "send_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "审批时间", "data": "approve_time", "name": "approve_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "申请金额", "data": "approved_cost", "name": "approved_cost", "class": "text-right", "sortable": false,
                    "render": function (data, type, row, meta) {
                        return '￥' + data;
                    }
                },
                {"title": "审核金额", "data": "approved_cost", "name": "approved_cost", "class": "text-right", "sortable": false,
                    "render": function (data, type, row, meta) {
                        return '￥<span class="person_cost">' + data + '</span>';
                    }
                },
                {"title": "操作", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        var html = '<a target="_blank" href="/finance/reimburse/print/' + sData + '" class="btn btn-sm btn-default print" title="打印"><i class="fa fa-print"></i></a>' +
                                ' <button class = "btn btn-sm btn-danger reject" title = "驳回"><i class = "fa fa-times"></i></button> <button class = "btn btn-sm btn-success agree" title = "通过"><i class = "fa fa-check"></i></button>';
                        $(nTd).html(html).css("padding", "6px").attr("row_num", iRow);
                    }
                }
            ],
            "ajax": "/finance/reimburse/list",
            "scrollX": 1000,
            "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                    "t" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [
                {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "className": "btn-primary"},
                {"text": "<i class='fa fa-refresh fa-fw'></i>", "className": "btn-primary", "action": function () {
                        oTable.fnDraw();
                    }
                }
            ]
        });
        hTable = $('#history-table').dataTable({
            "columns": [
                {"title": "详情", "data": "id", "name": "id", "class": "text-center",
                    "render": function (data, type, row, meta) {
                        return '<i class = "fa fa-plus-circle show_expense" style = "font-size:20px;cursor:pointer;" reim-id = "' + data + '"></i>';
                    }
                },
                {"title": "订单编号", "data": "reim_sn", "name": "reim_sn", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).css({"max-width": "120px", "overflow": "hidden", "white-space": "nowrap", "text-overflow": "ellipsis"}).attr("title", sData);
                    }
                },
                {"title": "申请人", "data": "user_name", "name": "user_name", "sortable": true},
                {"title": "审批人", "data": "approver_name", "name": "approver_name", "sortable": true},
                {"title": "部门", "data": "custom_department.name", "name": "custom_department_id", "sortable": true},
                {"title": "资金归属", "data": "reim_department.name", "name": "reim_department_id", "sortable": true},
                {"title": "申请时间", "data": "send_time", "name": "send_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "审批时间", "data": "approve_time", "name": "approve_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "审核时间", "data": "audit_time", "name": "audit_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "总金额", "data": "audited_cost", "name": "audited_cost", "class": "text-right", "width": "100px",
                    "render": function (data, type, row, meta) {
                        return '￥' + data;
                    }
                },
                {"title": "操作", "data": "id", "name": "id",
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        var html = '<a target="_blank" href="/finance/reimburse/print/' + sData + '" class="btn btn-sm btn-default print" title="打印"><i class="fa fa-print"></i></a>';
                        $(nTd).html(html).css("padding", "6px");
                    }
                }
            ],
            "ajax": "/finance/reimburse/audited",
            "serverParams": function (aoData) {
                if (auditStartTime) {
                    aoData["filter"] = {"audit_time": {"min": auditStartTime, "max": auditEndTime}};
                }
            },
            "scrollX": 1000,
            "dom": "<'row'<'col-sm-3'l><'col-sm-6'B<'#mytoolbox'>><'col-sm-3'f>r>" +
                    "t" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [
                {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "className": "btn-primary"},
                {"text": "<i class='fa fa-refresh fa-fw'></i>", "className": "btn-primary", "action": function () {
                        hTable.fnDraw();
                    }
                },
                {"text": "审核时间", "className": "reportrange"},
                {"text": "导出为Excel",
                    "action": function () {
                        var params = JSON.stringify(hTable.api(true).ajax.params());
                        var confirmExport = confirm("确认导出？");
                        if (confirmExport) {
                            window.location.href = "/finance/reimburse/excel?params=" + params;
                        }
                    }
                }
            ],
            initComplete: initComplete
        });
        rTable = $('#reject-table').dataTable({
            "columns": [
                {"title": "详情", "data": "id", "name": "id", "class": "text-center",
                    "render": function (data, type, row, meta) {
                        return '<i class = "fa fa-plus-circle show_expense" style = "font-size:20px;cursor:pointer;" reim-id = "' + data + '"></i>';
                    }
                },
                {"title": "订单编号", "data": "reim_sn", "name": "reim_sn", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).css({"max-width": "120px", "overflow": "hidden", "white-space": "nowrap", "text-overflow": "ellipsis"}).attr("title", sData);
                    }
                },
                {"title": "申请人", "data": "user_name", "name": "user_name", "sortable": true},
                {"title": "审批人", "data": "approver_name", "name": "approver_name", "sortable": true},
                {"title": "部门", "data": "custom_department.name", "name": "custom_department_id", "sortable": true},
                {"title": "资金归属", "data": "reim_department.name", "name": "reim_department_id", "sortable": true},
                {"title": "申请时间", "data": "send_time", "name": "send_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "审批时间", "data": "approve_time", "name": "approve_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "驳回时间", "data": "reject_time", "name": "reject_time", "sortable": true,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                    }
                },
                {"title": "申请金额", "data": "approved_cost", "name": "approved_cost", "class": "text-right",
                    "render": function (data, type, row, meta) {
                        return '￥' + data;
                    }
                },
                {"title": "操作", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                    "createdCell": function (nTd, sData, oData, iRow, iCol) {
                        var html = '<button class = "btn btn-sm btn-danger delete" title = "删除"><i class = "fa fa-trash-o"></i></button>';
                        $(nTd).html(html).css("padding", "6px");
                    }
                }
            ],
            "ajax": "/finance/reimburse/rejected",
            "scrollX": 1000,
            "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                    "t" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [
                {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "className": "btn-primary"},
                {"text": "<i class='fa fa-refresh fa-fw'></i>", "className": "btn-primary", "action": function () {
                        rTable.fnDraw();
                    }
                }
            ]
        });
    }

    /* Add event listener for opening and closing details
     * Note that the indicator for showing which row is open is not controlled by DataTables,
     * rather it is done here
     */
    $(document).on('click', '.reim_table tbody td .show_expense', function () {
        var id = $(this).parents(".reim_table").attr("id");
        var curTable;
        switch (id) {
            case "pending-table":
                curTable = oTable;
                break;
            case "history-table":
                curTable = hTable;
                break;
            case "reject-table":
                curTable = rTable;
        }
        var nTr = $(this).closest('tr')[0];
        var reim_id = $(this).attr('reim-id');
        if ($(this).hasClass("fa-plus-circle")) {
            $(".reim_table>tbody>tr.details").hide();
            $(".reim_table tbody td .show_expense").removeClass("fa-minus-circle").addClass("fa-plus-circle");
            $(this).removeClass("fa-plus-circle").addClass("fa-minus-circle");
        } else {
            $(this).removeClass("fa-minus-circle").addClass("fa-plus-circle");
        }
        if (curTable.fnIsOpen(nTr)) {
            /* This row is already open - close it */
            $(this).parents('tr').next().toggle();
        } else {
            fnFormatDetails(curTable, nTr, reim_id);
            /* Open this row */
            curTable.fnOpen(nTr, sOut, 'details');
            var details = $(this).parents('tr').next().find(".details");
            changeCost(details);
        }
    });
    $(document).on("change", ".expense_cost", function () {
        var details = $(this).parents(".details");
        changeCost(details);
    });

    $(document).on("click", ".agree", function () {
        var row = $(this).closest("tr");
        var reimId = row.find(".show_expense").attr("reim-id");
        var auditedCost = row.find(".person_cost").text();
        var expenses = new Array();
        var expensesList = row.next().find("input[name='agree[]']");
        expensesList.each(function () {
            if ($(this).prop("checked")) {
                var expenseId = $(this).val();
                var expenseCost = $(this).closest("tr").find(".expense_cost").val();
                expenses.push({'id': expenseId, 'audited_cost': expenseCost});
            }
        });
        if (expenses.length === 0 && expensesList.length === 0) {
            expenses = "all";
        }
        var data = {
            reim_id: reimId,
            expenses: expenses,
            audited_cost: auditedCost
        };
        var url = "/finance/reimburse/agree";
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (msg) {
                if (msg === "success") {
                    oTable.fnDraw();
                } else {
                    document.write(msg);
                }
            }
        });
    });

    $(document).on("change", ".person_cost", function () {
        var value = parseFloat($(this).text()).toFixed(2);
        $(this).text(value);
        var origin = $(this).parents("td").prev().text();
        if (value !== origin.substring(1)) {
            $(this).css("color", "red");
        } else {
            $(this).css("color", "#555");
        }
    });

    $(document).on("click", ".reject", function () {
        var url = "/finance/reimburse/reject";
        var row = $(this).closest("tr");
        var reimId = row.find(".show_expense").attr("reim-id");
        var remarks = prompt("请输入驳回原因：");
        if (remarks !== null) {
            $.ajax({
                type: "POST",
                url: url,
                data: {"reim_id": reimId, "remarks": remarks},
                success: function (msg) {
                    if (msg === "success") {
                        oTable.fnDraw();
                    }
                }
            });
        }
    });

    $(document).on("click", ".delete", function () {
        var url = "/finance/reimburse/delete";
        var row = $(this).closest("tr");
        var reimId = row.find(".show_expense").attr("reim-id");
        var check = confirm("确认删除？");
        if (check) {
            $.ajax({
                type: "POST",
                url: url,
                data: {"reim_id": reimId},
                success: function (msg) {
                    if (msg === "success") {
                        rTable.fnDraw();
                    }
                }
            });
        }
    });

    $('.nav-tabs li a').on("click", function () {
        var link = $(this).attr("href");
        switch (link) {
            case "#pending":
                oTable.fnDraw();
                break;
            case "#history":
                hTable.fnDraw();
                break;
            case "#reject":
                rTable.fnDraw();
                break;
        }
    });
});
function icheckClick(tag) {
    var self = $(tag);
    var input = self.parents("tr").eq(0).find(".expense_cost");
    if (self.parent().hasClass("checked")) {
        input.val("0.00").change();
    } else {
        autoCheck(input);
    }
}

function autoCheck(input) {
    var base = input.parent().parent().prev().text();
    var result = base.substring(1);
    input.val(result).change();
}

function changeCost(details) {
    var value = 0;
    details.find(".expense_cost").each(function () {
        value += parseFloat($(this).val());
    });
    details.parent().prev().find(".person_cost").text(value.toFixed(2)).change();
}

function initComplete(data) {
    $('.reportrange').daterangepicker(
            {
                startDate: moment().subtract(30, 'days').startOf('day'),
                endDate: moment().endOf('day'),
                maxDate: moment().endOf('day'), //最大时间
                showDropdowns: true,
                ranges: {
                    //'最近1小时': [moment().subtract('hours',1), moment()],
                    '今日': [moment().startOf('day'), moment()],
                    '昨日': [moment().subtract(1, 'days'), moment()],
                    '最近7日': [moment().subtract(6, 'days'), moment()],
                    '最近30日': [moment().subtract(29, 'days'), moment()]
                }
            }
    );

    //选择时间后触发重新加载的方法
    $(".reportrange").on('apply.daterangepicker', function (useless, info) {
        auditStartTime = Date.parse(info.startDate) / 1000;
        auditEndTime = Date.parse(info.endDate) / 1000;
        hTable.fnDraw();
    });
}

