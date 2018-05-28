@inject('HRM',HRM)
<style>
    #filter .ztree li span.button.switch.level0 {
        visibility: hidden;
        width: 1px;
    }

    #filter .ztree li ul.level0 {
        padding: 0;
        background: none;
    }

    #filter .ztree li span.node_name {
        font-size: 12px;
    }
</style>
<div class="panel-body bg-warning" id="filter"
     style="display:none;box-shadow: inset 0 0px 10px 0 rgba(0,0,0,.075);padding-bottom:0;">
    <form class="form-horizontal" method="post">
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">店铺编号</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="shop">
                        <input class="form-control" name="shop_sn.is" oaSearchColumn="shop_sn" type="text"
                               title="店铺编号"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">员工编号</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input class="form-control" name="staff_sn.is" oaSearchColumn="staff_sn" type="text"
                               title="员工编号"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow>
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">员工姓名</label>
                    <div class="col-sm-8 input-group input-group-sm">
                        <input class="form-control" name="staff_name.like" type="text" title="员工姓名"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">当日职务</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="shop_duty_id">
                            <option value="">全部</option>
                            {!!$HRM->getOptions(\App\Models\HR\Attendance\ShopDuty::getQuery())!!}
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-2 col-lg-offset-9">
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