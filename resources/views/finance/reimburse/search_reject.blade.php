@inject('reim_department_name',"\App\Services\Finance\Reimburse\AuditService")
<div class="panel-body bg-warning" id="searchReject"
     style="display:none;box-shadow: inset 0 0px 10px 0 rgba(0,0,0,.075);padding-bottom:0;">
    <form class="form-horizontal" method="post">
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">订单编号</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="reim_sn.like"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">申请人</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input type="text" class="form-control" name="realname.like" oaSearchColumn="realname"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">审批人</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input type="text" class="form-control" name="approver_name.like"
                               oaSearchColumn="realname"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">驳回人</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input type="text" class="form-control" name="reject_name.like"
                               oaSearchColumn="realname"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">资金归属</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="reim_department.name">
                            <option value="">全部</option>
                            @foreach($reim_department_name->getReimDepartmentName() as $v)
                                <option value="{{$v->name}}">{{$v->name}}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">申请时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="send_time.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="send_time.max" type="text" isDate/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">审批时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="approve_time.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="approve_time.max" type="text" isDate/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">驳回时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="reject_time.min" type="text" isDate/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="reject_time.max" type="text" isDate/>
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