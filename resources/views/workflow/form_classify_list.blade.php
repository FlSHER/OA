<!--form_classify_list-->
<a href="#myModal-1" data-toggle="modal" class="btn btn-success" type="button" id="createClassify">创建表单分类 </a><div hidden class="form_classify_hidden" style="float: right;text-align:center;width:100%;margin-top:-30px;color:red;"></div>
<!--创建表单分类start-->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-1" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title" id="formClassifyTitle">创建表单分类</h4>
            </div>
            <div class="modal-body">
                <form action="{{asset(route('workflow.formClassifySubmit'))}}" id="form_classify_form" method="post" class="form-horizontal" role="form">
                    <input type="hidden" id="validateTijiao" value="0">
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">分类名称<span style="color: red;">*</span></label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" maxlength="30" id="classifyname" name="classifyname" placeholder="请输入表单分类名称">
                            <!--<p style="color: red;"></p>-->
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 col-sm-2 control-label">表单描述</label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" maxlength="255" name="describe" placeholder="请输入描述">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button type="button" class="btn btn-primary" id="form_classify_submit">保存</button>
                        </div>
                    </div>
                    <div class="deleteId" hidden>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--创建表单分类end-->

<!--表单分类列表数据start-->
<div class="panel-body">
    <div class="adv-table">
        <table  class="display table table-bordered table-striped" id="form-classify">

        </table>
    </div>
</div>