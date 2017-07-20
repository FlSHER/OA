//审核获取数据
var token = $('meta[name="_token"]').attr('content');
var auditorTable = $("#auditorTable").oaTable({
    "columns": [
        {"data": "name", "title": "资金归属"},
        {"data": "auditor.auditor_realname", "title": "审核人", "defaultContent": "",
            "createdCell": function (nTd, sData, oData, iRow, iCol) {
                var str = '';
                $.each(oData.auditor, function (k, v) {
                    str += v.auditor_realname + ' | ';
                });
                str = str.substring(0, str.length - 2);
                $(nTd).attr('title', str);
                $(nTd).text(str);
            }
        },
        {"data": "id", "title": "操作", "sortable": false,
            "render": function (data, type, row, meta) {
                return '<a href="javascript:editAuditor(' + data + ')" title="编辑">编辑 </a> | <a href="javascript:delAuditor(' + data + ');" title="删除">删除</a>';
            }
        }
    ],
    "buttons": [
        {'text': '<i class="fa fa-fw fa-plus"></i>', 'action': addAuditor, 'titleAttr': "添加审核"}
    ],
    "ajax": {
        url: "/app/reimburse/auditor"
    }
});

//添加
function addAuditor() {
    $('#auditorTitle').text("添加审核");
    $('#auditorForm')[0].reset();
}

//删除
function delAuditor(id) {
    if (confirm("确认删除？")) {
        $.ajax({
            type: 'post',
            url: '/app/reimburse/delAuditor',
            data: {id: id},
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (msg) {
                auditorTable.draw();
            }
        });
    }
}
//编辑
function editAuditor(id) {
    $('#auditorTitle').text("编辑审核");
    $('#auditorForm').oaForm()[0].fillData('/app/reimburse/editAuditor', {id: id});
}
;

/*------------------------------添加、编辑start---------------------------*/
//表单配置数据
var auditorFormOption = {
    callback: {
        submitSuccess: auditorFormSubmitSuccess, //提交回调
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
    $('#auditorForm').oaForm(auditorFormOption);
}

//提交成功处理
function auditorFormSubmitSuccess(msg, obj) {
    $('#auditorForm')[0].reset();
    $('.close').click();
    auditorTable.draw();
}
//function auditorFormAfterReset(li, obj) {
////    li.oaSearch('auditor_realname');
//}

function oaFormListAfterAdd(li, obj) {
    li.oaSearch('staff');
}
/*------------------------------添加、编辑end---------------------------*/

