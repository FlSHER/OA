<div class="modal-body">
	<div class="form-group">
		<label class="control-label col-lg-3">调动员工</label>
		<div class="col-lg-8 input-group" style="padding-right:15px;">
			<input class="form-control" type="text" name="staff_name" style="background-color:#fff;" readonly/>
			{{-- <span class="input-group-btn">
				<button type="button" class="btn btn-default" onclick="searchStaff(this)"><i class="fa fa-search"></i></button>
			</span> --}}
		</div>
		<input class="form-control" name="staff_sn" type="hidden" title="主管编号" readonly/>
	</div>
	<div class="form-group">
        <label class="control-label col-lg-3">*预计到达时间</label>
        <div class="col-lg-6">
            <input class="form-control" name="budget" type="date"  />
        </div>
        <div class="col-lg-3"></div>
    </div>

    {{-- <div class="form-group">  
        <label class="control-label col-lg-3">*结束时间</label>
        <div class="col-lg-8">
            <input class="form-control" name="code" type="text" title="店铺代码"/>
        </div>
    </div> --}}

	<div class="form-group">
        <label class="control-label col-lg-3">*调离店铺</label>
        <div class="col-lg-8 input-group" style="padding-right:15px;">
            <input class="form-control" type="text" name="out_shop_name" style="background-color:#fff;" readonly/>
            <input class="form-control" name="out_shop_sn" type="hidden"   readonly/>
           {{--  <span class="input-group-btn">
                <button type="button" class="btn btn-default" onclick="searchShop(this,'out')"><i class="fa fa-search"></i></button>
            </span> --}}
            
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">*调达店铺</label>
        <div class="col-lg-8 input-group" style="padding-right:15px;">
            <input class="form-control" name="go_shop_sn" type="hidden"   readonly/>
            <input class="form-control" type="text" name="go_shop_name" style="background-color:#fff;" readonly/>
            <span class="input-group-btn">
                <button type="button" class="btn btn-default" onclick="searchShop(this,'go')"><i class="fa fa-search"></i></button>
            </span>
            
        </div>
    </div>
	
	


    {{csrf_field()}}
</div>