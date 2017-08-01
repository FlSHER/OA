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
                        <input type="text" class="form-control" name="realname.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">电话号码</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="mobile.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">身份证号</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="info.id_card_number.like"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">所属品牌</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="brand_id">
                            <option value="">全部</option>
                            {!!$HRM->getOptions(new App\Models\Brand)!!}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">职位</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="position_id" >
                            <option value="">全部</option>
                            {!!$HRM->getOptions(new App\Models\Position,[],'level')!!}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="control-label col-sm-2">所属部门</label>
                    <div class="col-md-7 col-sm-8" id="department_filter">
                        <select class="form-control" name="" onmousedown="showTreeViewOptions(this)">
                            <option value="">全部</option>
                            {!!$HRM->getDepartmentOptionsById()!!}
                        </select>
                        <div class="ztree ztreeOptions" id="department_filter_option"></div>
                        <input type="hidden" name="department_id.in">
                    </div>
                    <div class="col-sm-2 form-control-static">
                        <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                            <input type="checkbox" checked id="department_children">
                            <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                        </label> <span style="cursor:default;">包含下级</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">店铺编号</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="shop">
                        <input class="form-control" name="shop_sn.is" oaSearchColumn="shop_sn" type="text" title="店铺编号"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">员工状态</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="status_id" >
                            <option value="">全部</option>
                            {!!$HRM->getOptions(new App\Models\HR\StaffStatus)!!}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">是否激活</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="is_active" >
                            <option value="">全部</option>
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">银行卡号</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="info.account_number.like"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">入职时间</label>
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
                    <label class="control-label col-lg-3 col-sm-2">转正时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="employed_at.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="employed_at.max" type="text" isDate/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">离职时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="left_at.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="left_at.max" type="text" isDate/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">生日</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="birthday.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="birthday.max" type="text" isDate/>
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
        $("#department_children").on('change', function () {
            if ($(this).is(':checked')) {
                $('#department_filter').find('select').removeAttr('name');
                $('#department_filter').find('input').attr('name', 'department_id.in');
            } else {
                $('#department_filter').find('select').attr('name', 'department_id');
                $('#department_filter').find('input').removeAttr('name');
            }
        });
        $("#filter form").on("reset", function () {
            $(this).find('input,select').val('').change();
        });
    };
</script>