var table;
$(function () {
    /* dataTables start */
    table = $('#main_table').dataTable({
        "columns": columns,
        "ajax": "/log/staff/list",
        "scrollX": 746,
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": buttons,
        "sorting": [[4, 'desc']],
        "searching": false
    });
    /* dataTables end */
});