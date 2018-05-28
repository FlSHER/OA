<div id="{{$type}}ByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="{{$type}}Form" class="form-horizontal" method="post"
                  action="{{source(route('hr.staff.submit'))}}">
                <input name="operation_type" value="{{$type}}" type="hidden" title="操作类型" locked="true" />
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h4 class="modal-title">{{trans('fields.staff.operation_type.'.$type)}}</h4>
                </div>
                @if(in_array($type, ['edit', 'entry', 'reinstate']))
                    <ul class="nav nav-pills panel-heading">
                        <li class="active"><a href="#{{$type}}Form .basicInfo" data-toggle="tab">基础资料</a></li>
                        <li><a href="#{{$type}}Form .personalInfo1" data-toggle="tab">个人信息1</a></li>
                        <li><a href="#{{$type}}Form .personalInfo2" data-toggle="tab">个人信息2</a></li>
                        <li><a href="#{{$type}}Form .relativeInfo" data-toggle="tab">关系人</a></li>
                    </ul>
                @endif
                <div class="tab-content modal-body" style="padding-bottom:0px;">
                    @if(!in_array($type, ['entry']))
                        <input name="staff_sn" type="hidden" title="员工编号" />
                    @endif
                <!-- 基本信息 start -->
                    <div class="tab-pane fade in active basicInfo">
                        @if (in_array($type, ['edit', 'entry', 'reinstate']))
                            <div class="form-group">
                                <label class="control-label col-sm-2">*员工姓名</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="realname" type="text" title="员工姓名">
                                </div>
                                <div class="col-sm-1"></div>
                                <label class="control-label col-sm-2">*电话号码</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="mobile" type="text" title="电话号码">
                                </div>
                            </div>
                            @if($authority->checkAuthority(59))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">用户名</label>
                                    <div class="col-sm-6">
                                        <input class="form-control" name="username" type="text" title="用户名">
                                    </div>
                                    <div class="col-sm-4 form-control-static">
                                        <input name="is_active" type="hidden" value="0" locked="true">
                                        <label class="frame check frame-sm" unselectable="on"
                                               onselectstart="return false;">
                                            <input name="is_active" type="checkbox" value="1" checked>
                                            <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                        </label> <span style="cursor:default;">是否激活</span>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="control-label col-sm-2">*身份证号</label>
                                <div class="col-sm-5">
                                    <input class="form-control id_card_number" name="info[id_card_number]" type="text"
                                           title="身份证号">
                                </div>
                                <label class="control-label col-sm-2">*性别</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="gender_id" title="性别">
                                        {!! get_options('i_gender','name','id') !!}
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label class="control-label col-sm-2">*员工姓名</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="realname" type="text" title="员工姓名"
                                           placeholder="请输入2-10个字符" disabled>
                                </div>
                                @if(in_array($type, ['transfer']))
                                    <div class="col-sm-2 text-right">
                                        <button type="button" class="btn btn-danger" title="调离" onclick="transferOut()">
                                            <i class="fa fa-sign-out"></i></button>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if (in_array($type, ['transfer', 'entry', 'reinstate']))
                            <div class="form-group">
                                <label class="control-label col-sm-2">*所属部门</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="department_id" title="所属部门"
                                            onmousedown="showTreeViewOptions(this)">
                                        {!!$HRM->getDepartmentOptionsById()!!}
                                    </select>
                                    <div class="ztree ztreeOptions"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">*所属品牌</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="brand_id" title="所属品牌">
                                        {!!$HRM->getOptions(App\Models\Brand::visible())!!}
                                    </select>
                                </div>
                                <label class="control-label col-sm-2">店铺编号</label>
                                <div class="col-sm-3">
                                    <div class="input-group" oaSearch="shop">
                                        <input class="form-control" name="shop_sn" oaSearchColumn="shop_sn" type="text"
                                               title="店铺编号" />
                                        <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" oaSearchShow><i
                                                    class="fa fa-search"></i></button>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(!in_array($type, ['edit']))
                            <div class="form-group">
                                @if(in_array($type, ['entry', 'transfer', 'reinstate']))
                                    <label class="control-label col-sm-2">*职位</label>
                                    <div class="col-sm-3">
                                        <select class="form-control" name="position_id" title="职位"></select>
                                    </div>
                                    <div class="col-sm-1"></div>
                                @endif
                                <label class="control-label col-sm-2">*员工状态</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="status_id" title="员工状态"
                                            <?php if (!in_array($type, ['edit'])) { ?>locked="true"<?php } ?>>
                                        @if($type == 'leave')
                                            {!!$HRM->getOptions(new App\Models\HR\StaffStatus,[['id','<',0]])!!}
                                        @elseif($type == 'entry' || $type == 'reinstate')
                                            {!!$HRM->getOptions(new App\Models\HR\StaffStatus,[['id','=',1]])!!}
                                        @elseif($type == 'employ')
                                            {!!$HRM->getOptions(new App\Models\HR\StaffStatus,[['id','=',2]])!!}
                                        @elseif($type == 'transfer')
                                            {!!$HRM->getOptions(new App\Models\HR\StaffStatus,[['id','>',0]])!!}
                                        @else
                                            {!!$HRM->getOptions(new App\Models\HR\StaffStatus)!!}
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-1"></div>
                                @if(in_array($type, ['leave']))
                                    <div class="col-sm-4  form-control-static">
                                        <input name="skip_leaving" type="hidden" value="0" locked="true">
                                        <label class="frame check frame-sm" unselectable="on"
                                               onselectstart="return false;">
                                            <input name="skip_leaving" type="checkbox" value="1">
                                            <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                        </label> <span style="cursor:default;">跳过离职交接</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(in_array($type, ['edit', 'entry', 'reinstate']))
                            <div class="form-group">
                                <label class="control-label col-sm-2">员工备注</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="info[remark]" title="员工备注" style="resize: none"
                                              rows="5" placeholder="最大长度200字符"></textarea>
                                </div>
                            </div>
                        @endif
                        <div class="form-group <?php if ($type == 'edit') { ?>hidden<?php } ?>"
                             style="border-top: 1px dotted #e5e5e5;padding-top: 15px;">
                            <label class="control-label col-sm-2">*执行时间</label>
                            <div class="col-sm-3">
                                <input class="form-control" name="operate_at" type="text" isDate title="执行时间"
                                       <?php if ($type == 'edit') { ?>value="{{date('Y-m-d')}}"
                                       <?php } ?> locked="true" />
                            </div>
                            <div class="col-sm-1"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">操作说明</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="operation_remark" title="操作说明" style="resize: none"
                                          rows="5" placeholder="最大长度100字符"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- 基本信息 end -->
                    @if (in_array($type, ['edit', 'entry', 'reinstate']))
                    <!-- 个人信息1 start -->
                        <div class="tab-pane fade personalInfo1">
                            <div class="form-group">
                                <label class="control-label col-sm-2">银行卡号</label>
                                <div class="col-sm-5">
                                    <input class="form-control" name="info[account_number]" type="text" title="银行卡号" />
                                </div>
                                <label class="control-label col-sm-2">开户人</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="info[account_name]" type="text" title="开户人" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">开户行</label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="info[account_bank]" type="text" title="开户行"
                                           placeholder="如：中国农业银行成都荷花池支行" />
                                </div>
                                <div class="col-sm-4  form-control-static">
                                    <input name="info[account_active]" type="hidden" value="0" locked="true">
                                    <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                                        <input name="info[account_active]" type="checkbox" value="1">
                                        <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                                    </label> <span style="cursor:default;">使用工资卡</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-2">QQ号</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="info[qq_number]" type="text" title="QQ号">
                                </div>
                                <div class="col-sm-1"></div>
                                <label class="control-label col-sm-2">微信号</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="wechat_number" type="text" title="微信号">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">电子邮箱</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="info[email]" type="text" title="电子邮箱">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">现居住地</label>
                                <div class="col-sm-10">
                                    @include('layouts/district_group',['provinceName'=>'info[living_province_id]','cityName'=>'info[living_city_id]','countyName'=>'info[living_county_id]'])
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2"></label>
                                <div class="col-sm-10">
                                    <input class="form-control" name="info[living_address]" type="text" title="现居住地"
                                           placeholder="详细地址，请输入0-30个字符">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">紧急联系人</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="info[concat_name]" type="text" title="紧急联系人" />
                                </div>
                                <label class="control-label col-sm-2">联系人电话</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="info[concat_tel]" type="text" title="联系人电话" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">联系人类型</label>
                                <div class="col-sm-4">
                                    <input class="form-control" name="info[concat_type]" type="text" title="联系人类型" />
                                </div>
                                <label class="control-label col-sm-2">招聘人员</label>
                                <div class="col-sm-4" oaSearch="all_staff">
                                    <div class="input-group">
                                        <input class="form-control" name="info[recruiter_name]"
                                               oaSearchColumn="realname" type="text" title="招聘人员"
                                               style="background-color:#fff;" readonly />
                                        <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" oaSearchShow><i
                                                    class="fa fa-search"></i></button>
                                    </span>
                                    </div>
                                    <input class="form-control" name="info[recruiter_sn]" oaSearchColumn="staff_sn"
                                           type="hidden" />
                                </div>
                            </div>
                        </div>
                        <!-- 个人信息1 end -->
                        <!-- 个人信息2 start -->
                        <div class="tab-pane fade personalInfo2">
                            <div class="form-group">
                                <label class="control-label col-sm-2">生日</label>
                                <div class="col-sm-3">
                                    <input class="form-control pick_date" name="birthday" isDate type="text"
                                           title="生日" />
                                </div>
                                <div class="col-sm-1"></div>
                                <label class="control-label col-sm-2">民族</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="info[national]" title="民族">
                                        {!! get_options('i_national','name','name') !!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">学历</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="info[education]" title="学历">
                                        {!! get_options('i_education','name') !!}
                                    </select>
                                </div>
                                <div class="col-sm-1"></div>
                                <label class="control-label col-sm-2">政治面貌</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="info[politics]" title="政治面貌">
                                        {!! get_options('i_politics','name','name') !!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">婚姻状况</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="info[marital_status]" title="婚姻状况">
                                        {!! get_options('i_marital_status','name','name') !!}
                                    </select>
                                </div>
                                <div class="col-sm-1"></div>
                                <label class="control-label col-sm-2">员工属性</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="property" title="员工属性">
                                        <option value="0">无</option>
                                        <option value="1">108将</option>
                                        <option value="2">36天罡</option>
                                        <option value="3">24金刚</option>
                                        <option value="4">18罗汉</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">身高(cm)</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="info[height]" type="number" title="身高" />
                                </div>
                                <div class="col-sm-1"></div>
                                <label class="control-label col-sm-2">体重(kg)</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="info[weight]" type="number" title="体重" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">户口所在地</label>
                                <div class="col-sm-10">
                                    @include('layouts/district_group',['provinceName'=>'info[household_province_id]','cityName'=>'info[household_city_id]','countyName'=>'info[household_county_id]'])
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2"></label>
                                <div class="col-sm-10">
                                    <input class="form-control" name="info[household_address]" type="text" title="户口所在地"
                                           placeholder="详细地址，请输入0-30个字符">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">籍贯</label>
                                <div class="col-sm-10">
                                    <input class="form-control" name="info[native_place]" type="text" title="籍贯"
                                           placeholder="请输入0-30个字符" />
                                </div>
                            </div>
                            @if($authority->checkAuthority(59))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">微商城编码</label>
                                    <div class="col-sm-3">
                                        <input class="form-control" name="info[mini_shop_sn]" type="text"
                                               title="微商城编码" />
                                    </div>
                                    <div class="col-sm-1"></div>
                                    <label class="control-label col-sm-2">钉钉编号</label>
                                    <div class="col-sm-4">
                                        <input class="form-control" name="dingding" type="text" title="钉钉编号" />
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- 个人信息2 end -->
                        <!-- 关系人 start -->
                        <div class="tab-pane fade relativeInfo">
                            <div class="form-group" isFormList>
                                <label class="control-label col-sm-2">关系人</label>
                                <div class="col-sm-4">
                                    <div class=" input-group">
                                        <input class="form-control" name="relative[][pivot][relative_name]"
                                               oaSearchColumn="realname" type="text" title="关系人"
                                               style="background-color:#fff;" readonly />
                                        <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" oaSearchShow><i
                                                    class="fa fa-search"></i></button>
                                    </span>
                                    </div>
                                    <input name="relative[][pivot][relative_sn]" oaSearchColumn="staff_sn"
                                           type="hidden" />
                                </div>
                                <label class="control-label col-sm-2">关系类型</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="relative[][pivot][relative_type]" title="关系类型">
                                        <option value="">-- 无 --</option>
                                        {!! get_options('staff_relative_type','name','id',[],['sort'=>'asc']) !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- 关系人 end -->
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>