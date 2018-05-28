@inject('HRM',HRM)
<style>
    #filter .ztree li span.button.switch.level0 {
        visibility: hidden;
        width: 1px;
    }

    #filter .ztree li ul.level0 {
        padding: 0;
        background: none;
    }

    #filter .ztree li span.node_name {
        font-size: 12px;
    }
</style>
<div class="panel-body bg-warning" id="filter"
     style="display:none;box-shadow: inset 0 0px 10px 0 rgba(0,0,0,.075);padding-bottom:0;">
    <form class="form-horizontal" method="post">
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">员工编号</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input class="form-control" name="staff_sn.is" oaSearchColumn="staff_sn" type="text"
                               title="员工编号"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow>
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">店铺代码</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="shop">
                        <input class="form-control" name="shop_sn.is" oaSearchColumn="shop_sn" type="text"
                               title="店铺代码"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">打卡类型</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="attendance_type.is">
                            <option value="">全部</option>
                            <option value="1">上/下班</option>
                            <option value="2">调动</option>
                            <option value="3">请假</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select class="form-control" name="type.is">
                            <option value="">全部</option>
                            <option value="1">到店</option>
                            <option value="2">离店</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">操作人编号</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input class="form-control" name="operator_sn.is" oaSearchColumn="staff_sn" type="text"
                               title="操作人编号"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow>
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">打卡时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="clock_at.min" type="text" defaultHour="0"
                                       defaultMinute="0" isDateTime/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="clock_at.max" type="text" defaultHour="23"
                                       defaultMinute="59" isDateTime/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">计划打卡时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="punctual_time.min" type="text" defaultHour="0"
                                       defaultMinute="0" isDateTime/>
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="punctual_time.max" type="text" defaultHour="23"
                                       defaultMinute="59" isDateTime/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-lg-offset-1">
                <button type="reset" class="btn btn-warning btn-sm">重置</button>
                <button type="submit" class="btn btn-default btn-sm pull-right">确认</button>
            </div>
        </div>
    </form>
</div>
<script>
    window.onload = function () {
        $("#department_children").on('change', function () {
            if ($(this).is(':checked')) {
                $('#department_filter').find('select').removeAttr('name');
                $('#department_filter').find('input').attr('name', 'department_id.in');
            } else {
                $('#department_filter').find('select').attr('name', 'department_id');
                $('#department_filter').find('input').removeAttr('name');
            }
        });
        $("#filter form").on("reset", function () {
            $(this).find('input,select').val('').change();
        });
    };

    departmentOptionsZTreeSetting = {
        async: {
            url: "/hr/department/tree",
            dataFilter: function (treeId, parentNode, responseData) {
                if (treeId == "department_filter_option") {
                    return [{
                        "name": "全部",
                        "drag": true,
                        "id": "0",
                        "children": responseData,
                        "iconSkin": " _",
                        "open": true
                    }];
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
</script>