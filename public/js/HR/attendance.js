var table;

$(function () {
    /* dataTables start   */
    table = $('#transfer').oaTable({
        "columns": columns,
        "ajax": {
            url: '/hr/attendance/list'
        },
        "scrollX": 746
    });

});


//attend_id
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