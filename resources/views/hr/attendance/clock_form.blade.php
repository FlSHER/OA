<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-2">*员工</label>
        <div class="col-sm-4">
            <input name="staff[realname]" class="form-control" type="text" title="员工" disabled/>
        </div>
        <label class="control-label col-sm-2">*店铺</label>
        <div class="col-sm-4">
            <div class="input-group" oaSearch="shop">
                <input class="form-control" name="shop_sn" oaSearchColumn="shop_sn" type="text" title="店铺"/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">*打卡类型</label>
        <div class="col-sm-4">
            <select class="form-control" name="combine_type" id="combine_type" title="打卡类型"
                    onchange="selectCombineType(this)" disabled>
                <option value="11">上班</option>
                <option value="12">下班</option>
                <option value="22">调动出发</option>
                <option value="21">调动到达</option>
                <option value="32">请假离店</option>
                <option value="31">请假返回</option>
            </select>
        </div>
        <label class="control-label col-sm-2">*操作人</label>
        <div class="col-sm-4">
            <input name="operator[realname]" class="form-control" type="text" title="操作人" disabled/>
        </div>
    </div>
    <div class="form-group clock_info">
        <label class="control-label col-sm-2">*打卡时间</label>
        <div class="col-sm-4">
            <input class="form-control" name="clock_at" type="text" title="打卡时间" disabled/>
        </div>
        <label class="control-label col-sm-2">*计划时间</label>
        <div class="col-sm-4">
            <input class="form-control" name="punctual_time" type="text" title="计划打卡时间" disabled/>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="submit" class="btn btn-success">确认</button>
</div>