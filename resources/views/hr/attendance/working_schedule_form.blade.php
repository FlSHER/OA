<div class="modal-body">
    @if($type=='add')
        <div class="form-group">
            <label class="control-label col-sm-2">*员工</label>
            <div class="col-sm-4" oaSearch="staff">
                <div class="input-group">
                    <input class="form-control" name="staff_name" oaSearchColumn="realname" type="text" title="员工"
                           style="background-color:#fff;" readonly/>
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
                </div>
                <input class="form-control" name="staff_sn" oaSearchColumn="staff_sn" type="hidden"/>
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
    @else
        <div class="form-group">
            <label class="control-label col-sm-2">*员工</label>
            <div class="col-sm-4">
                <input class="form-control" name="staff_name" type="text" title="员工" readonly/>
            </div>
            <label class="control-label col-sm-2">*店铺</label>
            <div class="col-sm-4">
                <input class="form-control" name="shop_sn" type="text" title="店铺" readonly/>
            </div>
        </div>
    @endif
    <div class="form-group">
        <label class="control-label col-sm-2">*日期</label>
        @if($type=='add')
            <div class="col-sm-3">
                <input class="form-control" name="date" type="text" isDate value="{{date('Y-m-d')}}" locked="true"
                       title="日期"/>
            </div>
        @else
            <div class="col-sm-3">
                <input class="form-control" name="date" type="text" title="日期" readonly/>
            </div>
        @endif
        <div class="col-sm-1"></div>
        <label class="control-label col-sm-2">*当日职务</label>
        <div class="col-sm-4">
            <select class="form-control" name="shop_duty_id" title="当日职务">
                <option value="0">待定</option>
                {!!$HRM->getOptions(\App\Models\HR\Attendance\ShopDuty::getQuery())!!}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">上班时间</label>
        <div class="col-sm-3">
            <input class="form-control" name="clock_in" type="text" isTime title="上班时间"/>
        </div>
        <div class="col-sm-1"></div>
        <label class="control-label col-sm-2">下班时间</label>
        <div class="col-sm-3">
            <input class="form-control" name="clock_out" type="text" isTime title="下班时间"/>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="submit" class="btn btn-success">确认</button>
</div>