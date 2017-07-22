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
    var url = ATTENDANCE.get_staff_list + '?staff_sn=' + id;
    var str = '';

    $.ajax({
        type: "get",
        // url: url,
        url: url,
        dataType: 'jsonp',
        success: function (msg) {
            oaWaiting.hide();
            if (msg[0]) {
                str += '<div class="col-sm-4 panel" style="padding-top:10px">';
                str += '<div class="clearfix">';
                str += '<div class="col-lg-12">';
                str += '<label class="control-label col-lg-12"><strong>今日合照：</strong></label>';
                str += '<p class="form-control-static">'
                if (msg[0]['attachment']) {
                    str += '<img src="http://staff.cc' + msg[0]['attachment'] + '" />'
                }
                str += '</p>'
                str += '</div>'
                str += '</div>'
                str += '</div>';

                str += '<div class="col-sm-4 panel" style="padding-top:10px">';

                msg.forEach(function (val) {

                    str += '<div class="clearfix">';
                    str += '<label class="control-label col-lg-4"><strong>员工姓名：</strong></label>';
                    str += '<div class="col-lg-6">';
                    str += '<p class="form-control-static">'
                    str += val.staff_name;
                    str += '</p>'
                    str += '</div>'
                    str += '</div>'

                    str += '<div class="clearfix">';
                    str += '<label class="control-label col-lg-4"><strong>员工业绩：</strong></label>';
                    str += '<div class="col-lg-6">';
                    str += '<p class="form-control-static">'
                    str += val.achievement ? val.achievement : 0;
                    str += '</p>'
                    str += '</div>'
                    str += '</div>'

                    str += '<div class="clearfix">';
                    str += '<label class="control-label col-lg-4"><strong>提交时间：</strong></label>';
                    str += '<div class="col-lg-6">';
                    str += '<p class="form-control-static">'
                    str += val.submit_time;
                    str += '</p>'
                    str += '</div>'
                    str += '</div>'
                    str += '<div>'

                    str += '<div class="clearfix">';
                    str += '<label class="control-label col-lg-4"><strong>上班时间：</strong></label>';
                    str += '<div class="col-lg-6">';
                    str += '<p class="form-control-static">'
                    str += val.sign_time ? val.sign_time : '';
                    str += '</p>'
                    str += '</div>'
                    str += '</div>'
                    str += '<div>'

                    str += '<div class="clearfix">';
                    str += '<label class="control-label col-lg-4"><strong>下班时间：</strong></label>';
                    str += '<div class="col-lg-6">';
                    str += '<p class="form-control-static">'
                    str += val.down_time ? val.down_time : '';
                    str += '</p>'
                    str += '</div>'
                    str += '</div>'
                    str += '<div style="margin-bottom:10px"></div>'
                })
            } else {
                str += '';
                console.log(111)
            }

            str += '</div>';

            $("#board-right").html(str);
        },
        error: function (err) {
            console.log(err);
        }
    });
}