var table;

$(function () {
    var oaFormOption = {
        callback: {
            submitSuccess: oaFormSubmitSuccess
        }
    };
    $(".modal form").oaForm(oaFormOption);

    /* dataTables start  */
    table = $('#clock_table').oaTable({
        columns: columns,
        ajax: {
            url: '/hr/clock/list'
        },
        order: [['6', 'desc']],
        scrollX: 746,
        filter: $("#filter"),
        buttons: buttons,
        searching: false,
        callback: {
            loaded: function (self) {
                self.settings()[0].ajax.data.clock_month = $('.clock_month').text();
                $('.clock_month').oaDate({
                    defaultDate: 'today',
                    dateFormat: 'Y-m',
                    maxDate: 'today',
                    minDate: '2017-10',
                    onChange: function (selectedDates, dateStr, instance) {
                        $('.clock_month').html(dateStr);
                        self.settings()[0].ajax.data.clock_month = dateStr;
                        self.draw(false);
                    }
                });
            }
        }
    });
});

function abandon(id, month) {
    var _confirm = confirm("确认作废？");
    if (_confirm) {
        oaWaiting.show();
        var data = {'id': id, 'month': month};
        $.ajax({
            type: "POST",
            url: '/hr/clock/abandon',
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

function edit(id, month) {
    oaWaiting.show();
    var form = $("#editForm");
    form.oaForm()[0].fillData('/hr/clock/info', {'id': id, 'month': month});
    oaWaiting.hide();
}

function oaFormSubmitSuccess(msg, obj) {
    table.draw(false);
    $(".close").click();
}