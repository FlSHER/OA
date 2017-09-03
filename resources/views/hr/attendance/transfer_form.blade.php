<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-2">*调动员工</label>
        <div class="col-sm-4" oaSearch="staff">
            <div class="input-group">
                <input class="form-control" name="staff_name" oaSearchColumn="realname" type="text" title="调动员工" style="background-color:#fff;" readonly/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
            </div>
            <input class="form-control" name="staff_sn" oaSearchColumn="staff_sn" type="hidden"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">调离店铺</label>
        <div class="col-sm-4">
            <div class="input-group" oaSearch="shop">
                <input class="form-control" name="leaving_shop_sn" oaSearchColumn="shop_sn" type="text" title="调离店铺"/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
        <label class="control-label col-sm-2">*到达店铺</label>
        <div class="col-sm-4">
            <div class="input-group" oaSearch="shop">
                <input class="form-control" name="arriving_shop_sn" oaSearchColumn="shop_sn" type="text" title="到达店铺"/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">*到店职务</label>
        <div class="col-sm-4">
            <select class="form-control" name="arriving_shop_duty" title="到店职务" >
                <option value="">待定</option>
                <option>店长</option>
                <option>店助</option>
                <option>导购</option>
                <option>协助</option>
            </select>
        </div>
        <label class="control-label col-sm-2">*出发时间</label>
        <div class="col-sm-3">
            <input class="form-control" name="left_at" type="text" isDate title="出发时间"/>
        </div>
        <div class="col-sm-1"></div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2"></label>
        <div class="col-sm-8">
            <input name="tag" type="hidden" value="" locked="true">
            {!! $HRM->getCheckBox('tag[][id]','staff_transfer_tags') !!}
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">备注</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="remark" title="备注" style="resize: none" rows="5" placeholder="最大长度200字符"></textarea>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="submit" class="btn btn-success">确认</button>
</div>