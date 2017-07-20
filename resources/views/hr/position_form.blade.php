<div class="form-group">
    <label class="control-label col-lg-3">*职位名称</label>
    <div class="col-lg-8">
        <input class="form-control" name="name" type="text" title="职位名称"/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3">*职级</label>
    <div class="col-lg-8">
        <input class="form-control" name="level" type="number" title="职级"/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3">是否为公共职位</label>
    <div class="col-lg-8">
        <select class="form-control" name="is_public" title="是否为公共职位">
            <option value="0">否</option>
            <option value="1">是</option>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3">关联品牌</label>
    <div class="col-lg-8" id="position_in_add">
        @foreach($brand as $v)
        <div class="col-lg-3" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-left:0;">
            <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                <input name="brand[]" type="checkbox" value="{{$v->id}}">
                <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
            </label>
            <span>{{$v->name}}</span>
        </div>
        @endforeach
    </div>
</div>
{{csrf_field()}}