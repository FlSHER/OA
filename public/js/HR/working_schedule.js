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
        scrollX: 746,
        callback: {
            loaded: function (self) {
                self.settings()[0].ajax.data.working_schedule_date = $('.working_schedule_date').text();
                $('.working_schedule_date').oaDate({
                    defaultDate: 'today',
                    onChange: function (selectedDates, dateStr, instance) {
                        $('.working_schedule_date').html(dateStr);
                        self.settings()[0].ajax.data.working_schedule_date = dateStr;
                        self.draw(false);
                    }
                });
            }
        }
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
    var attendanceDate = table.settings()[0].ajax.data.working_schedule_date;
    form.oaForm()[0].fillData('/hr/working_schedule/info', {'id': id, 'date': attendanceDate});
    oaWaiting.hide();
}

function deleteByOne(id) {
    var _confirm = confirm("确认删除？");
    if (_confirm) {
        oaWaiting.show();
        var url = '/hr/working_schedule/delete';
        var attendanceDate = table.settings()[0].ajax.data.working_schedule_date;
        var data = {'id': id, 'date': attendanceDate};
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