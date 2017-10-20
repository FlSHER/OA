var table;

$(function () {
    var oaFormOption = {
        oaDate: {
            minDate: '2017-10-19',
            maxDate: 'today'
        },
        oaTime: {
            enableSeconds: false
        },
        callback: {
            submitSuccess: oaFormSubmitSuccess,
            afterReset: oaFormAfterReset
        }
    };
    /* oaForm */
    $(".modal form").oaForm(oaFormOption);
    /* dataTables start   */
    table = $('#transfer').oaTable({
        columns: columns,
        filter: $("#filter"),
        ajax: {
            url: '/hr/working_schedule/list'
        },
        buttons: buttons,
        scrollX: 746
    });
});

function add() {
    oaWaiting.show();
    var form = $("#addForm");
    form.oaForm()[0].reset();
    oaWaiting.hide();
}

function edit(id) {
    oaWaiting.show();
    var form = $("#editForm");
    form.oaForm()[0].fillData('/hr/working_schedule/info', {'id': id});
    oaWaiting.hide();
}

function deleteByOne(id) {
    var _confirm = confirm("确认删除？");
    if (_confirm) {
        oaWaiting.show();
        var url = '/hr/working_schedule/delete';
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                if (msg['status'] === 1) {
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

function oaFormAfterReset(obj) {
    var formType = $(this).attr("id").replace("Form", "");
    $('#' + formType + 'ByOne').modal('show');
}

function oaFormSubmitSuccess(msg, obj) {
    table.draw(false);
    $(".close").click();
}