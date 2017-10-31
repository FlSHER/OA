<div class="modal-body">
	<div class="form-group">
		<label class="control-label col-sm-2">*日期</label>
		<div class="col-sm-4">
			<input class="form-control" name="date" type="text" isDate title="日期" id="date_input"
			       onchange="toggleWorkingSchedule"/>
		</div>
		<label class="control-label col-sm-2">*员工</label>
		<div class="col-sm-4" oaSearch="staff">
			<div class="input-group">
				<input class="form-control" oaSearchColumn="realname" type="text" title="员工" id="staff_input"
				       style="background-color:#fff;" readonly onchange="toggleWorkingSchedule"/>
				<span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
			</div>
			<input class="form-control" name="staff_sn" oaSearchColumn="staff_sn" type="hidden"
			/>
		</div>
	</div>
	<div class="form-group working_schedule">
		<label class="control-label col-sm-2">*打卡类型</label>
		<div class="col-sm-4">
			<select class="form-control" name="combine_type" title="打卡类型">
				<option value="11">上班</option>
				<option value="12">下班</option>
				<option value="32">请假离店</option>
				<option value="31">请假返回</option>
			</select>
		</div>
	</div>
	<div class="clock_content">
		<div class="form-group">
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
    function toggleWorkingSchedule(dom) {
//        if ($(dom).val()) {
//            $('.working_schedule').show();
//        } else {
//            $('.working_schedule').hide();
//        }
    }

    function toggleClockContent(dom) {
//        if ($(dom).val()) {
//            $('.clock_content').show();
//        } else {
//            $('.clock_content').hide();
//        }
    }
</script>