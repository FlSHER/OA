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

function oaFormAfterReset(obj) {
    var formType = $(this).attr("id").replace("Form", "");
    $('#' + formType + 'ByOne').modal('show');
}

function oaFormSubmitSuccess(msg, obj) {
    table.draw(false);
    $(".close").click();
}