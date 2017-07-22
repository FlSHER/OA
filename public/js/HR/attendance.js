var table, zTreeSetting;
var csrfToken = $("meta[name='_token']").attr("content");

$(function () {
    $("#addDepartmentForm,#editDepartmentForm").validity(function () {
        // $(this).find("input[name='staff_sn']").require().maxLength("20");
        // $(this).find("input[name='out_shop_name']").require().maxLength("20"); 
        // $(this).find("input[name='go_shop_name']").require().maxLength("30");
    }, submitByAjax);
    /* dataTables start   */
    table = $('#transfer').dataTable({
        "columns": columns,
        // "ajax":"/hr/attendance/staffinfo?_token=" + csrfToken, 
        "ajax": {
            url: ATTENDANCE.getlist,
            dataType: 'JSONP'
        },
        "scrollX": 746,
        "order": [[0, "asc"]],
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": buttons
    });

})
function submitByAjax(form) {
    oaWaiting.show();
    var url = $(form).attr("action");
    var data = $(form).serialize();
    var type = $(form).attr('method');
    $.ajax({
        type: type,
        url: url,
        data: data,
        dataType: 'jsonp',
        success: function (msg) {
            table.fnDraw();
            $(".close").click();
            oaWaiting.hide();
            console.log(msg)
        },
        error: function (err) {
            console.log('error')
        }
    });
    return false;
}



function searchStaff(obj) {
    var name = $(obj).parent().prev().val();
    var url = "/hr/staff/search?_token=" + csrfToken;
    var data = {"target": {"staff_sn": "staff_sn", "realname": "staff_name"}};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'text',
        success: function (msg) {
            $("body").append(msg);
            $("#openSearchStaffResult").click();
        },
        error: function (err) {
            alert(err.responseText);
        }
    });
}

function searchShop(obj, str) {
    var name = $(obj).parent().prev().val();

    var url = "/hr/shop/search?_token=" + csrfToken;
    var data = {"target": {"shop_sn": str + "_shop_sn", "name": str + "_shop_name"}};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'text',
        success: function (msg) {
            $("body").append(msg);
            $("#openSearchShopResult").click();
        },
        error: function (err) {
            alert(err.responseText);
        }
    });
}




function setStaff(id) {
    var url = "/hr/shop/shop_modal_set?_token=" + csrfToken;
    var data = {"role_id": id};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'text',
        success: function (msg) {
            $("body").append(msg);
            $("#openSetStaff").click();
            $("#setStaff").bind("remove", function () {
                table.fnDraw();
            });
            oaWaiting.hide();
            // console.log(msg)
        },
        error: function (err) {
            document.write(err.responseText);
        }
    });
    // $("#openEditStaff").click();
}


//attend_id
function showPersonalInfo(id) {
    oaWaiting.show();
    console.log(id);
    // var url = '/hr/attendan/staffinfo?_token='+csrfToken+'&staff_sn='+id;
    // var url = '/hr/attendan/staffinfo?staff_sn='+id+'&_token'+csrfToken;
    var url = ATTENDANCE.get_staff_list + '?staff_sn=' + id;
    // console.log(url);
    // return false;
    // var data = {'staff_sn': staffSn};
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