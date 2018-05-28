<!--经办人-->
<!--steps_operator-->
<style >
    /*千万不能删除哦，删除会世界末日哈*/
    #divTableUser>div>div>div>div>div>div { width: 100% !important; }
    #divTableUser>div>div>div>div>div>div>table { width: 100% !important; }
    #list>div>div>div>div{width: 100% !important;}
    #list>div>div>div>div>table{width: 100% !important;}
    #roleShow>div>div>div>div{width: 100% !important;}
    #roleShow>div>div>div>div>table{width: 100% !important;}
    .table-bordered {
        border: 1px solid #dddddd;
        border-collapse: separate;
        /* border-left: 0; */
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    div.dataTables_length label {
        font-weight: normal;
        text-align: left;
        white-space: nowrap;
        margin-left: 10px;
    }
    div.dataTables_info {
        padding-top: 8px;
        white-space: nowrap;
        margin-left: 10px;
    }
    .col-sm-6{width: 48%;}
</style>

<div class="tab-pane" id="operator">
    <div class="flow_top_type clearfix">
        <div style="float:left;width:500px;"><span class="icon20-operator">设置经办权限</span></div>
        <!--<div style="float:right;margin-top:5px;margin-right:5px;"class="step_name_intro">步骤名称:</div>-->
        <div class="step_name_intro">
            <span class="flow_intro">步骤名称:</span>
            <span class="flow_name">{{$data['prcs_name'] or ''}}</span>

        </div>
    </div>
    
    <div style="clear: both;width: 40%;float: left;">	
        <div class="f_field_block">
            <div class="f_field_label"><span class="f_field_title">授权范围（人员）</span></div>
            <div class="f_field_ctrl">
                <textarea type="hidden"  style="display:none;"id="prcs_user" name="prcs_user" >{{$data['prcs_user'] or ''}}</textarea>
                <textarea style="width:500px;height:60px;" id="copy_prcs_user_name" wrap="yes" readonly="">{{$data['copy_prcs_user_name'] or ''}}</textarea>   
                <a href="javascript:;" class="orgAdd" onclick="SelectUser('divTableUser')">添加</a>
                <a href="javascript:;" class="orgClear" onclick="ClearUser('prcs_user', 'copy_prcs_user_name')">清空</a>
            </div>
        </div>
        <div class="f_field_block">
            <div class="f_field_label"><span class="f_field_title">授权范围（部门）</span></div>
            <div class="f_field_ctrl">
                <textarea type="hidden" style="display:none;" name="prcs_dept" id="prcs_dept" >{{$data['prcs_dept'] or ''}}</textarea>
                <textarea style="width:500px;height:60px;"id="copy_prcs_dept_name" wrap="yes" readonly="">{{$data['copy_prcs_dept_name'] or ''}}</textarea>   
                <a href="javascript:;" class="orgAdd" onclick="SelectDept('divTableDept')">添加</a>
                <a href="javascript:;" class="orgClear" onclick="ClearUser('prcs_dept', 'copy_prcs_dept_name')">清空</a>
            </div>
        </div>
        <div class="f_field_block">
            <div class="f_field_label"><span class="f_field_title">授权范围（角色）</span></div>
            <div class="f_field_ctrl">
                <textarea type="hidden" style="display:none;"id="prcs_priv" name="prcs_priv" >{{$data['prcs_priv'] or ''}}</textarea>
                <textarea style="width:500px;height:60px;"  id="copy_prcs_priv_name" wrap="yes" readonly="">{{$data['copy_prcs_priv_name'] or ''}}</textarea>   
                <a href="javascript:;" class="orgAdd" onclick="SelectPriv('divTableRole')">添加</a>
                <a href="javascript:;" class="orgClear" onclick="ClearUser('prcs_priv', 'copy_prcs_priv_name')">清空</a>
            </div>
        </div>
        <div class="f_field_block">
            <div class="f_field_label"><span class="f_field_title">会签人设置</span> <font color="red">该设置对[无主办人会签]类型不生效</font></div>
            <div class="f_field_ctrl">
                <input type="radio" style="vertical-align: text-bottom;" <?php if(isset($data['sign_type'])){
                    if($data['sign_type'] == 0){ echo "checked";}
                }?>  name="sign_type" value="0">不允许
                <input type="radio" style="vertical-align: text-bottom;" <?php if(isset($data['sign_type'])){
                    if($data['sign_type'] == 1){ echo "checked";}
                }else{
                    echo "checked";
                }?> name="sign_type" value="1">本步骤经办人			
                <input type="radio" style="vertical-align: text-bottom;"<?php if(isset($data['sign_type'])){
                    if($data['sign_type'] == 2){ echo "checked";}
                }?> name="sign_type" value="2">全部人员
            </div>
        </div>
        <!--		<div id="countersign" class="f_field_block" style="display:none">
                                <div class="f_field_label"><span class="f_field_title">是否允许会签人加签</span></div>
                                <input type="radio" style="vertical-align: text-bottom;" checked="" name="countersign" value="0">不允许<input type="radio" style="vertical-align: text-bottom;" name="countersign" value="1">允许	
                        </div>		-->
    </div>
    <div style="width: 60%;float: right;">
        <div style="margin-right: 30px;" >
            <div class="modal-dialog modal-lg">
                <div class="panel-body">
                    <div class="panel" id="divTableUser" hidden>
                        <table class="table table-hover table-bordered dataTable no-footer" id="searchTable"></table>
                    </div>
                    <div class="panel" id="divTableDept" style="height: 900px;" hidden>
                        <div style="border: 1px solid #dddddd;border-radius: 4px;">
                            <section class="panel">
                                <header class="panel-heading custom-tab dark-tab">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#list" data-toggle="tab" id="tableTab">部门列表</a>
                                        </li>
                                        <li class="">
                                            <a href="#treeview" data-toggle="tab">部门结构</a>
                                        </li>
                                    </ul>
                                </header>
                                <div class="panel-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="list">
                                            <table class="table table-striped table-bordered dataTable no-footer" id="department_list"></table>
                                        </div>
                                        <div class="tab-pane" id="treeview">
                                            <div class="btn-group" id="nestable_list_menu">
                                                <a type="button" class="btn btn-sm btn-default" href="javascript:expandAll()">全部展开</a>
                                                <a type="button" class="btn btn-sm btn-default" href="javascript:collapseAll()">全部收起</a>
                                            </div>
                                            <div class="ztree" id="deptShow" style="overflow:auto;max-height:750px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="panel" id="divTableRole" hidden>
                        <div style="border: 1px solid #dddddd;border-radius: 4px;">
                            <section class="panel">
                                <header class="panel-heading">
                                    职位列表
                                </header>
                                <div class="panel-body" id="roleShow">
                                    <table class="table table-striped table-bordered dataTable no-footer" id="position_list"></table>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

