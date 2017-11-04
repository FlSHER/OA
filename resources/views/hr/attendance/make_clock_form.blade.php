<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-2">*日期</label>
        <div class="col-sm-4">
            <input class="form-control" name="date" type="text" isDate title="日期" id="date_input"
                   onchange="toggleWorkingSchedule()"/>
        </div>
        <label class="control-label col-sm-2">*员工</label>
        <div class="col-sm-4" oaSearch="staff">
            <div class="input-group">
                <input class="form-control" oaSearchColumn="realname" type="text" title="员工"
                       style="background-color:#fff;" readonly/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
            </div>
            <input class="form-control" name="staff_sn" id="staff_input" oaSearchColumn="staff_sn" type="hidden"
                   onchange="toggleWorkingSchedule()"/>
        </div>
    </div>
    <div class="clock_content" style="display:none;">
        <div id="clock_records_box">

        </div>
        <div class="form-group">
            <label class="control-label col-sm-2">*打卡类型</label>
            <div class="col-sm-4">
                <select class="form-control" name="combine_type" id="combine_type" title="打卡类型"
                        onchange="selectCombineType(this)">
                    <option value="11">上班</option>
                    <option value="12">下班</option>
                    <option value="32">请假离店</option>
                    <option value="31">请假返回</option>
                </select>
            </div>
        </div>
        <div class="form-group leave_request">
            <label class="control-label col-sm-2">*假条</label>
            <div class="col-sm-8">
                <select class="form-control" name="leave_request" title="假条" onchange="" id="leave_request_select">
                </select>
            </div>
        </div>
        <div class="form-group clock_info">
            <label class="control-label col-sm-2">*店铺</label>
            <div class="col-sm-4">
                <div class="input-group" oaSearch="shop">
                    <input class="form-control" name="shop_sn" oaSearchColumn="shop_sn" type="text" title="店铺"/>
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
                </div>
            </div>
            <label class="control-label col-sm-2">*打卡时间</label>
            <div class="col-sm-4">
                <input class="form-control" name="clock_at" type="text" isTime title="打卡时间"/>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="submit" class="btn btn-success">确认</button>
</div>

<script>
    function toggleWorkingSchedule() {
        if ($('#date_input').val() && $('#staff_input').val()) {
            $.ajax({
                type: 'POST',
                url: '/hr/attendance/get_clock_records',
                data: {
                    date: $('#date_input').val(),
                    staff_sn: $('#staff_input').val()
                },
                success: function (response) {
                    $('#clock_records_box').html(response);
                    $('.clock_content').show();
                },
                error: function (err) {
                    document.write(err.responseText);
                }
            });
            $.ajax({
                type: 'POST',
                url: '/hr/leave/person',
                data: {
                    staff_sn: $('#staff_input').val()
                },
                success: function (response) {
                    var leaveRequest;
                    var clockOut = false;
                    var clockIn = false;
                    for (var i in response) {
                        leaveRequest = response[i];
                        $('#leave_request_select').html();
                        $('#leave_request_select').append('<option value="' + leaveRequest.id + '">' + leaveRequest.start_at + ' 至 ' + leaveRequest.end_at + '</option>');
                        if (!leaveRequest.clock_out_at)
                            clockOut = true;
                        if (!leaveRequest.clock_in_at)
                            clockIn = true;
                    }
                    if (clockOut) {
                        $('#combine_type option[value=32]').show();
                    } else {
                        $('#combine_type option[value=32]').hide();
                    }
                    if (clockIn) {
                        $('#combine_type option[value=31]').show();
                    } else {
                        $('#combine_type option[value=31]').hide();
                    }
                },
                error: function (err) {
                    document.write(err.responseText);
                }
            });
        } else {
            $('.clock_content').hide();
        }
    }

    function selectCombineType(dom) {
        var value = dom.value;
        if (value == 31 || value == 32) {
            $('.leave_request').show();
//            $('.clock_info').hide();
        } else {
            $('.leave_request').hide();
//            $('.clock_info').show();
        }
    }
</script>