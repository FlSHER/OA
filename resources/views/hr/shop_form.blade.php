<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-lg-3">*店铺代码</label>
        <div class="col-lg-8">
            <input class="form-control" name="shop_sn" type="text" title="店铺代码"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">*店铺名称</label>
        <div class="col-sm-8">
            <input class="form-control" name="name" type="text" title="店铺名称"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">*所属部门</label>
        <div class="col-sm-8">
            <select class="form-control" name="department_id" title="所属部门" onmousedown="showTreeViewOptions(this)">
                {!!$HRM->getDepartmentOptionsById()!!}
            </select>
            <div class="ztree ztreeOptions"></div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">*所属品牌</label>
        <div class="col-sm-8">
            <select class="form-control" name="brand_id" title="所属品牌">
                {!!$HRM->getOptions(App\Models\Brand::visible())!!}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">*店铺地址</label>
        <div class="col-sm-8">
            @include('layouts/district_group',['provinceName'=>'province_id','cityName'=>'city_id','countyName'=>'county_id'])
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3"></label>
        <div class="col-sm-8">
            <input class="form-control" name="address" type="text" title="店铺地址" placeholder="请输入店铺街道地址" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">店长</label>
        <div class="col-sm-4" oaSearch="staff">
            <div class="input-group">
                <input class="form-control" name="manager_name" oaSearchColumn="realname" type="text" title="店长" style="background-color:#fff;" readonly/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                </span>
            </div>
            <input class="form-control" name="manager_sn" oaSearchColumn="staff_sn" type="hidden"/>
        </div>
    </div>
    <?php if (check_authority(117)): ?>
        <div class="form-group">
            <label class="control-label col-sm-3">店员</label>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6" isFormList>
                        <div class="row">
                            <div class="input-group col-xs-10">
                                <input class="form-control" name="staff[][realname]" oaSearchColumn="realname" type="text" title="关系人" style="background-color:#fff;" readonly/>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" oaSearchShow><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                            <input name="staff[][staff_sn]" oaSearchColumn="staff_sn" type="hidden"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="submit" class="btn btn-success">确认</button>
</div>