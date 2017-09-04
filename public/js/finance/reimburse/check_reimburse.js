var oTable, hTable, rTable;
var sOut;
$(function () {

    createDataTable();//已审核报销单

    $('.nav-tabs li a').on("click", function () {
        var link = $(this).attr("href");
        switch (link) {
            case "#personal"://个人已完成报销单
                oTable.draw();
                break;
            case "#history"://已审核报销单
                hTable.draw();
                break;
            case "#reject":
                rTable.draw();
                break;
        }
    });
});

/**
 * 初始已审核报销单列表
 * Initialse DataTables, with no sorting on the 'details' column
 */
function createDataTable() {
    hTable = $('#history-table').oaTable({
        "columns": [
            {
                "title": "详情", "data": "id", "name": "id", "class": "text-center", "sortable": false,
                "render": function (data, type, row, meta) {
                    return '<i class="fa fa-plus-circle show_expense" style="font-size: 20px; cursor:pointer;" onclick="show_expenses(' + data + ',this)"></i>';
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
                "title": "审核人",
                "data": "accountant_name",
                "name": "accountant_name",
                "class": "text-center",
                "sortable": true
            },
            {
                "title": "总金额",
                "data": "audited_cost",
                "name": "audited_cost",
                "class": "text-center",
                "width": "100px",
                "sortable": true,
                "render": function (data, type, row, meta) {
                    return '￥' + data;
                }
            },
            {
                title: "操作",
                data: 'id',
                name: 'id',
                class: 'text-center',
                sortable: false,
                createdCell: function (nTd, sData, oData, iRow, iCol) {
                    var html = '<a target="_blank" href="/finance/check_reimburse/print/' + sData + '" class="btn btn-sm btn-default print" title="打印"><i class="fa fa-print"></i></a>';//打印
                    if (reply_button) {//撤回权限
                        html += ' <button class = "btn btn-sm btn-danger" title = "撤回" onclick="restore(' + sData + ')"><i class = "fa fa-reply"></i></button>';
                    }
                    $(nTd).html(html).css("padding", "6px");
                }
            }
        ],
        "ajax": {"url": "/finance/check_reimburse/audited"},
        "scrollX": 1000,
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B<'#mytoolbox'>><'col-sm-3'f>r>" +
        "t" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [
            'export:/finance/reimburse/excel?type=all',//导出
        ],
        filter: $("#check_reimburse_search"),//搜索
        'order': [['8', 'desc']],
        'scrollY':586
    });
}


/**
 * 点击详情
 * @param id
 */
function show_expenses(reim_id, _self) {
    var curTable = hTable;
    var nTr = $(_self).closest('tr')[0];
    if ($(_self).hasClass("fa-plus-circle")) {
        $(".reim_table>tbody>tr.details").hide();
        $(".reim_table tbody td .show_expense").removeClass("fa-minus-circle").addClass("fa-plus-circle");
        $(_self).removeClass("fa-plus-circle").addClass("fa-minus-circle");
    } else {
        $(_self).removeClass("fa-minus-circle").addClass("fa-plus-circle");
    }
    if (curTable.row(nTr).child.isShown()) {
        /* This row is already open - close it */
        $(_self).parents('tr').next().toggle();
    } else {
        getFnFormatDetails(reim_id);//获取消费明细详情
        /* Open this row */
        curTable.row(nTr).child(sOut, 'details').show();
    }
}

/**
 * 获取消费明细详情
 * @param reim_id
 */
function getFnFormatDetails(reim_id) {
    var url = '/finance/check_reimburse/expenses';
    $.ajax({
        type: 'post',
        url: url,
        async: false,
        data: {reim_id: reim_id},
        dataType: 'json',
        success: function (msg) {
            getExpensesHtml(msg);
        }
    });
}

function getExpensesHtml(msg) {
    var payee_city = msg.payee_city? ('-'+msg.payee_city):'';
    sOut = '<p title="' + msg.description + '"> 描述：' + msg.description + '</p>';
    sOut += '<p title="' + msg.remark + '" style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;max-width:800px;font-weight:700;"> 备注：' + msg.remark + '</p>';
    sOut += '<p>' +
        '户名：<span style="margin-right:30px;">' + msg.payee_name + '</span>' +
        '卡号：<span style="margin-right:30px;">' + msg.payee_bank_account + '</span>' +
        '银行：<span>' + msg.payee_bank_other + '</span>' +
        '</p>';
    sOut += '<p>' +
        '手机：<span style="margin-right:30px;">' + msg.payee_phone + '</span>' +
        '开户省、市：<span style="margin-right:30px;">' + msg.payee_province + payee_city + '</span>';
    if (msg.payee_bank_other != '中国农业银行') {
        sOut += '开户网点：<span>' + msg.payee_bank_dot + '</span>';
    }
    sOut += '</p>';
    sOut += getExpensesTableHtml(msg);
}

function getExpensesTableHtml(msg) {
    var str = '<table class="col-lg-12" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    str += getExpensesTableTheadHtml();//获取table 的表头
    str += getExpensesTableTbodyHtml(msg);//获取内容
    str += '</table>';
    return str;
}

//获取详情table的表头
function getExpensesTableTheadHtml(table) {
    var str = '<tr style="background:#566986;">';
    str += '<th class="col-lg-1"></th>' +
        '<th class="text-center col-lg-3">描述</th>' +
        '<th class="text-center">消费时间</th>' +
        '<th class="text-center">消费类型</th>' +
        '<th class="text-center">&nbsp;金额</th>' +
        '<th class="text-center">发票详情</th>';
    str += '</tr>';
    return str;
}


//获取详情table的内容
function getExpensesTableTbodyHtml(msg) {
    var str = '';
    for (var i in msg.expenses) {
        var expense = msg.expenses[i];
        str += '<tr>';
        str += getExpensesTableTbodyTrTdCheckboxHtml(expense);//选择框
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
        str += getExpensesTableTbodyTrTdMoney(expense);
        /* 总金额 End */
        str += '<td class="text-center">';
        /* 发票按钮 Start */
        str += getExpensesTableTbodyTrTdBills(expense);
        /* 发票按钮 End */
        str += '</td>';
        str += '</tr>';
    }
    return str;
}

//获取详情table tr td下CheckBox 的html
function getExpensesTableTbodyTrTdCheckboxHtml(expense) {
    var checked = '';
    if (expense.is_audited) {
        checked = 'checked';
    }
    return '<td class="text-center"><div class="flat-green single-row"><div class="radio "><input type="checkbox" ' + checked + ' disabled></div></div></td>';
}

//获取详情的table tbody tr td 金额html
function getExpensesTableTbodyTrTdMoney(expense) {
    var str = '';
    var audited_cost = expense.audited_cost ? expense.audited_cost : 0;
    if (expense.audited_cost != expense.send_cost) {
        str = '<td class="text-center" style="color:red" title="原金额：￥' + expense.send_cost + '">￥' + audited_cost;
    } else {
        str = '<td class="text-center">￥' + audited_cost;
    }
    str += '</td>';
    return str;
}


//获取详情table tbody tr td 发票详情按钮
function getExpensesTableTbodyTrTdBills(expense) {
    var str = '<a class="btn btn-danger disabled" >0</a>';//无发票
    if (expense.bills && expense.bills.length > 0) {//有发票
        var pic_path = '';
        for (var i in expense.bills) {
            pic_path += expense.bills[i].pic_path + ',';
        }
        var pic_path_str = pic_path.substring(0, pic_path.length - 1);// 全部发票路径字符串
        cost = expense.audited_cost ? expense.audited_cost : 0;
        str = '<a class="btn btn-success bills"  href="javascript:expensesBillsClick(\'' + pic_path_str + '\',\'' + expense.description + '\',\'' + cost + '\')" bills="' + pic_path_str + '" description="' + expense.description + '" cost="' + cost + '">';
        str += '' + expense.bills.length + '</a>';
    }
    return str;
}


/**
 * 点击消费明细的发票按钮
 * @param pic_path_str
 * @param description
 * @param cost
 */
function expensesBillsClick(pic_path_str, description, cost) {
    bills = pic_path_str.split(',');
    var html = '<div class="panel panel-default">' +
        '<div class="panel-heading">' +
        '<h3 class="panel-title">金额<span class="pull-right">￥' + cost + '</span></h3>' +
        '</div>' +
        '<div class="panel-footer">' + description + '</div>' +
        '</div>' +
        '<ul class="row" id="bill_pic">';
    for (var i in bills) {
        var img = getExpensesTypeImgPath() + bills[i];
        html += '<li class="col-lg-5" style="max-height: 200px;"><img src="' + img + '" alt="' + img + '" title="明细发票" /></li>';
    }
    html += '</ul>';
    if ($("#gallery").children().length === 0) {
        $("#gallery").parents(".panel").find(".tools .fa").click();
    }
    $("#gallery").html(html);
    var viewer = new Viewer($("#gallery")[0], {"title": false});
}


/**
 * 撤回（操作）
 * @param id
 */
function restore(id) {
    if (confirm('确认撤回')) {
        var url = '/finance/check_reimburse/restore';
        $.post(url, {reim_id: id}, function (msg) {
            if (msg === 'success') {
                hTable.draw();
            } else if (msg === 'error') {
                alert('撤回失败！请重新刷新后再试');
            }
        }, 'text');
    }
}

