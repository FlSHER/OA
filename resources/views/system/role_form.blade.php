<div class="form-group">
    <label class="control-label col-lg-3">*角色名称</label>
    <div class="col-lg-8">
        <input class="form-control" name="role_name" type="text" title="角色名称"/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3">配属品牌</label>
    <div class="col-lg-8" id="position_in_add">
        @foreach($brand as $v)
        <div class="col-lg-3" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-left:0;">
            <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                <input name="brand_id[]" type="checkbox" value="{{$v->id}}" <?php if ($v->is_public) echo 'checked disabled'; ?>>
                <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
            </label>
            <span  <?php if ($v->is_public) echo 'style="color:#999;"'; ?>>{{$v->name}}</span>
        </div>
        @endforeach
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3">配属部门</label>
    <div class="col-lg-8">
        <div class="btn-group m-bot15">
            <button type="button" class="btn btn-sm btn-default" onclick="departmentTreeview.checkAllNodes(true);">全选</button>
            <button type="button" class="btn btn-sm btn-default" onclick="departmentTreeview.checkAllNodes();">清除选择</button>
            <button type="button" class="btn btn-sm btn-default" onclick="departmentTreeview.expandAll(true);">全部展开</button>
            <button type="button" class="btn btn-sm btn-default" onclick="departmentTreeview.expandAll();">全部收起</button>
        </div>
        <div class="ztree department_treeview" style="max-height: 500px;overflow: auto;"></div>
    </div>
</div>
<div class="form-group">

</div>
{{csrf_field()}}