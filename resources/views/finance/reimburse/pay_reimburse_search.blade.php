@inject('reim_department_name',"\App\Services\Finance\Reimburse\AuditService")
@inject('HRM',HRM)
<div class="panel-body bg-warning reimburse_search"
     style="display:none;box-shadow: inset 0 0px 10px 0 rgba(0,0,0,.075);padding-bottom:0;">
    <form class="form-horizontal" method="post">
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">订单编号</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="reim_sn.like" />
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">申请人</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input type="text" class="form-control" name="realname.like" oaSearchColumn="realname" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">审批人</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input type="text" class="form-control" name="approver_name.like"
                               oaSearchColumn="realname" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">审核人</label>
                    <div class="col-sm-8 input-group input-group-sm" oaSearch="staff">
                        <input type="text" class="form-control" name="accountant_name.like"
                               oaSearchColumn="realname" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" oaSearchShow><i
                                        class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-3">
                <div class="row">
                    <label class="control-label col-lg-4 col-sm-2">资金归属</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="reim_department.name">
                            <option value="">全部</option>
                            @foreach($reim_department_name->getReimDepartmentName() as $v)
                                <option value="{{$v->name}}">{{$v->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="control-label col-sm-2">部门</label>
                    <div class="col-md-7 col-sm-7" id="department_filter">
                        <select class="form-control" name="" onmousedown="showTreeViewOptions(this)">
                            <option value="">全部</option>
                            {!!$HRM->getDepartmentOptionsById()!!}
                        </select>
                        <div class="ztree ztreeOptions" id="department_filter_option"></div>
                        <input type="hidden" name="department_id.in">
                    </div>
                    <div class="col-sm-3 form-control-static">
                        <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                            <input type="checkbox" checked id="department_children">
                            <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
                        </label> <span style="cursor:default;">包含下级</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">申请时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="send_time.min" type="text" isDate />
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="send_time.max" type="text" isDate />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">审批时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="approve_time.min" type="text" isDate />
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="approve_time.max" type="text" isDate />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">审核时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="audit_time.min" type="text" isDate />
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="audit_time.max" type="text" isDate />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="form-group form-group-sm">
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">副总审批时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="manager_approved_at.min" type="text" isDate />
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="manager_approved_at.max" type="text" isDate />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="control-label col-lg-3 col-sm-2">转账时间</label>
                    <div class="col-lg-9 col-sm-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" name="paid_at.min" type="text" isDate />
                            </div>
                            <label class="control-label row pull-left" style="padding-left: 9px">至</label>
                            <div class="col-xs-6">
                                <input class="form-control" name="paid_at.max" type="text" isDate />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-lg-offset-2">
                <button type="reset" class="btn btn-warning btn-sm">重置</button>
                <button type="submit" class="btn btn-default btn-sm pull-right">确认</button>
            </div>
        </div>
    </form>
</div>
<script>
  $(function () {
    /* zTree start */
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
    /* zTree End */
  });

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
    $("#check_reimburse_search form").on("reset", function () {
      $(this).find('input,select').val('').change();
    });
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