<?php
$leaving = $staff->leaving;
?>
@inject('authority','Authority')
<div id="leavingByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="leavingForm" class="form-horizontal" method="post" action="{{source(route('hr.staff.leaving.submit'))}}">
                <input name="staff_sn" value="{{$staff->staff_sn}}" type="hidden" title="员工编号" locked="true"/>
                <div class="modal-header">
                    <button data-dismiss="modal" class="close" type="button">×</button>
                    <h4 class="modal-title">{{trans('fields.staff.operation_type.leaving')}}</h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-pills nav-justified">
                        @if(0)
                        @if($authority->checkAuthority(108))
                        <li><a href="#attendance" data-toggle="tab">本月考勤</a></li>
                        @endif
                        @if($authority->checkAuthority(109))
                        <li><a href="#goods" data-toggle="tab">物品回收</a></li>
                        @endif
                        @if($authority->checkAuthority(110))
                        <li><a href="#punishment" data-toggle="tab">费用扣减</a></li>
                        @endif
                        @if($authority->checkAuthority(111))
                        <li><a href="#inventory" data-toggle="tab">库存奖罚</a></li>
                        @endif
                        @if($authority->checkAuthority(112))
                        <li><a href="#software" data-toggle="tab">系统停用</a></li>
                        @endif
                        @if($authority->checkAuthority(113))
                        <li><a href="#finance" data-toggle="tab">财务清算</a></li>
                        @endif
                        @else
                        @if($authority->checkAuthority(114))
                        <li><a href="#check" data-toggle="tab">交接审核</a></li>
                        @endif
                        @endif
                    </ul>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        @if(0)
                        <!-- 考勤 start -->
                        @if($authority->checkAuthority(108))
                        <div class="tab-pane fade in" id="attendance">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">*出勤天数</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="attendance[出勤天数]" type="text" title="出勤天数" value="{{$leaving->attendance['出勤天数']}}">
                                </div>
                                <label class="control-label col-sm-2">*请假天数</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="attendance[请假天数]" type="text" title="请假天数" value="{{$leaving->attendance['请假天数']}}">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="attendance[备注]" title="备注" style="resize: none" rows="5">{{$leaving->attendance['备注']}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- 考勤 end -->
                        <!-- 物品回收 start -->
                        @if($authority->checkAuthority(109))
                        <div class="tab-pane fade in" id="goods">
                            <div class="form-group form-group-sm">
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="goods[钥匙]" type="hidden" value="未还" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="goods[钥匙]" type="checkbox" value="已还" <?php if ($leaving->goods['钥匙'] == '已还') echo 'checked'; ?> >
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">钥匙</span>
                                </div>
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="goods[工作证]" type="hidden" value="未还" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="goods[工作证]" type="checkbox" value="已还" <?php if ($leaving->goods['工作证'] == '已还') echo 'checked'; ?>>
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">工作证</span>
                                </div>

                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">办公用品</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="goods[办公用品]" title="办公用品" style="resize: none" rows="5">{{$leaving->goods['办公用品']}}</textarea>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="goods[备注]" title="备注" style="resize: none" rows="5">{{$leaving->goods['备注']}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- 物品回收 end -->
                        <!-- 费用扣减 start -->
                        @if($authority->checkAuthority(110))
                        <div class="tab-pane fade in" id="punishment">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">工装费用</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="punishment[工装费用]" type="text" title="工装费用" value="{{$leaving->punishment['工装费用']}}">
                                </div>
                                <label class="control-label col-sm-2">社保费用</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="punishment[社保费用]" type="text" title="社保费用" value="{{$leaving->punishment['社保费用']}}">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">大爱扣款</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="punishment[大爱扣款]" type="text" title="大爱扣款" value="{{$leaving->punishment['大爱扣款']}}">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="punishment[备注]" title="备注" style="resize: none" rows="5">{{$leaving->punishment['备注']}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- 费用扣减 end -->
                        <!-- 库存奖罚 start -->
                        @if($authority->checkAuthority(111))
                        <div class="tab-pane fade in" id="inventory">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">奖罚金额</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="inventory[奖罚金额]" type="text" title="奖罚金额" value="{{$leaving->inventory['奖罚金额']}}">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="inventory[备注]" title="备注" style="resize: none" rows="5">{{$leaving->inventory['备注']}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- 库存奖罚 end -->
                        <!-- 系统停用 start -->
                        @if($authority->checkAuthority(112))
                        <div class="tab-pane fade in" id="software">
                            <div class="form-group form-group-sm">
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="software[伯俊ERP]" type="hidden" value="未注销" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="software[伯俊ERP]" type="checkbox" value="已注销" <?php if ($leaving->software['伯俊ERP'] == '已注销') echo 'checked'; ?>>
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">伯俊ERP</span>
                                </div>
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="software[百胜ERP]" type="hidden" value="未注销" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="software[百胜ERP]" type="checkbox" value="已注销" <?php if ($leaving->software['百胜ERP'] == '已注销') echo 'checked'; ?>>
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">百胜ERP</span>
                                </div>
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="software[通达OA]" type="hidden" value="未注销" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="software[通达OA]" type="checkbox" value="已注销" <?php if ($leaving->software['通达OA'] == '已注销') echo 'checked'; ?>>
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">通达OA</span>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="software[钉钉]" type="hidden" value="未注销" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="software[钉钉]" type="checkbox" value="已注销" <?php if ($leaving->software['钉钉'] == '已注销') echo 'checked'; ?>>
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">钉钉</span>
                                </div>
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="software[数据决策系统]" type="hidden" value="未注销" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="software[数据决策系统]" type="checkbox" value="已注销" <?php if ($leaving->software['数据决策系统'] == '已注销') echo 'checked'; ?>>
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">数据决策系统</span>
                                </div>
                                <div class="col-sm-4 form-control-static text-center">
                                    <input name="software[金蝶]" type="hidden" value="未注销" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="software[金蝶]" type="checkbox" value="已注销" <?php if ($leaving->software['金蝶'] == '已注销') echo 'checked'; ?>>
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">金蝶</span>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="software[备注]" title="备注" style="resize: none" rows="5">{{$leaving->software['备注']}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- 系统停用 end -->
                        <!-- 财务清算 start -->
                        @if($authority->checkAuthority(113))
                        <div class="tab-pane fade in" id="finance">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">*应发工资</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="finance[应发工资]" type="text" title="应发工资" value="{{$leaving->finance['应发工资']}}">
                                </div>
                                <label class="control-label col-sm-2">*奖罚汇总</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="finance[奖罚汇总]" type="text" title="奖罚汇总" value="{{$leaving->finance['奖罚汇总']}}">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">*实发工资</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="finance[实发工资]" type="text" title="实发工资" value="{{$leaving->finance['实发工资']}}">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="finance[备注]" title="备注" style="resize: none" rows="5">{{$leaving->finance['备注']}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- 财务清算 end -->
                        @else
                        <!-- 交接审核 start -->
                        @if($authority->checkAuthority(114))
                        <div class="tab-pane fade in" id="check">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">离职时间</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="left_at" type="text" isDate title="离职时间" placeholder="实际离职时间（选填）">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">*办结时间</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="operate_at" type="text" isDate title="办结时间">
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label col-sm-2">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="operation_remark" title="备注" style="resize: none" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- 交接审核 end -->
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">通过</button>
                </div>
            </form>
        </div>
    </div>
    <script>$('#leavingByOne .nav>li>a:first').click();</script>
</div>