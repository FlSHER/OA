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
                    <label class="control-label col-lg-4 col-sm-2">状态</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="status">
                            <option value="">全部</option>
                            <option value="1">待审核</option>
                            <option value="2">已通过</option>
                            <option value="-1">已驳回</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">包含店员</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input class="form-control" oaSearchColumn="realname" type="text" title="包含店员"/>
                        <input name="details.staff_sn.is" oaSearchColumn="staff_sn" type="hidden" title="员工编号"/>
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
                    <label class="control-label col-lg-4 col-sm-2">店长</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input class="form-control" oaSearchColumn="realname" type="text" title="店长"/>
                        <input name="manager_sn.is" oaSearchColumn="staff_sn" type="hidden" title="店长编号"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow>
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-6">
                <div class="row">
                    <label class="control-label col-lg-2 col-sm-2">考勤日期</label>
                    <div class="col-lg-8 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="attendance_date.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="attendance_date.max" type="text" isDate/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-lg-offset-3">
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