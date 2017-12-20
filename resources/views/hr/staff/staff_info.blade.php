@inject('authority','Authority')
<div class="col-md-4">
    <section class="panel">
        <header class="panel-heading custom-tab dark-tab">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#info" data-toggle="tab"><i class="fa fa-address-card"></i> 详细信息</a>
                </li>
                @if($authority->checkAuthority(118))
                    <li class="">
                        <a href="#info-log" data-toggle="tab"><i class="fa fa-history"></i> 变更日志</a>
                    </li>
                @endif
                @if($authority->checkAuthority(69))
                    <li class="">
                        <a href="#info-auth" data-toggle="tab"><i class="fa fa-key"></i>员工权限</a>
                    </li>
                @endif
                @if($authority->checkAuthority(120))
                    <li class="">
                        <a href="#info-remark" data-toggle="tab"><i class="fa fa-eyedropper"></i>员工评价</a>
                    </li>
                @endif
            </ul>
        </header>
        <div class="panel-body">
            <div class="tab-content" style="max-height:775px;overflow-x:hidden;overflow-y:auto;">
                <!-- 详细信息 start -->
                <div class="form-horizontal tab-pane active" id="info">
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>员工姓名：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">
                                    {{$staff->realname}}
                                    @if($staff->gender_id==1)
                                        <i class="fa fa-mars text-info" style="font-weight:700"></i>
                                    @elseif($staff->gender_id==2)
                                        <i class="fa fa-venus text-danger" style="font-weight:700"></i>
                                    @endif
                                </p>
                            </div>
                            <label class="control-label col-lg-3 bold"><strong>电话号码：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->mobile}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>身份证号：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->info->id_card_number}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>所属部门：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->department->full_name}}{{$staff->shop?"-{$staff->shop->name}({$staff->shop->shop_sn})":''}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>品牌-职位：</strong></label>
                            <div class="col-lg-9" style="white-space: nowrap;">
                                <p class="form-control-static">
                                    {{$staff->brand->name}} - {{$staff->position->name}}
                                    <span class="badge badge-important">{{$staff->position->level}}</span>
                                </p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>员工状态：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->status->name}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" <?php if (!$staff->info->account_active) echo 'style="color:#ccc;"'; ?>>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>银行账户：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->info->account_number}}
                                    &nbsp;&nbsp;{{$staff->info->account_name}}
                                    &nbsp;&nbsp;{{$staff->info->account_bank}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>入职时间：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->hired_at}}</p>
                            </div>
                            <label class="control-label col-lg-3"><strong>转正时间：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->employed_at}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>离职时间：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->left_at}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>生日：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->birthday}}</p>
                            </div>
                            <label class="control-label col-lg-3"><strong>民族：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->national->name}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>QQ号：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->info->qq_number}}</p>
                            </div>
                            <label class="control-label col-lg-3"><strong>微信号：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->wechat_number}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>邮箱：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->info->email}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>学历：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->info->education}}</p>
                            </div>
                            <label class="control-label col-lg-3"><strong>政治面貌：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->politics->name}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>婚姻状况：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->marital_status->name}}</p>
                            </div>
                            <label class="control-label col-lg-3"><strong>身高/体重：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">@if($staff->info->height) {{$staff->info->height}}
                                    cm @endif/@if($staff->info->weight) {{$staff->info->weight}}kg @endif</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>现居住地：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->info->living_province->name or ""}}{{$staff->info->living_city?'-'.$staff->info->living_city->name:""}}{{$staff->info->living_county?'-'.$staff->info->living_county->name:""}} {{$staff->info->living_address}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>户口所在地：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->info->household_province->name or ""}}{{$staff->info->household_city?'-'.$staff->info->household_city->name:""}}{{$staff->info->household_county?'-'.$staff->info->household_county->name:""}} {{$staff->info->household_address}}</p>
                            </div>
                        </div>

                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>籍贯：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->info->native_place}}</p>
                            </div>
                        </div>
                    </div>
                    <?php if ($authority->checkAuthority(59)) { ?>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>钉钉编码：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->dingding}}</p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>招聘人员：</strong></label>
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$staff->info->recruiter_name}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>紧急联系人：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{{$staff->info->concat_name}}<?php echo $staff->info->concat_type ? '（' . $staff->info->concat_type . '）' : '' ?> {{$staff->info->concat_tel}}</p>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>关系人：</strong></label>
                            <div class="col-lg-9 row">
                                @foreach($staff->relative as $relative)
                                    <p class="form-control-static col-sm-5">{{$relative->realname}}@if($relative->pivot['relative_type'])
                                            （{{$relativeType[$relative->pivot['relative_type']]->name}}） @endif</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="control-label col-lg-3"><strong>备注：</strong></label>
                            <div class="col-lg-9">
                                <p class="form-control-static"><?php echo nl2br($staff->info->remark); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 详细信息 end -->
                <!-- 变更日志 start -->
                @if($authority->checkAuthority(118))
                    <div class="tab-pane" id="info-log">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="col-xs-3">操作类型</th>
                                <th class="col-xs-3">执行日期</th>
                                <th class="col-xs-2">操作人</th>
                                <th class="col-xs-4">操作时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($staff->change_log as $log)
                                <tr>
                                    <td onclick="showLogDetail(this)" style="cursor:pointer">
                                        {{$log->operation_type}}
                                    </td>
                                    <td class="text-center">{{$log->operate_at}}</td>
                                    <td class="text-center">@if($log->admin_sn == 999999)
                                            开发者@else{{$log->admin->realname}}@endif</td>
                                    <td class="text-center">{{$log->created_at}}</td>
                                </tr>
                                @if(count($log->changes)>0 || !empty($log->operation_remark))
                                    <tr class="log_detail" style="display:none;">
                                        <td colspan="4" class="row bg-primary">
                                            @foreach($log->changes as $key => $change)
                                                @if(count($change)==count($change,1))
                                                    <div class="row m-bot15">
                                                        <div class="col-xs-3"><strong>{{$key}} :</strong></div>
                                                        <div class="col-xs-4"
                                                             style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                                                             title="{{$change[0]}}">{{$change[0]}}</div>
                                                        <div class="col-xs-1"><i class="fa fa-arrow-right"></i></div>
                                                        <div class="col-xs-4"
                                                             style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                                                             title="{{$change[1]}}">{{$change[1]}}</div>
                                                    </div>
                                                @else
                                                    <div class="row m-bot15">
                                                        <div class="col-xs-12"><strong>{{$key}} :</strong></div>
                                                    </div>
                                                    @foreach($change as $attribute => $value)
                                                        <div class="row m-bot15">
                                                            <div class="col-xs-2 col-xs-offset-1"><strong>{{$attribute}}
                                                                    :</strong></div>
                                                            <div class="col-xs-4"
                                                                 style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                                                                 title="{{$value[0]}}">{{$value[0]}}</div>
                                                            <div class="col-xs-1"><i class="fa fa-arrow-right"></i>
                                                            </div>
                                                            <div class="col-xs-4"
                                                                 style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                                                                 title="{{$value[1]}}">{{$value[1]}}</div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                            @if(!empty($log->operation_remark))
                                                <div class="row">
                                                    <div class="col-xs-3"><strong>操作备注 :</strong></div>
                                                    <div class="col-xs-9">{{$log->operation_remark}}</div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            <!-- 变更日志 end -->
                <!-- 员工权限 start -->
                @if($authority->checkAuthority(69))
                    <div class="tab-pane" id="info-auth">
                        <div class="modal-body">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-default"
                                        onclick="$.fn.zTree.getZTreeObj('authority_treeview').expandAll(true);">全部展开
                                </button>
                                <button class="btn btn-sm btn-default"
                                        onclick="$.fn.zTree.getZTreeObj('authority_treeview').expandAll();">全部收起
                                </button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="col-lg-10 ztree" id="authority_treeview"></div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                @endif
            <!-- 员工权限 end -->
                <!-- 员工评价 start -->
                @if($authority->checkAuthority(120))
                    <div class="form-horizontal tab-pane" id="info-remark">
                        @foreach($staff->appraise as $v)
                            <div class="form-group">
                                <div class="clearfix">
                                    <label class="control-label col-lg-2"><strong>时间：</strong></label>
                                    <div class="col-lg-4">
                                        <p class="form-control-static"
                                           title="{{$v->create_time}}">{{$v->create_time}} </p>
                                    </div>
                                    <label class="control-label col-lg-2 bold"><strong>评价人：</strong></label>
                                    <div class="col-lg-3">
                                        <p class="form-control-static" title="{{$v->entry_name}}">{{$v->entry_name}}</p>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label class="control-label col-lg-2"><strong>部门：</strong></label>
                                    <div class="col-lg-4">
                                        <p class="form-control-static"
                                           title="{{$v->department}}">{{$v->department}} </p>
                                    </div>
                                    <label class="control-label col-lg-2 bold"><strong>职位：</strong></label>
                                    <div class="col-lg-3">
                                        <p class="form-control-static" title="{{$v->position}}">{{$v->position}}</p>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label class="control-label col-lg-2"><strong>店铺：</strong></label>
                                    <div class="col-lg-9">
                                        <p class="form-control-static" title="{{$v->shop}}">{{$v->shop}}</p>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label class="control-label col-lg-2"><strong>评价：</strong></label>
                                    <div class="col-lg-9">
                                        <p class="form-control-static" title="{{$v->remark}}">{{$v->remark}}</p>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin:0;"/>
                        @endforeach
                    </div>
            @endif
            <!-- 员工评价 end -->
            </div>
        </div>
    </section>
    <script>
        $(function () {
            var authorityZTreeSetting = {
                async: {
                    url: "/system/authority/treeview?_token={{csrf_token()}}",
                    otherParam: {"staff_sn":<?php echo $staff->staff_sn ?>}
                },
                check: {
                    enable: true
                },
                view: {
                    showIcon: false
                },
                callback: {
                    beforeCheck: function () {
                        return false;
                    }
                }
            };

            $.fn.zTree.init($("#authority_treeview"), authorityZTreeSetting);
        });

        function showLogDetail(target) {
            var logDetail = $(target).parent().next(".log_detail");
            if (logDetail.is(":hidden")) {
                $(".log_detail").hide();
            }
            logDetail.toggle();
        }
    </script>
</div>
