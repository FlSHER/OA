var table;
$(function () {
    var oaFormOption = {
        callback: {
            submitSuccess: oaFormSubmitSuccess
        }
    };
    /* oaForm */
    $(".modal form").oaForm(oaFormOption);
    /* dataTables start */
    table = $('#transfer').oaTable({
        columns: columns,
        ajax: {
            url: '/hr/transfer/list'
        },
        filter: $("#filter"),
        order: [[0, 'desc']],
        buttons: buttons
    });
    /* dataTables end */
    $("#import_input").on("change", importByExcel);
});

function importByExcel() {
    oaWaiting.show();
    var formdata = new FormData();
    var fileObj = $(this).get(0).files;
    var url = "/hr/transfer/import";
    if (fileObj) {
        formdata.append($(this).attr('name'), fileObj[0]);
        $.ajax({
            type: "POST",
            url: url,
            data: formdata,
            contentType: false,
            processData: false,
            success: function (msg) {
                if (msg['status'] === -1) {
                    alert(msg['message']);
                    $("#import_result").html('');
                    oaWaiting.hide();
                } else {
                    table.draw();
                    $("#import_result").html(msg);
                    oaWaiting.hide();
                }
            },
            error: function (err) {
                alert(err.responseText);
            }
        });
        $(this).val('');
    }
}

function exportByExcel(e, dt, node, config) {
    var dataCount = dt.ajax.json().recordsFiltered;
    if (dataCount === 0) {
        alert("无可用信息");
    } else if (confirm("确认以当前条件导出？")) {
        oaWaiting.show();
        var params = dt.ajax.params();
        delete params.length;
        var url = "/hr/transfer/export";
        $.ajax({
            type: "POST",
            url: url,
            data: params,
            dataType: 'json',
            success: function (msg) {
                if (msg['state'] === 1) {
                    var fileName = msg['file_name'];
                    window.location.href = '/storage/exports/' + fileName + '.xlsx';
                    oaWaiting.hide();
                }
            }
        });
    }
}

function add(id) {
    oaWaiting.show();
    var form = $("#addForm");
    form.oaForm()[0].reset();
    oaWaiting.hide();
}

function edit(id) {
    oaWaiting.show();
    var form = $("#editForm");
    form.oaForm()[0].fillData('/hr/transfer/info', {'id': id});
    oaWaiting.hide();
}

function oaFormSubmitSuccess(msg, obj) {
    table.draw(false);
    $(".close").click();
}

function cancel(id) {
    var _confirm = confirm("确认取消调动？");
    if (_confirm) {
        oaWaiting.show();
        var url = '/hr/transfer/cancel';
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                table.draw(false);
                oaWaiting.hide();
            }
        });
    }
}
