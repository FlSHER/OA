var table;
$(function () {

    /* validity */
    $("#addPositionForm").validity(function () {
        $(this).find("input[name='name']").require().maxLength(10);
        $(this).find("input[name='level']").require().greaterThanOrEqualTo(0);
    }, submitByAjax);
    $("#editPositionForm").validity(function () {
        $(this).find("input[name='id']").require();
        $(this).find("input[name='name']").require().maxLength(10);
        $(this).find("input[name='level']").require().greaterThanOrEqualTo(0);
    }, submitByAjax);
    /* dataTables start */
    table = $('#position_list').dataTable({
        "columns": columns,
        "ajax": "/hr/position/list",
        "scrollX": 746,
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": buttons
    });
    /* dataTables end */
});

function addPosition() {
    oaWaiting.show();
    $("#addPositionForm")[0].reset();
    $("#addPositionForm .validity-tooltip").remove();
    oaWaiting.hide();
    $("#openAddPositionByOne").click();
}

function editPosition(detail) {
    oaWaiting.show();
    $("#editPositionForm")[0].reset();
    $("#editPositionForm .validity-tooltip").remove();
    detail['brand[]'] = detail['brand'].map(function (value) {
        return value.id;
    });
    $("#editPositionForm input,#editPositionForm select").each(function () {
        if ($(this).attr("type") == "checkbox") {
            var value = parseInt($(this).val());
            if ($.inArray(value, detail[$(this).attr("name")]) !== -1) {
                $(this).prop("checked", true);
            }
        } else {
            var value = detail[$(this).attr("name")];
            if (value !== undefined) {
                $(this).val(value);
            }
        }
    });
    oaWaiting.hide();
    $("#openEditPositionByOne").click();
}

function deletePosition(id) {
    var _confirm = confirm("确认删除？");
    if (_confirm) {
        oaWaiting.show();
        var url = '/hr/position/delete';
        var data = {'id': id};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            async: false,
            dataType: 'json',
            success: function (msg) {
                if (msg['status'] === 1) {
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

function submitByAjax(form) {
    oaWaiting.show();
    var url = $(form).attr("action");
    var data = $(form).serializeArray();
    var type = $(form).attr('method');
    $.ajax({
        type: type,
        url: url,
        data: data,
        dataType: 'json',
        success: function (msg) {
            if (msg['status'] === 1) {
                table.fnDraw();
                $(".close").click();
                oaWaiting.hide();
            }
        },
        error: function (err) {
            alert(err.responseText);
        }
    });
    return false;
}