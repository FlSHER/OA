<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-lg-3">*部门名称</label>
        <div class="col-lg-8">
            <input class="form-control" name="name" type="text" title="部门名称"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">*上级部门</label>
        <div class="col-lg-8">
            <style>
                .ztree.ztreeOptions li span.button.switch.level0 {visibility:hidden; width:1px;}
                .ztree.ztreeOptions li ul.level0 {padding:0; background:none;}
            </style>
            <select class="form-control" name="parent_id" title="上级部门" onmousedown="showTreeViewOptions(this)">
                <option value="0">无</option>
                {!!$HRM->getDepartmentOptionsById()!!}
            </select>
            <div class="ztree ztreeOptions"></div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">所属品牌</label>
        <div class="col-lg-8">
            <select class="form-control" name="brand_id" title="所属品牌">
                {!!$HRM->getOptions(App\Models\Brand::visible())!!}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">部门负责人</label>
        <div class="col-lg-8 input-group" style="padding-right:15px;">
            <input class="form-control" type="text" name="manager_name" style="background-color:#fff;" readonly/>
            <span class="input-group-btn">
                <button type="button" class="btn btn-default" onclick="searchStaff(this)"><i class="fa fa-search"></i></button>
            </span>
        </div>
        <input class="form-control" name="manager_sn" type="hidden" title="主管编号" readonly/>
    </div>
<!--    <div class="form-group">
        <label class="control-label col-lg-3">关联职位</label>
        <div class="col-lg-8" id="position_in_add">
            @foreach($position as $v)
            <div class="col-lg-3" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-left:0;">
                <label class="frame check frame-sm <?php if ($v->is_public) echo 'disabled'; ?>" unselectable="on" onselectstart="return false;">
                    <input name="position_id[]" type="checkbox" value="{{$v->id}}"  <?php if ($v->is_public) echo 'disabled'; ?> >
                    <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                </label>
                <span  <?php if ($v->is_public) echo 'style="color:#999;"'; ?>>{{$v->name}}</span>
            </div>
            @endforeach
        </div>
    </div>-->
    {{csrf_field()}}
</div>