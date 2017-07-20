@inject('HRM',HRM)
<style>
    #filter .ztree li span.button.switch.level0 {visibility:hidden; width:1px;}
    #filter .ztree li ul.level0 {padding:0; background:none;}
    #filter .ztree li span.node_name {font-size:12px;}
</style>
<div class="panel-body bg-warning" id="filter" style="display:none;box-shadow: inset 0 0px 10px 0 rgba(0,0,0,.075);padding-bottom:0;">
    <form class="form-horizontal" method="post">
        <div class="form-group">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label input-sm col-md-4">员工姓名</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control input-sm" name="staff_name[like]"/>
                    </div>
                </div>
            </div>
			<div class="col-lg-3">
                <div class="row">
                    <label class="control-label input-sm col-md-3">选择</label>
                   <div class="col-md-8">
                        <select class="form-control input-sm" name="fruit">
                            <option value="">全部</option>
                            <option value="市场">市场</option>
							<option value="后台">后台</option>
                        </select>
                    </div>
                </div>
            </div>
			<div class="col-lg-3">
                <div class="row">
                    <label class="control-label input-sm col-md-4">所属品牌</label>
                    <div class="col-md-8">
                        <select class="form-control input-sm" name="brand_id">
                            <option value="">全部</option>
                            {!!$HRM->getOptions(new App\Models\Brand)!!}
                        </select>
                    </div>
                </div>
            </div>
			<div class="col-lg-3">
                <div class="row">
                    <label class="control-label input-sm col-md-4">违纪原因</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control input-sm" name="reason[like]"/>
                    </div>
                </div>
            </div>
        </div>
       <div class="form-group">
            
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label input-sm col-md-3">职位</label>
                    <div class="col-md-8">
                        <select class="form-control input-sm" name="position_id" >
                            <option value="">全部</option>
                            {!!$HRM->getOptions(new App\Models\Position,[],'level')!!}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="control-label input-sm col-md-2">所属部门</label>
                    <div class="col-md-7" id="department_filter">
                        <select class="form-control input-sm" name="" onmousedown="showTreeViewOptions(this)">
                            <option value="">全部</option>
                            {!!$HRM->getDepartmentOptionsById()!!}
                        </select>
                        <div class="ztree ztreeOptions" id="department_filter_option"></div>
                        <input type="hidden" name="department_id[in]">
                    </div>
                    <div class="col-md-3 form-control-static">
                        <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                            <input type="checkbox" checked id="department_children">
                            <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                        </label> <span style="cursor:default;">包含下级</span>
                    </div>
                </div>
            </div>
			<div class="col-lg-2">
                <div class="row">
                    <label class="control-label input-sm col-md-4">是否付钱</label>
                   <div class="col-md-6">
                        <select class="form-control input-sm" name="paid_at[null]">
                            <option value="">全部</option>
                            <option value="1">是</option>
							<option value="0">否</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
		     
            <div class="col-lg-6">
                <div class="row">
                    <label class="control-label input-sm col-md-2">违纪时间</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-sm-5" >
                                <input class="form-control  input-sm dateinfo" name="committed_at[min]" type="text"/>
                            </div>
                            <span class="input-sm row" style="position:absolute;">至</span>
                            <div class="col-sm-5 date  fl">
                                <input class="form-control input-sm dateinf" name="committed_at[max]" type="text"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			 <div class="col-lg-6">
                <div class="row">
                    <label class="control-label input-sm col-md-2">付款时间</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-sm-5" >
                                <input class="form-control input-sm dateinfr" name="paid_at[min]" type="text"/>
                            </div>
                            <span class="input-sm row" style="position:absolute;">至</span>
                            <div class="col-sm-5 date  fl">
                                <input class="form-control input-sm  dateinfer" name="paid_at[max]" type="text"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
		</div>
		<div class="form-group">	
            <div class="col-lg-2 col-lg-offset-4">
                <button type="reset" class="btn btn-warning btn-sm">重置</button>
                <button type="submit" class="btn btn-default btn-sm pull-right">确认</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
$(function(){
  jeDate({
        dateCell:".dateinfo",
        format:"YYYY-MM-DD",
        isinitVal:false,
        isTime:false, //isClear:false,
        minDate:"2014-09-19 00:00:00",
        okfun:function(val){alert(val)}
    });
	jeDate({
        dateCell:".dateinf",
        format:"YYYY-MM-DD",
        isinitVal:false,
        isTime:false, //isClear:false,
        okfun:function(val){alert(val)}
    });
	jeDate({
        dateCell:".dateinfr",
        format:"YYYY-MM-DD",
        isinitVal:false,
        isTime:false, //isClear:false,
        okfun:function(val){alert(val)}
    });
	jeDate({
        dateCell:".dateinfer",
        format:"YYYY-MM-DD",
        isinitVal:false,
        isTime:false, //isClear:false,
        minDate:"2014-09-19 00:00:00",
        okfun:function(val){alert(val)}
    });
})
  window.onload = function () {
        $("#department_children").on('change', function () {
            if ($(this).is(':checked')) {
                $('#department_filter').find('select').removeAttr('name');
                $('#department_filter').find('input').attr('name', 'department_id');
            } else {
                $('#department_filter').find('select').attr('name', 'department_id');
                $('#department_filter').find('input').removeAttr('name');
            }
        });
        $("#filter form").on("submit", submitFilterConditions);
        $("#filter form").on("reset", function () {
            $(this).find('input,select').val('').change();
        });
    };

    function submitFilterConditions() {
        var tableApi = iication.api();
        var info = $(this).serializeArray();
        var condition = false;
        var filter = new Object;
        for (var i in info) {
            var v = info[i];
            if (v.value.length > 0) {
                condition = true;
                var filterName = v.name.replace('[]', '').replace(/\]/g, '').replace(/\[/g, '.');
                if (typeof filter[filterName] == undefined) {
                    filter[filterName] = [];
                }
                if (v.name.search('[]') > 0) {
                    filter[filterName].push(v.value);
                } else {
                    filter[filterName] = v.value;
                }
            }
        }
        tableApi.on('preXhr.dt', function (e, settings, data) {
            data.filter = filter;
        });
        tableApi.draw();
        if (condition) {
            $("#filter_icon").css("color", "#65cea7");
        } else {
            $("#filter_icon").attr("style", false);
        }
        return false;
    }
	function showTreeViewOptions(obj) {
    var options = $(obj).next(".ztreeOptions");
    var width = $(obj).outerWidth();
    departmentTriger = obj;
    options.outerWidth(width);
    $(obj).children("option").hide();
    if (options.html().length == 0) {
        $.fn.zTree.init(options, departmentOptionsZTreeSetting);
    }
    options.toggle();
    $("body").bind("click", hideTreeViewOptions);
    return false;
}

function hideTreeViewOptions(event) {
    if (!($(event.target).hasClass("ztreeOptions") || $(event.target).parents(".ztreeOptions").length > 0 || event.target == departmentTriger)) {
        $(".ztree.ztreeOptions").hide();
        $("body").unbind("click", hideTreeViewOptions);
    }
}
 departmentOptionsZTreeSetting = {
        async: {
            url: "/hr/department/tree",
            dataFilter: function (treeId, parentNode, responseData) {
                if (treeId == "department_filter_option") {
                    return [{"name": "全部", "drag": true, "id": "0", "children": responseData, "iconSkin": " _", "open": true}];
                } else {
                    return responseData;
                }

            }
        },
        view: {
            dblClickExpand: false
        },
        callback: {
            onClick: function (event, treeId, treeNode) {
                if (treeNode.drag) {
                    var options = $(event.target).parents(".ztreeOptions");
                    if (treeNode.id == 0) {
                        options.prev().children("option").first().prop("selected", true);
                        if (options.next().prop('tagName') == 'INPUT') {
                            options.next().val('');
                        }
                    } else {
                        options.prev().children("option[value=" + treeNode.id + "]").prop("selected", true);
                        if (options.next().prop('tagName') == 'INPUT') {
                            var children = $.fn.zTree.getZTreeObj(treeId).getNodesByFilter(function (node) {
                                return node.drag;
                            }, false, treeNode);
                            var departmentId = treeNode.id;
                            for (var i in children) {
                                departmentId += ',' + children[i].id;

                            }
                            options.next().val(departmentId);
                        }
                    }
                    options.hide();
                    options.prev().change();
                }
            }
        }
    };
</script>