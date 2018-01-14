/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 审批获取数据
 */
var token = $('meta[name="_token"]').attr('content');
var statisticTable = $("#statisticTable").oaTable({
    "columns": [
        {"data": "realname", "name": "realname", "title": "统计人", 'class': 'text-center'},
        {data: 'department_name', name: 'department_name', title: '统计人部门', 'class': "text-center"},
        {
            data: 'statisticDepartment.department_name',
            name: 'statisticDepartment.department_name',
            class: 'text-center',
            title: '统计部门',
            defaultContent: '',
            sortable: false,
            "createdCell": function (nTd, sData, oData, iRow, iCol) {
                var html = '';
                $.each(oData.statistic_department, function (k, v) {
                    html += v.department_name + ' | ';
                });
                html = html.substring(0, html.length - 2);
                var str = html;
                if (html.length > 20) {
                    str = html.substr(0, 19) + '...';
                }
                $(nTd).text(str);
                $(nTd).attr('title', html);
            }
        },
        {
            "data": "id",
            "title": "操作",
            "sortable": false,
            "class": "text-center",
            "render": function (data, type, row, meta) {
                return '<a href="javascript:edit_allotment(' + data + ')" title="编辑">编辑 </a> | <a href="javascript:del_allotment(' + data + ');" title="删除">删除</a>';
            }
        }
    ],
    "buttons": [
        {'text': '<i class="fa fa-fw fa-plus"></i>', 'action': addUser, 'titleAttr': "添加人员配置"}
    ],
    "ajax": {
        url: "/app/work_mission/statistic/list"
    }
});

//添加审批
function addUser() {
    $('#statisticTitle').text('添加人员配置');
    $('#statisticForm')[0].reset();
}

//删除
function del_allotment(id) {
    if (confirm('确认删除该条数据？')) {
        $.ajax({
            type: 'post',
            url: '/app/work_mission/statistic/delete',
            data: {id: id},
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (msg) {
                if (msg.message === 'success') {
                    statisticTable.draw();
                } else {
                    alert('删除失败');
                }
            }
        });
    }
}

//编辑
function edit_allotment(id) {
    $('#statisticTitle').text('编辑人员配置');
    $('#statisticForm').oaForm()[0].fillData('/app/work_mission/statistic/update', {id: id});
}

//表单配置数据
var allotmentFormOption = {
    callback: {
        submitSuccess: approverFormSubmitSuccess, //提交回调
//        afterReset: auditorFormAfterReset
    },
    //表单列表数据配置
    oaFormList: {
        min: 1,
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
    $('#statisticForm').oaForm(allotmentFormOption);
}

//提交成功处理
function approverFormSubmitSuccess(msg, obj) {
    if (msg === 'success') {
        $('#statisticForm')[0].reset();
        $('.close').click();
        statisticTable.draw();
    } else {
        alert('保存失败');
    }
}

//function auditorFormAfterReset(li, obj) {
////    li.oaSearch('auditor_realname');
//}

function oaFormListAfterAdd(li, obj) {
    li.oaSearch('staff');
}



