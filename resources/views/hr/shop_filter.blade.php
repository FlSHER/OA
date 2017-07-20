<style>
    #filter .ztree li span.button.switch.level0 {visibility:hidden; width:1px;}
    #filter .ztree li ul.level0 {padding:0; background:none;}
    #filter .ztree li span.node_name {font-size:12px;}
</style>
<div class="panel-body bg-warning" id="filter" style="display:none;box-shadow: inset 0 0px 10px 0 rgba(0,0,0,.075);padding-bottom:0;">
    <form class="form-horizontal" method="post">
        <div class="form-group form-group-sm">
            <div class="col-lg-2">
                <div class="row">
                    <label class="control-label col-md-4">店铺编号</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="shop_sn.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="row">
                    <label class="control-label col-md-4">店铺名称</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="name.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="row">
                    <label class="control-label col-md-4">所属品牌</label>
                    <div class="col-md-8">
                        <select class="form-control" name="brand_id">
                            <option value="">全部</option>
                            {!!$HRM->getOptions(new App\Models\Brand)!!}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-md-2">所属部门</label>
                    <div class="col-md-7" id="department_filter">
                        <select class="form-control" name="" onmousedown="showTreeViewOptions(this)">
                            <option value="">全部</option>
                            {!!$HRM->getDepartmentOptionsById()!!}
                        </select>
                        <div class="ztree ztreeOptions" id="department_filter_option"></div>
                        <input type="hidden" name="department_id.in">
                    </div>
                    <div class="col-md-3 form-control-static">
                        <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                            <input type="checkbox" checked id="department_children">
                            <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                        </label> <span style="cursor:default;">包含下级</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="row">
                    <label class="control-label col-md-4">店长姓名</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="manager_name.like"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-8">
                <div class="row">
                    <label class="control-label col-md-1">店铺地址</label>
                    <div class="col-md-6">
                        <div class="input-3level-group">
                            <select class="form-control" name="province_id" title="省">
                                <option value="">全部</option>
                                {!!$HRM->getDistrictOptions()!!}
                            </select>
                            <select class="form-control" name="city_id" title="市">
                                <option value="">全部</option>
                            </select>
                            <select class="form-control" name="county_id" title="区/县">
                                <option value="">全部</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="address.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 col-lg-offset-2">
                <button type="reset" class="btn btn-warning btn-sm">重置</button>
            </div>
            <div class="col-lg-1">
                <button type="submit" class="btn btn-default btn-sm">确认</button>
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