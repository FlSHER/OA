var oTable, hTable, rTable;
var auditStartTime, auditEndTime;
var sOut;//详情html代码
$(function () {
    createDataTable();
});

/*
 * Initialse DataTables, with no sorting on the 'details' column
 */
function createDataTable() {

    //待审核报销单列表数据
    oTable = $('#pending-table').oaTable({
        "columns": [
            {
                "title": "详情", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                "render": function (data, type, row, meta) {
                    return '<i class = "fa fa-plus-circle show_expense" style = "font-size:20px;cursor:pointer;" reim-id = "' + data + '"></i>';
                }
            },
            {
                "title": "订单编号", "data": "reim_sn", "name": "reim_sn", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css({"max-width": "120px", "overflow": "hidden"}).attr("title", sData);
                }
            },
            {"title": "申请人", "data": "realname", "name": "realname", "sortable": true},
            {"title": "审批人", "data": "approver_name", "name": "approver_name", "sortable": true},
            {
                "title": "部门", "data": "department_name", "name": "department_id", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    var html = (sData.length > 6) ? sData.substring(0, 6) + '..' : sData;
                    $(nTd).html(html).attr('title', sData);
                }
            },
            {"title": "资金归属", "data": "reim_department.name", "name": "reim_department_id", "sortable": true},
            {
                "title": "申请时间", "data": "send_time", "name": "send_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "审批时间", "data": "approve_time", "name": "approve_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    sData = sData ? sData : '';
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "审批金额",
                "data": "approved_cost",
                "name": "approved_cost",
                "class": "text-right",
                "sortable": false,
                "render": function (data, type, row, meta) {
                    return '￥' + (data ? data : row.send_cost);
                }
            },
            {
                "title": "审核金额",
                "data": "approved_cost",
                "name": "approved_cost",
                "class": "text-right",
                "sortable": false,
                "render": function (data, type, row, meta) {
                    return '￥<span class="person_cost">' + (data == null ? row.send_cost : data) + '</span>';
                }
            },
            {
                "title": "操作", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    var html = '<a target="_blank" href="/finance/reimburse/print/' + sData + '" class="btn btn-sm btn-default print" title="打印"><i class="fa fa-print"></i></a>' +
                        ' <button class = "btn btn-sm btn-danger reject" href="#myModals" data-toggle="modal" onclick="auditReject(' + sData + ')" title = "驳回"><i class = "fa fa-times"></i></button> <button class = "btn btn-sm btn-success agree" title = "通过"><i class = "fa fa-check"></i></button>';
                    $(nTd).html(html).css("padding", "6px").attr("row_num", iRow);
                }
            }
        ],
        "ajax": {
            "url": "/finance/reimburse/list"
        },
        "scrollX": 1000,
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
        "t" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        // "buttons": [
        //     {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "className": "btn-primary"},
        //     {
        //         "text": "<i class='fa fa-refresh fa-fw'></i>", "className": "btn-primary", "action": function () {
        //         oTable.fnDraw();
        //     }
        //     }
        // ]
        'order': [[6, 'desc']],
        'scrollY': 586
    });
    //已审核报销单列表数据
    hTable = $('#history-table').oaTable({
        "columns": [
            {
                "title": "详情", "data": "id", "name": "id", "class": "text-center",
                "render": function (data, type, row, meta) {
                    return '<i class = "fa fa-plus-circle show_expense" style = "font-size:20px;cursor:pointer;" reim-id = "' + data + '"></i>';
                }
            },
            {
                "title": "订单编号", "data": "reim_sn", "name": "reim_sn", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css({
                        "max-width": "120px",
                        "overflow": "hidden",
                        "white-space": "nowrap",
                        "text-overflow": "ellipsis"
                    }).attr("title", sData);
                }
            },
            {"title": "申请人", "data": "realname", "name": "realname", "sortable": true},
            {
                "title": "部门", "data": "department_name", "name": "department_name", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    var html = (sData.length > 6) ? sData.substring(0, 6) + '..' : sData;
                    $(nTd).html(html).attr('title', sData);
                }
            },
            {"title": "审批人", "data": "approver_name", "name": "approver_name", "sortable": true},
            {"title": "资金归属", "data": "reim_department.name", "name": "reim_department.name", "sortable": true},
            {
                "title": "申请时间", "data": "send_time", "name": "send_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "审批时间", "data": "approve_time", "name": "approve_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "审核时间", "data": "audit_time", "name": "audit_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "总金额",
                "data": "audited_cost",
                "name": "audited_cost",
                "class": "text-center",
                "width": "100px",
                "render": function (data, type, row, meta) {
                    return '￥' + data;
                }
            },
            {
                "title": "操作", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    var html = '<a target="_blank" href="/finance/reimburse/print/' + sData + '" class="btn btn-sm btn-default print" title="打印"><i class="fa fa-print"></i></a>';
                    // html +=' <button class = "btn btn-sm btn-danger" title = "撤回" onclick="reply('+sData+')"><i class = "fa fa-reply"></i></button>';
                    $(nTd).html(html).css("padding", "6px");
                }
            }
        ],
        "ajax": {"url": "/finance/reimburse/audited"},
        "scrollX": 1000,
        "buttons": [
            'export:/finance/reimburse/excel',//导出
        ],
        filter: $("#searchApproved"),//搜索
        'order': [[8, 'desc']],
        'scrollY': 586
    });
    //已驳回报销单列表数据
    rTable = $('#reject-table').oaTable({
        "columns": [
            {
                "title": "详情", "data": "id", "name": "id", "class": "text-center", 'sortable': false,
                "render": function (data, type, row, meta) {
                    return '<i class = "fa fa-plus-circle show_expense" style = "font-size:20px;cursor:pointer;" reim-id = "' + data + '"></i>';
                }
            },
            {
                "title": "订单编号", "data": "reim_sn", "name": "reim_sn", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css({
                        "max-width": "120px",
                        "overflow": "hidden",
                        "white-space": "nowrap",
                        "text-overflow": "ellipsis"
                    }).attr("title", sData);
                }
            },
            {"title": "申请人", "data": "realname", "name": "realname", "sortable": true},
            {"title": "审批人", "data": "approver_name", "name": "approver_name", "sortable": true},
            {
                "title": "部门", "data": "department_name", "name": "department_id", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    var html = (sData.length > 6) ? sData.substring(0, 6) + '..' : sData;
                    $(nTd).html(html).attr('title', sData);
                }
            },
            {"title": "资金归属", "data": "reim_department.name", "name": "reim_department_id", "sortable": true},
            {
                "title": "申请时间", "data": "send_time", "name": "send_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "审批时间", "data": "approve_time", "name": "approve_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "驳回时间", "data": "reject_time", "name": "reject_time", "sortable": true,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html(sData.substring(0, 10)).attr("title", sData);
                }
            },
            {
                "title": "审批金额", "data": "approved_cost", "name": "approved_cost", "class": "text-center",
                "render": function (data, type, row, meta) {
                    return '￥' + (data ? data : row.send_cost);
                }
            },
            {
                "title": "操作", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                "createdCell": function (nTd, sData, oData, iRow, iCol) {
                    var html = '<button class = "btn btn-sm btn-danger" onclick="deleteReject(' + sData + ')" title = "删除"><i class = "fa fa-trash-o"></i></button>';
                    $(nTd).html(html).css("padding", "6px");
                }
            }
        ],
        "ajax": {"url": "/finance/reimburse/rejected"},
        "scrollX": 1000,
        'scrollY': 586,
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
        "t" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        // "buttons": [
        //     {"extend": "colvis", "text": "<i class='fa fa-eye-slash fa-fw'></i>", "className": "btn-primary"},
        //     {
        //         "text": "<i class='fa fa-refresh fa-fw'></i>", "className": "btn-primary", "action": function () {
        //         rTable.draw();
        //     }
        //     }
        // ]
    });
}

/* Add event listener for opening and closing details
 * Note that the indicator for showing which row is open is not controlled by DataTables,
 * rather it is done here
 * 点击详情
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
    if (curTable.row(nTr).child.isShown()) {
        /* This row is already open - close it */
        $(this).parents('tr').next().toggle();
    } else {
        fnFormatDetails(curTable, nTr, reim_id);
        /* Open this row */
        curTable.row(nTr).child(sOut, 'details').show();
        var details = $(this).parents('tr').next().find(".details");
        changeCost(details);//金额处理
    }
});

/*----------------------------------------------发票start-------------------------------------*/
//点击发票详情
$(document).on("click", ".bills", function () {
    var bills = $(this).attr("bills");
    bills = bills.split(',');
    var description = $(this).attr("description");
    var cost = $(this).attr("cost");
    var html = '<div class="panel panel-default">' +
        '<div class="panel-heading">' +
        '<h3 class="panel-title">金额<span class="pull-right">￥' + cost + '</span></h3>' +
        '</div>' +
        '<div class="panel-footer">' + description + '</div>' +
        '</div>' +
        '<ul class="row" id="bill_pic">';
    for (var i in bills) {
        var img = getExpensesTypeImgPath() + bills[i];
        html += '<li class="col-lg-5" ><img src="' + img + '" alt="' + img + '" title="明细发票" /></li>';
    }
    html += '</ul>';
    if ($("#gallery").children().length === 0) {
        $("#gallery").parents(".panel").find(".tools .fa").click();
    }
    $("#gallery").html(html);
    new Viewer($("#gallery")[0], {"title": false});
});
/*----------------------------------------------发票end-------------------------------------*/

/*-------------------------------------通过处理start-------------------------------*/
$(document).on("click", ".agree", function () {
    if (confirm('确认通过？')) {
        var row = $(this).closest("tr");
        var reimId = row.find(".show_expense").attr("reim-id");
        // var auditedCost = row.find(".person_cost").text();
        var expenses = getExpensesIdAuditedCost(row);//获取消费明细数据

        var data = {
            reim_id: reimId,
            expenses: expenses
        };
        var url = "/finance/reimburse/agree";
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                if (msg.msg === 'warning') {
                    alert(msg.result);
                } else if (msg.msg === 'error') {
                    alert(msg.result);
                } else if (msg.msg === "success") {
                    oTable.draw();
                } else {
                    document.write(JSON.stringify(msg));
                }
            },
            error: function (msg) {
                if (msg.status === 422) {
                    var response = JSON.parse(msg.responseText);
                    for (var i in response) {
                        alert(response[i]);
                    }
                }
            }
        });
    }
});


//获取审核消费明细id和金额
function getExpensesIdAuditedCost(row) {
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
    return expenses;
}

/*-------------------------------------通过处理end-------------------------------*/


//删除 驳回单
function deleteReject(reim_id) {
    var url = '/finance/reimburse/delete';
    if (confirm('确认删除？')) {
        $.post(url, {id: reim_id}, function (msg) {
            if (msg.msg === 'error') {
                alert(msg.result);
            }
            else if (msg.msg === 'success') {
                rTable.draw();
            }
        }, 'json');
    }
}


$('.nav-tabs li a').on("click", function () {
    var link = $(this).attr("href");
    switch (link) {
        case "#pending":
            oTable.draw();
            break;
        case "#history":
            hTable.draw();
            break;
        case "#reject":
            rTable.draw();
            break;
    }
});


/*------------------------------------------详情start----------------------------------------------*/
/**
 * 获取详情数据
 * @param table
 * @param nTr
 * @param reim_id
 */
function fnFormatDetails(table, nTr, reim_id) {
    var url = '/finance/reimburse/expenses';
    $.ajax({
        type: 'post',
        url: url,
        async: false,
        data: {reim_id: reim_id},
        dataType: 'json',
        success: function (msg) {
            getExpensesHtml(table, msg);
        }
    });
}


function getExpensesHtml(table, msg) {
    sOut = '<p title="' + msg.description + '"> 描述：' + msg.description + '</p>';
    sOut += '<p title="' + msg.remark + '" style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;max-width:800px;font-weight:700;"> 备注：' + msg.remark + '</p>';
    sOut += getExpensesTableHtml(table, msg);
}


function getExpensesTableHtml(table, msg) {
    var str = '<table class="col-lg-12" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    str += getExpensesTableTheadHtml(table);//获取table 的表头
    str += getExpensesTableTbodyHtml(table, msg);//获取内容
    str += '</table>';
    return str;
}

//获取详情table的表头
function getExpensesTableTheadHtml(table) {
    var str = '<tr style="background:#566986;">';
    if (table == oTable) {
        str += '<th class="text-center">是否通过</th>';
    } else {
        str += '<th class="col-lg-1"></th>';
    }
    str += '<th class="text-center col-lg-3">描述</th>' +
        '<th class="text-center">消费时间</th>' +
        '<th class="text-center">消费类型</th>' +
        '<th class="text-center">&nbsp;金额</th>';
    if (table == oTable) {
        str += '<th class="text-center col-lg-1">审核金额</th>';
    }
    str += '<th class="text-center">发票详情</th>';
    str += '</tr>';
    return str;
}


//获取详情table的内容
function getExpensesTableTbodyHtml(table, msg) {
    var str = '';
    for (var i in msg.expenses) {
        var expense = msg.expenses[i];
        str += '<tr>';
        str += getExpensesTableTbodyTrTdCheckboxHtml(table, expense);//选择框
        str += '<td title="' + expense.description + '" class="text-center">';
        if (expense.description.length >= 26) {
            str += expense.description.substring(0, 24) + '...';
        } else {
            str += expense.description;
        }
        str += '</td>';
        str += '<td class="text-center">' + expense.date.substring(0, 10) + '</td>';
        str += '<td class="text-center" title="' + expense.type.name + '"><img width="30" src="' + getExpensesTypeImgPath() + expense.type.pic_path + '">';
        str += '</td>';
        /* 总金额 Start */
        str += getExpensesTableTbodyTrTdMoney(msg, expense);
        /* 总金额 End */

        /*待审核金额处理start*/
        if (table == oTable) {
            str += '<td class="text-center"><div class="input-group pull-right"><span class="input-group-addon">$</span><input class="form-control text-right expense_cost" required style="width:90px;" type="text"  value="' + expense.send_cost + '"></div></td>';
        }
        /*待审核金额处理end*/
        str += '<td class="text-center">';
        /* 发票按钮 Start */
        str += getExpensesTableTbodyTrTdBills(expense, msg);
        /* 发票按钮 End */
        str += '</td>';
        str += '</tr>';
    }
    return str;
}

//获取详情table tr td下CheckBox 的html
function getExpensesTableTbodyTrTdCheckboxHtml(table, expense) {
    var str = '';
    if (table == oTable) {
        str = '<td class="text-center"><div class="flat-green single-row"><div class="radio "><input type="checkbox" name="agree[]" checked value="' + expense.id + '"></div></div></td>';
    } else {
        str = '<td class="text-center"><div class="flat-green single-row"><div class="radio "><input type="checkbox"';
        if (expense.is_audited) {
            str += ' checked ';
        }
        str += 'disabled></div></div></td>';
    }
    return str;
}

//获取详情的table tbody tr td 金额html
function getExpensesTableTbodyTrTdMoney(msg, expense) {
    var str = '';
    if (msg.status_id == 4 && expense.audited_cost != expense.send_cost) {
        str = '<td class="text-center" style="color:red" title="原金额：￥' + expense.send_cost + '">￥';
        str += expense.audited_cost ? expense.audited_cost : 0;
    } else if (msg.status_id == 4) {
        str = '<td class="text-center">￥';
        str += expense.audited_cost ? expense.audited_cost : 0;
    } else {
        str = '<td class="text-center">￥';
        str += expense.send_cost;
    }
    str += '</td>';
    return str;
}


//获取详情table tbody tr td 发票详情按钮
function getExpensesTableTbodyTrTdBills(expense, msg) {
    var str = '<a class="btn btn-danger disabled" >0</a>';//无发票
    if (expense.bills && expense.bills.length > 0) {//有发票
        var pic_path = '';
        for (var i in expense.bills) {
            pic_path += expense.bills[i].pic_path + ',';
        }
        var pic_path_str = pic_path.substring(0, pic_path.length - 1);// 全部发票路径字符串

        var cost = expense.send_cost;//金额
        if (msg.status_id == 4) {
            cost = expense.audited_cost ? expense.audited_cost : 0;
            if (expense.audited_cost != expense.send_cost) {
                cost = ' (￥' + expense.audited_cost + ')';
            }
        }
        str = '<a class="btn btn-success bills" bills="' + pic_path_str + '" description="' + expense.description + '" cost="' + cost + '">';
        str += '' + expense.bills.length + '</a>';
    }
    return str;
}

/*------------------------------------------详情end----------------------------------------------*/


/*---------------------------驳回start-------------------------*/

//驳回 弹出层（待审核单）
function auditReject(id) {
    $('#remarks').val('');
    $('#confirm_reject').attr('reim_id', id);
}

//驳回提交处理 （待审核单）
function confirm_reject(_self) {
    var id = $(_self).attr('reim_id');
    var url = "/finance/reimburse/reject";
    var remarks = $.trim($('#remarks').val());
    if (remarks.length < 1) {
        alert('请输入驳回原因！内容不能为空');
        return false;
    }
    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: {"id": id, "remarks": remarks},
        success: function (msg) {
            if (msg.msg == 'error') {
                alert(msg.result);
            } else if (msg.msg === "success") {
                $('#myModals .close').click();
                oTable.draw();
            } else if (msg == 'warning') {
                alert(msg.result);
            }
        },
        error: function (msg) {
            if (msg.status === 422) {
                var response = JSON.parse(msg.responseText);
                for (var i in response) {
                    alert(response[i]);
                }
            }
        }
    });

}


//检测内容是否为空（控制确认按钮是否可点击）
function check_remarks(_self) {
    var val = $.trim($(_self).val());
    $("#confirm_reject").attr('disabled', true);
    if (val.length != 0) {
        $("#confirm_reject").attr('disabled', false);
    }
}


/*---------------------------驳回end-------------------------*/

/*---------------------待审核金额处理start----------------------*/


//点击详情过后处理金额数据
function changeCost(details) {
    details.find('table tbody').find('tr').not('tr:eq(0)').each(function () {
        checkboxClick($(this), details);
        saveMoney($(this), details)
    });
}

//点击checkbox
function checkboxClick(_this, details) {
    _this.find('input[type="checkbox"]').on('click', function () {
        moneySum(details);
    })
}

//审核金额修改
function saveMoney(_this, details) {
    _this.find('td .expense_cost').on('blur', function () {
        moneySum(details);
    })
}

//计算金额
function moneySum(details) {
    var value = 0;
    details.find(".expense_cost").each(function () {
        if ($(this).parents('.text-center').parent('tr').find('.radio input[type="checkbox"]').is(':checked')) {
            value += parseFloat($(this).val());
        }
    });
    var obj_audit_cost = details.parent().prev().find(".person_cost");
    obj_audit_cost.text(value.toFixed(2));
    audit_cost(obj_audit_cost);//金额变动处理
}

//审核金额变动处理
function audit_cost(obj_audit_cost) {
    var value = parseFloat(obj_audit_cost.text()).toFixed(2);
    obj_audit_cost.text(value);
    var origin = obj_audit_cost.parents("td").prev().text();
    if (value !== origin.substring(1)) {
        obj_audit_cost.css("color", "red");
    } else {
        obj_audit_cost.css("color", "#555");
    }
}

/*---------------------待审核金额处理end----------------------*/


/**
 *（撤回）已审核单
 * @param id
 */
// function reply(id){
//     if(confirm('确认撤回')){
//         var url = '/finance/reimburse/reply';
//         $.post(url,{id:id},function(msg){
//
//         },'text');
//     }
// }





