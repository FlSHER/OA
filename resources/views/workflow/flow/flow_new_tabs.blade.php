<!-- 代码部分begin -->
<!--flow_new_tabs-->
<style>
    .col-lg-10 textarea{height: 34px;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                        }
    .orgAdd:hover{text-decoration:none;}
    .orgClear:hover{text-decoration:none;}
    .orgAdd:link {text-decoration: none;}
    .orgClear:link {text-decoration: none;}

</style>
<div class="bigone" style="position:relative;height:740px;">
    <div style="position:relative;">
        <div class="titleone">
            <a href="#" style="color: red; background: rgb(255, 255, 255);">基本属性</a>
            <a href="#">工作名称/文号</a>
            <a href="#">流程说明</a>
            <a href="#" class="last">扩展字段</a>
        </div>
        <div class="con">
            <!-- 第一个内容 -->
            <div class="listone list1" style="padding: 20px;">                         
                <div class="form-horizontal">
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">流程名称<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            @if(isset($skip))
                            <input type="text" id="skip" hidden>
                            <input type="text" id="flow_id" value="{{isset($skip)?$data['flow_id']:0}}" hidden>
                            @endif
                            <input type="text" class="form-control" id="flow_name" maxlength="30" name="flow_name" value="{{isset($skip)?$data['flow_name']:''}}" placeholder="请输入名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">流程分类<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            <select name="form_classify_id" class="form-control" id="flow_sort" ></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">表单<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            <span class="form-control" style="position: relative;display:inline-block;">
                                <input type="text" maxlength="255" id="form_id" value="{{isset($skip)?$data['form_id']:''}}" name="form_id" hidden>
                                <input type="text" maxlength="255" id="form_name" value="{{isset($skip)?$data['form_name']:''}}" name="form_name" list="files" autocomplete="off" placeholder="请选择表单">
                                <ul id="files" class="on_changes"></ul>
                                <span><button type="button" id="form_select"><i class="fa fa-sort-down"></i></button></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">所属部门<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            <select class="form-control" id="department_id" name="department_id" title="所属部门" onmousedown="showTreeViewOptions(this)">
                                {!!$HRM->getDepartmentOptionsById()!!}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">流程类型<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            <select class="form-control" id="flow_type">
                                <option value="1">固定流程</option>
                                <option value="2">自由流程</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">委托类型<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            <select class="form-control" id="free_other">
                                <option value="0">禁止委托</option>
                                <option value="1">仅允许委托当前步骤经办人</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">排序号</label>
                        <div class="col-lg-10">
                            <input type="text" id="flow_no" class="form-control" maxlength="5" name="sort" value="{{isset($skip)?$data['flow_no']:''}}" placeholder="请输入排序号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">允许传阅</label>
                        <div class="col-lg-10">   
                            <input type="radio" name="view_priv" id="view_priv_true" value="1"> 是
                            <input type="radio" name="view_priv" id="view_priv_false" value="0" style="margin-left:15px;" checked> 否
                        </div>
                    </div>
                    <div id="pass_priv" hidden>
                        <div class="form-group">
                            <label  class="col-lg-2 col-sm-2 control-label">传阅人</label>
                            <div class="col-lg-10">
                                <textarea id="view_user_id" name="view_user_id" hidden>{{isset($skip)?$data['view_user']:''}}</textarea> 
                                <textarea id="view_user_name" disabled>{{isset($skip)?$data['view_user_show']:''}}</textarea>
                                <a href="javascript:;" class="orgAdd" onclick="newWindow('./passReadPerson')">选择</a>
                                <a href="javascript:;" class="orgClear" onclick="passReadClear('view_user_id','view_user_name')">清空</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label  class="col-lg-2 col-sm-2 control-label">传阅部门</label>
                            <div class="col-lg-10">
                                <textarea id="view_dept_id" name="view_dept_id" hidden>{{isset($skip)?$data['view_dept']:''}}</textarea>
                                <textarea id="view_dept_name" disabled>{{isset($skip)?$data['view_dept_show']:''}}</textarea>
                                <a href="javascript:;" class="orgAdd" onclick="newWindow('./passReadDept')">选择</a>
                                <a href="javascript:;" class="orgClear" onclick="passReadClear('view_dept_id','view_dept_name')">清空</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label  class="col-lg-2 col-sm-2 control-label">传阅角色</label>
                            <div class="col-lg-10">
                                <textarea id="view_role_id" name="view_role_id" hidden>{{isset($skip)?$data['view_role']:''}}</textarea>
                                <textarea id="view_role_name" disabled>{{isset($skip)?$data['view_role_show']:''}}</textarea>
                                <a href="javascript:;" class="orgAdd" onclick="newWindow('./passReadRole')">选择</a>
                                <a href="javascript:;" class="orgClear" onclick="passReadClear('view_role_id','view_role_name')">清空</a>
                            </div>
                        </div>
                    </div>
                    <!--编辑时存放流程ID-->
                    <div class="deleteId" hidden>

                    </div>
                </div>
            </div>
            <!-- 第二个内容 -->
            <div class="listone list2">
                <div style="width: 32%;float:left;margin-top: 15px;">
                    <div style="margin-left: 5px;" class="ggg">
                        <div>
                            <span style="font-size: 12px;font-weight: bold;margin-left: 2px;margin-right: 5px;">文号表达式</span>
                        </div>
                        <div>
                            <input type="text" name="" id="auto_name" value="{{isset($skip)?$data['auto_name']:''}}" style="background-color: #ffffff;border: 1px solid #cccccc;border-radius: 4px;padding-left:4px;">
                        </div>
                        <div>
                            <span style="font-size:12px;font-weight:bold;margin-left:2px;margin-right:5px;">编号计数器</span>
                        </div>
                        <div>
                            <input type="text" name="" id="auto_num" value="{{isset($skip)?empty($data['auto_num'])?0:$data['auto_num']:0}}" style="background-color:#ffffff;border: 1px solid #cccccc;border-radius:4px;width:84px;padding-left:4px;">
                        </div>
                        <div>
                            <span style="font-size: 12px;font-weight: bold;margin-left: 2px;margin-right: 5px;">编号位数</span>
                        </div>
                        <div>
                            <input type="text" name="" id="auto_len" value="{{isset($skip)?empty($data['auto_len'])?0:$data['auto_len']:0}}" style="background-color: #ffffff;border: 1px solid #cccccc;border-radius: 4px;width:84px;padding-left:4px;">
                        </div>
                        <div>
                            <span style="font-size: 12px;font-weight: bold;margin-left: 2px;margin-right: 5px;">是否允许修改</span>
                        </div>
                        <div>
                            <select id="auto_edit" style="font-size:12px;height:24px;background-color:#ffffff;border: 1px solid #cccccc;border-radius: 4px;">
                                <option value="1">允许修改</option>
                                <option value="0">不允许修改</option>
                                <option value="2">允许输入前缀</option>
                                <option value="3">允许输入后缀</option>
                                <option value="4">允许输入前缀和后缀</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="width: 68%;float: right;margin-top: 15px;padding-right:5px;font-size: 12px;">
                    <div style="background:#fcfcfc;">
                        <div>一、文号表达式说明</div>
                        <div>
                            <div><span>表达式中可以使用以下特殊标记</span></div>
                            <div style="margin-top: 5px;">
                                <span>{Y} ：表示年</span>
                                <span style="margin-left: 20px;">{M} ：表示月</span>
                                <span style="margin-left: 20px;">{D}：表示日</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>{H} ：表示时</span>
                                <span style="margin-left: 20px;">{I} ：表示分</span>
                                <span style="margin-left: 20px;">{S}：表示秒</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>{F} ：表示流程名</span>
                                <span style="margin-left: 20px;">{U} ：表示用户姓名</span>
                                <span style="margin-left: 20px;">{R}：表示角色</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>{FS}：表示流程分类名称</span>
                                <span style="margin-left: 20px;">{SD}：表示短部门</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>{LD}：表示长部门</span>
                                <span style="margin-left: 20px;">{RUN}：表示流水号</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>{N} ：表示编号，通过编号计数器取值并自动增加计数值</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>{NY} ：表示编号，每过一年编号重置一次</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>{NM} ：表示编号，每过一月编号重置一次</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>例如，表达式为：成建委发[{Y}]{N}号，同时设置自动编号显示长度为4，</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>则自动生成的文号如下：成建委发[2006]0001号。</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>例如，表达式为：BH{N}，同时设置自动编号显示长度为3，</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>则自动生成的文号如下：BH001。</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>例如，表达式为：{F}流程（{Y}年{M}月{D}日{H}:{I}）{U}，</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>则自动生成文号如：请假流程（2006年01月01日10:30）张三。</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>可以不填写自动文号表达式，则系统默认按以下格式，如：</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <span>请假流程(2006-01-01 10:30:30)。</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 第三个内容 -->
            <div class="listone list3">
             <textarea id="flow_desc" style="outline:none;overflow:auto;border:0px;width: 100%;height: 135px;margin-top:5px;" placeholder="在这里输入内容...">{{isset($skip)?$data['flow_desc']:''}}</textarea>  
         </div>
         <!-- 第四个内容 -->
         <div class="listone list4">
            <div align="center" border="0" style=" display:inline-block;width:100%;padding-top:20px;">
                <div style="width:90%;height: 100%;" id="list_flds_str">
                    <div style="width:44%;float:left;">
                        <div style="background:#f1f0e8;line-height: 30px;font-weight: bold;height:30px;padding-left:10px;color:inherit;text-align:left;font-size: 13px;">
                            显示在待办列表上的字段
                        </div>
                        <!--已选择字段列表-->
                        <select style="WIDTH:100%;height:182px;" multiple name="list1" size="12" id="list1" ></select>
                        
                        <div style="margin-top: 10px;">
                            <input type="button" value="全选" id="addAllOption">
                            <div style="color: green;font-size:12px;margin-top: 10px;">
                                点击条目时，可以组合CTRL键进行多选
                            </div>
                        </div>
                    </div>
                    <div style="width:12%;float:left;position:relative;">
                        <div style="position:relative;top:50%;transform: translateY(90%);">
                            <div>
                                <input type="button" value=">" id="addOption">
                            </div>
                            <div style="margin-top: 20px;">
                                <input type="button" value="<" id="delOption">
                            </div>
                        </div>
                    </div>
                    <div style="width:44%;float: right;">
                        <div style="background:#f1f0e8;line-height: 30px;font-weight: bold;height:30px;padding-left:10px;color:inherit;text-align:left;font-size: 13px;">备选字段
                        </div>
                        <!--备选字段列表-->
                        <select style="WIDTH:100%;height:182px;" multiple name="list2" size="12" id="list2"></select>
                        
                        <div style="margin-top: 10px;">
                            <input type="button" value="全选" id="delAllOption">
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>
</div>
<script type="text/javascript" src="{{asset('js/jquery-3.1.1.min.js')}}"></script>
<script>
//打开新窗口
function newWindow(url)
{
    var str=window.open(url,'name','width=880,height=605,left=1020,top=99,toolbar=no,menubar=no,resizable=no,location=no,status=no');
    if(str!=null)
    {
        str.focus();  //保持在最前面
    }
}
/**
*传阅清除
*clear_hide_container_id:提交后台数据的容器
*clear_show_container_id:展示给用户的容器
*/
function passReadClear(clear_hide_container_id,clear_show_container_id)
{
    document.getElementById(clear_hide_container_id).value = "";
    document.getElementById(clear_show_container_id).value = "";
}
$(function(){
    //初始化分类
    classify();
    //动态变更委托权限
    $('#flow_type').change(function(){
        if(2 == $('#'+this.id+' option:selected').val() && $("#free_other").find("option[value='2']").length < 1)
        {
            $('#free_other').append("<option value='2'>自由委托</option>");
        }
        else
        {
            $("#free_other").find("option[value='2']").remove();
        }
    });
    var pass_priv = $('#pass_priv');
    $('#view_priv_true').click(function(){
        pass_priv.show();
    });
    $('#view_priv_false').click(function(){
        pass_priv.hide();
    });
});
//分类
function classify(){
    var url = "./flowConfigCreate";
    $.ajax({
        type: 'post',
        url: url,
        dataType: 'json',
        async:false,
        data: '',
        headers:{
          'X-CSRF-TOKEN':$('meta[name="_token"]').attr('content')  
        },
        success: function (msg) {
            var str = '<option value="0">未分类</option>';
            $.each(msg, function (k, v) {
                str += '<option value="' + v.id + '">' + v.flow_classifyname + '</option>';
            });
            $('#flow_sort').html(str);
        }
    });
}
</script>

@if(isset($skip))
<script>
    if("" != "{{$data['list_flds_str']}}")
    {
        var str = "{{$data['list_flds_str']}}";
        str = str.split("/");
        $(function(){
            $.each(str,function(i,v){
                if(''!=v)
                {
                    $('#list1').append('<option value="'+v+'">'+v+'</option>');
                }
            });
        });
    }
    $(function (){
        if(2 == "{{$data['free_other']}}")
        {
            $('#free_other').append("<option value='2'>自由委托</option>");
        }
        $("#flow_sort").val({{$data['flow_sort']}});
        $("#department_id").val({{$data['department_id']}});
        $("#flow_type").val({{$data['flow_type']}});
        $("#free_other").val({{$data['free_other']}});
        $("#auto_edit").val({{$data['auto_edit']}});
        if("1"=="{{$data['view_priv']}}")
        {
            $('input[id="view_priv_true"]').trigger("click");
        }
    });
</script>
@endif