var table, editForm;
var csrfToken = $("meta[name='_token']").attr("content");

$(function () {
    editForm = $("#editForm").oaForm();

    /* dataTables start  */
    table = $('#leave_table').oaTable({
        "columns": columns,
        "ajax": {
            url: '/hr/leave/list'
        },
        "scrollX": 746,
        "buttons": buttons
    });
});

function imports() {
    oaWaiting.show();
    // $("#addForm")[0].reset();
    oaWaiting.hide();
    $("#openAddByOne").click();
}

//编辑
function edit(id) {
    oaWaiting.show();
    editForm[0].fillData('/hr/leave/info', {id: id});
    oaWaiting.hide();
}

function del(id) {
    var _confirm = confirm("确认撤销？");
    if (_confirm) {
        oaWaiting.show();
        // var url = '/hr/shop/delete?_token=' + csrfToken;
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: HOLIDAY.cancel,
            data: data,
            async: false,
            dataType: 'json',
            success: function (msg) {
                console.log(msg);
                if (msg['status'] == 1) {
                    table.fnDraw();
                    oaWaiting.hide();
                } else if (msg['status'] === -1) {
                    oaWaiting.hide(function () {
                        alert(msg['message']);
                    });
                }
            }
        });
    }
}
