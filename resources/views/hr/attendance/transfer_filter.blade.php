@inject('HRM',HRM)
<style>
    #filter .ztree li span.button.switch.level0 {visibility:hidden; width:1px;}
    #filter .ztree li ul.level0 {padding:0; background:none;}
    #filter .ztree li span.node_name {font-size:12px;}
</style>
<div class="panel-body bg-warning" id="filter" style="display:none;box-shadow: inset 0 0px 10px 0 rgba(0,0,0,.075);padding-bottom:0;">
    <form class="form-horizontal" method="post">
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">员工编号</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="staff_sn.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">员工姓名</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="staff_name.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">部门名称</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="staff_department.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">建单人</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input class="form-control" name="maker_name.like" oaSearchColumn="realname" type="text" title="建单人"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">调离店铺</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="shop">
                        <input class="form-control" name="leaving_shop_sn.like" oaSearchColumn="shop_sn" type="text" title="调离店铺"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">到达店铺</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="shop">
                        <input class="form-control" name="arriving_shop_sn.like" oaSearchColumn="shop_sn" type="text" title="到达店铺"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">到店职务</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="arriving_shop_duty.is">
                            <option value="">全部</option>
                            <option>店长</option>
                            <option>店助</option>
                            <option>导购</option>
                            <option>协助</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">标签</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="tag.id.is">
                            <option value="">全部</option>
                            {!!$HRM->getOptions(new App\Models\HR\Attendance\StaffTransferTag)!!}
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">出发时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="hired_at.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="hired_at.max" type="text" isDate/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">创建时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="created_at.min" type="text" isDateTime/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="created_at.max" type="text" isDateTime/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-lg-offset-2">
                <button type="reset" class="btn btn-warning btn-sm">重置</button>
                <button type="submit" class="btn btn-default btn-sm pull-right">确认</button>
            </div>
        </div>
    </form>
</div>
<script>
    window.onload = function () {
        $("#filter form").on("reset", function () {
            $(this).find('input,select').val('').change();
        });
    };
</script>