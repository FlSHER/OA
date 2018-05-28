/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 审批获取数据
 */
var token = $('meta[name="_token"]').attr('content');
var approverTable = $("#approverTable").oaTable({
    "columns": [
        {"data": "department.full_name", "title": "部门", defaultContent: ""},
        {"data": "reim_department.name", "title": "资金归属"},
        {
            "data": "approver1.realname", "title": "一级审批人", "defaultContent": "", orderable: false,
            "createdCell": function (nTd, sData, oData, iRow, iCol) {
                var html = '';
                $.each(oData.approver1, function (k, v) {
                    html += v.realname + ' | ';
                });
                html = html.substring(0, html.length - 2);
                $(nTd).text(html);
                $(nTd).attr('title', html);
            }
        },
        {
            "data": "approver2.realname", "title": "二级审批人", "defaultContent": "", orderable: false,
            "createdCell": function (nTd, sData, oData, iRow, iCol) {
                var html = '';
                $.each(oData.approver2, function (k, v) {
                    html += v.realname + ' | ';
                });
                html = html.substring(0, html.length - 2);
                $(nTd).text(html);
                $(nTd).attr('title', html);
            }
        },
        {
            "data": "approver3.realname", "title": "三级审批人", "defaultContent": "", orderable: false,
            "createdCell": function (nTd, sData, oData, iRow, iCol) {
                var html = '';
                $.each(oData.approver3, function (k, v) {
                    html += v.realname + ' | ';
                });
                html = html.substring(0, html.length - 2);
                $(nTd).text(html);
                $(nTd).attr('title', html);
            }
        },
        {
            "data": "id", "title": "操作", "sortable": false,
            "render": function (data, type, row, meta) {
                return '<a href="javascript:editApprover(' + data + ')" title="编辑">编辑 </a> | <a href="javascript:delApprover(' + data + ');" title="删除">删除</a>';
            }
        }
    ],
    "buttons": [
        {'text': '<i class="fa fa-fw fa-plus"></i>', 'action': addApprover, 'titleAttr': "添加审批"}
    ],
    "ajax": {
        url: "/app/reimburse/approver"
    }
});

//添加审批
function addApprover() {
    $('#apporverTitle').text('添加审批');
    $('#approverForm')[0].reset();
}

//删除
function delApprover(id) {
    if (confirm('确认删除？')) {
        $.ajax({
            type: 'post',
            url: '/app/reimburse/delApprover',
            data: {id: id},
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (msg) {
                approverTable.draw();
            }
        });
    }
}

//编辑
function editApprover(id) {
    $('#apporverTitle').text('编辑审批');
    $('#approverForm').oaForm()[0].fillData('/app/reimburse/editApprover', {id: id});
}

//表单配置数据
var approverFormOption = {
    callback: {
        submitSuccess: approverFormSubmitSuccess, //提交回调
//        afterReset: auditorFormAfterReset
    },
    //表单列表数据配置
    oaFormList: {
        min: 0,
        emptyInput: true,
        callback: {
            afterAdd: oaFormListAfterAdd
        }
    }
};

jQuery(function () {
    init();//初始化表单
});

//初始化表单
function init() {
    $('#approverForm').oaForm(approverFormOption);
}

//提交成功处理
function approverFormSubmitSuccess(msg, obj) {
    $('#approverForm')[0].reset();
    $('.close').click();
    approverTable.draw();
}

//function auditorFormAfterReset(li, obj) {
////    li.oaSearch('auditor_realname');
//}

function oaFormListAfterAdd(li, obj) {
    li.oaSearch('staff');
}



