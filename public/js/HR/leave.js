var table;

$(function () {
    $(".modal form").oaForm({
        callback: {
            submitSuccess: oaFormSubmitSuccess
        }
    });

    /* dataTables start  */
    table = $('#leave_table').oaTable({
        columns: columns,
        ajax: {
            url: '/hr/leave/list'
        },
        order: [['0', 'desc']],
        scrollX: 746,
        filter: $("#filter"),
        buttons: buttons
    });
});

function edit(id) {
    oaWaiting.show();
    $("#editForm").oaForm()[0].fillData('/hr/leave/info', {id: id});
    oaWaiting.hide();
}

function cancel(id) {
    var _confirm = confirm("确认撤销？");
    if (_confirm) {
        oaWaiting.show();
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: '/hr/leave/cancel',
            data: data,
            dataType: 'json',
            success: function (msg) {
                if (msg['status'] == 1) {
                    table.draw();
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

function oaFormSubmitSuccess(msg, obj) {
    table.draw(false);
    $(".close").click();
}
