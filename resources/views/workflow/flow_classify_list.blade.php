<!--flow_classify_list-->
<a href="#flowClassify" data-toggle="modal" class="btn btn-success" type="button" id="createFlowClassify">创建流程分类 </a> <div hidden class="flow_classify_hidden" style="float: right;text-align:center;width:100%;margin-top:-30px;color:red;"></div>
<!--创建流程分类start-->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="flowClassify" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title" id="flow_classify_title">创建流程分类</h4>
            </div>
            <div class="modal-body">
                <form action="{{asset(route('workflow.flowClassifySubmit'))}}" method="post" class="form-horizontal" id="flow_classify_form" role="form">
                    <input type="hidden" id="flowClassifyTijiao" value="0"/>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">分类名称<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" maxlength="30" name="flow_classifyname" placeholder="请输入流程分类名称">
                            <p style="color:red;"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">流程描述</label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" maxlength="255" name="flow_describe" placeholder="请输入描述">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button type="button" class="btn btn-primary" id ="submit_flow">保存</button>
                        </div>
                    </div>
                    <div class="flowClassifyDeleteId" hidden>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--创建流程分类end-->

<!--流程分类列表数据start-->
<div class="panel-body">
    <div class="adv-table">
        <table  class="display table table-bordered table" id="flow_classify_list">
<!--            <thead>
                <tr>
                    <th class="col-lg-1">分类id</th>
                    <th class="col-lg-5">分类名字</th>
                    <th class="col-lg-3">描述</th>
                    <th class="col-lg-1">流程数量</th>
                    <th class="col-lg-1">上次修改时间</th>
                    <th class="col-lg-1">操作</th>
                </tr>
            </thead>
            <tbody>

                <tr class="gradeX">
                    <td>id</td>
                    <td>分类名字</td>
                    <td>描述</td>
                    <td>流程数量</td>
                    <td>上次修改时间</td>
                    <td><a href="#flowClassify" data-toggle="modal" class="edit" title="编辑"  urlid="">编辑 </a> | <a href="javascript:void();" class="delete_flowClassify" title="删除"  deleteId="">删除</a></td>
                </tr>

            </tbody>-->
        </table>
    </div>
</div>
<!--流程分类列表数据end-->

