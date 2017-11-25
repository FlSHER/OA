var table;

$(function () {
    /* dataTables start   */
    table = $('#transfer').oaTable({
        columns: columns,
        buttons: buttons,
        filter: $("#filter"),
        ajax: {
            url: '/hr/attendance/list'
        },
        scrollX: 746
    });
});

/**
 * 加载详细信息
 * @param id
 */
function showPersonalInfo(id) {
    oaWaiting.show();
    var url = '/hr/attendance/detail';
    var data = {id: id};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'text',
        success: function (msg) {
            oaWaiting.hide();
            $("#board-right").html(msg);
        }
    });
}

/**
 * 通过审核
 * @param id
 */
function pass(id) {
    oaWaiting.show();
    $.ajax({
        type: 'POST',
        url: '/hr/attendance/pass',
        data: {id: id},
        success: function (response) {
            if (response.state == 1) {
                table.draw(false);
                showPersonalInfo(id);
            } else if (response.state == -1) {
                alert(response.message);
            }
        },
        error: function (err) {
            document.write(err.responseText);
        }
    })
}

/**
 * 驳回
 * @param id
 */
function reject(id) {
    oaWaiting.show();
    $.ajax({
        type: 'POST',
        url: '/hr/attendance/reject',
        data: {id: id},
        success: function (response) {
            if (response.state == 1) {
                table.draw(false);
                showPersonalInfo(id);
            } else if (response.state == -1) {
                oaWaiting.hide();
                alert(response.message);
            }
        },
        error: function (err) {
            document.write(err.responseText);
        }
    })
}

/**
 * 驳回
 * @param id
 */
function revert(id) {
    oaWaiting.show();
    $.ajax({
        type: 'POST',
        url: '/hr/attendance/revert',
        data: {id: id},
        success: function (response) {
            if (response.state == 1) {
                table.draw(false);
                showPersonalInfo(id);
            } else if (response.state == -1) {
                oaWaiting.hide();
                alert(response.message);
            }
        },
        error: function (err) {
            document.write(err.responseText);
        }
    })
}

/**
 * 刷新
 * @param id
 * @param attendanceHost
 */
function refresh(id) {
    oaWaiting.show();
    $.ajax({
        type: 'POST',
        url: '/hr/attendance/refresh',
        data: {id: id},
        success: function (response) {
            if (response.state == 1) {
                table.draw(false);
                showPersonalInfo(id);
            } else if (response.state == -1) {
                oaWaiting.hide();
                alert(response.message);
            }
        },
        error: function (err) {
            document.write(JSON.stringify(err).responseText);
        }
    })
}