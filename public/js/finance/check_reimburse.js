var oTable, hTable, rTable;
var auditStartTime, auditEndTime;
$(function () {

    createDataTable();
    /*
     * Initialse DataTables, with no sorting on the 'details' column
     */
    function createDataTable() {
        hTable = $('#history-table').dataTable({
            "columns": [
                {"title": "详情", "data": "id", "name": "id", "class": "text-center", "sortable": false,
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
                {"title": "审核人", "data": "accountant_name", "name": "accountant_name", "sortable": true},
                {"title": "总金额", "data": "audited_cost", "name": "audited_cost", "class": "text-right", "width": "100px", "sortable": false,
                    "render": function (data, type, row, meta) {
                        return '￥' + data;
                    }
                }
            ],
            "ajax": "/finance/check_reimburse/audited",
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
                {"text": "<i class='fa fa-eye-slash fa-fw'></i>", "extend": "colvis", "className": "btn-primary"},
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
                            window.location.href = "/finance/reimburse/excel?type=all&params=" + params;
                        }
                    }
                }
            ],
            initComplete: initComplete
        });
    }

    /* Add event listener for opening and closing details
     * Note that the indicator for showing which row is open is not controlled by DataTables,
     * rather it is done here
     */
    $(document).on('click', '.reim_table tbody td .show_expense', function () {
        var id = $(this).parents(".reim_table").attr("id");
        var curTable = hTable;
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

