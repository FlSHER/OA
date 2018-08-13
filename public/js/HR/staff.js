var table;

var oaFormOption = {
  callback: {
    submitSuccess: oaFormSubmitSuccess
  },
  oaFormList: {
    min: 0,
    callback: {
      afterAdd: oaFormListAfterAdd
    }
  }
};

$(function () {
  /* oaForm */
  $(".modal form").oaForm(oaFormOption);
  /* oaTable start */
  table = $("#example").oaTable({
    columns: staffColumns,
    ajax: { url: "/hr/staff/list" },
    buttons: buttons,
    filter: $("#filter"),
    scrollY: 586
  });
  /* oaTable end */
  /* zTree start */
  departmentOptionsZTreeSetting = {
    async: {
      url: "/hr/department/tree",
      dataFilter: function (treeId, parentNode, responseData) {
        if (treeId == "department_filter_option") {
          return [
            {
              name: "全部",
              drag: true,
              id: "0",
              children: responseData,
              iconSkin: " _",
              open: true
            }
          ];
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
            options
              .prev()
              .children("option")
              .first()
              .prop("selected", true);
            if (options.next().prop("tagName") == "INPUT") {
              options.next().val("");
            }
          } else {
            options
              .prev()
              .children("option[value=" + treeNode.id + "]")
              .prop("selected", true);
            if (options.next().prop("tagName") == "INPUT") {
              var children = $.fn.zTree.getZTreeObj(treeId).getNodesByFilter(
                function (node) {
                  return node.drag;
                },
                false,
                treeNode
              );
              var departmentId = treeNode.id;
              for (var i in children) {
                departmentId += "," + children[i].id;
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
  // 生成可选职位
  $("select[name='brand_id']").on("change", function () {
    var brandId = $(this).val();
    var form = $(this).closest("form");
    getCostBrands(brandId, form);
    getPositionOptions(brandId, form);
  });
  // 批量上传
  $("#import_staff").on("change", importStaff);
  // 通过身份证号获取生日性别
  $(".id_card_number").on("focus", getInfoFromIdCardNumber);
});

/**
 * 获取费用品牌
 */
function getCostBrands(brandId, form) {
  var box = form.find(".cost_brands_box");
  var url = "/hr/cost_brands";
  var data = { brand_id: brandId };
  box.find("input").prop({ disabled: true, checked: false });
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    async: false,
    success: function (costBrands) {
      costBrands.forEach(function (costBrand) {
        box.find("input[value=" + costBrand.id + "]").prop("disabled", false);
      });
    }
  });
}

/**
 * 获取职位选项
 */
function getPositionOptions(brandId, form) {
  var positionTag = form.find("select[name='position_id']");
  var optionId = positionTag.attr("origin_value");
  var url = "/hr/position/options";
  var data = { brand_id: brandId };
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    async: false,
    dataType: "text",
    success: function (msg) {
      var firstOptionTag = positionTag.children().eq(0);
      if (firstOptionTag.val() === "") {
        msg = '<option value="">全部</option>' + msg;
      }
      positionTag.html(msg);
      if (optionId) {
        positionTag.val(optionId);
      }
    }
  });
}

/**
 * 查看个人信息
 * @param {int} staffSn
 */
function showPersonalInfo(staffSn) {
  oaWaiting.show();
  var url = "/hr/staff/show_info";
  var data = { staff_sn: staffSn };
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "text",
    success: function (msg) {
      $("#board-right").html(msg);
      oaWaiting.hide();
    },
    error: showErrorPage
  });
}

function addStaff() {
  oaWaiting.show();
  var form = $("#addForm");
  form[0].reset();
  oaWaiting.hide();
}

function entryStaff() {
  oaWaiting.show();
  var form = $("#entryForm");
  form[0].reset();
  oaWaiting.hide();
}

function editStaff(staffSn, type) {
  oaWaiting.show();
  var form = $("#" + type + "Form");
  if (typeof staffSn === "number") {
    form.oaForm()[0].fillData("/hr/staff/info", { staff_sn: staffSn });
  } else if (typeof staffSn === "object") {
    form[0].reset();
  }
  oaWaiting.hide();
}

/**
 * 重置密码
 * @author 28youth
 * @param  staffSn 员工编号
 */
function resetPwd(staffSn) {
  oaWaiting.show();
  if (typeof staffSn === "number") {
    var url = "/hr/staff/reset";
    var data = { staff_sn: staffSn };
    $.ajax({
      type: "POST",
      url: url,
      data: data,
      dataType: "json",
      success: function (response) {
        if (response["status"] === 1) {
          alert(response['message']);
          oaWaiting.hide();
        }
      }
    });
  }
}

/**
 * 调离员工
 * @returns {undefined}
 */
function transferOut() {
  if (confirm("确认调离？")) {
    var form = $("#transferForm");
    form
      .find(
        "select[name=department_id],select[name=brand_id],select[name=position_id],select[name=status_id]"
      )
      .val(1).change();
    form.find("input[name='cost_brands[][id]']").prop('checked', false);
    form.find("input[name='cost_brands[][id]'][value=1]").prop('checked', true);
    form.find("input[name=shop_sn]").val("");
    form.find("input[name=operate_at]").oaDate({ defaultDate: "today" });
    form.find("textarea[name=operation_remark]").val("人员调离");
    form.submit();
  }
}

/**
 * 激活员工
 * @param {int} staffSn
 * @returns {undefined}
 */
function activeStaff(staffSn) {
  oaWaiting.show();
  var url = "/hr/staff/submit";
  var curDate = new Date();
  var dateStr =
    curDate.getFullYear() +
    "-" +
    (curDate.getMonth() + 1) +
    "-" +
    curDate.getDate();
  var data = {
    staff_sn: staffSn,
    is_active: 1,
    operation_type: "active",
    operate_at: dateStr,
    operation_remark: ""
  };
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "json",
    success: function (msg) {
      if (msg["status"] === 1) {
        table.draw();
        oaWaiting.hide();
      } else if (msg["status"] === -1) {
        oaWaiting.hide(function () {
          alert(msg["message"]);
        });
      }
    }
  });
}

/**
 * 删除员工
 * @param {int} staffSn
 */
function deleteStaff(staffSn) {
  var _confirm = confirm("确认删除？");
  if (_confirm) {
    oaWaiting.show();
    var url = "/hr/staff/delete";
    var data = { staff_sn: staffSn };
    $.ajax({
      type: "POST",
      url: url,
      data: data,
      dataType: "json",
      success: function (msg) {
        if (msg["status"] === 1) {
          table.draw();
          oaWaiting.hide();
        } else if (msg["status"] === -1) {
          oaWaiting.hide(function () {
            alert(msg["message"]);
          });
        }
      },
      error: showErrorPage
    });
  }
}

/**
 * 离职交接
 * @param {type} staffSn
 */
function showStaffLeavingPage(staffSn) {
  var url = "/hr/staff/leaving";
  var data = { staff_sn: staffSn };
  $.ajax({
    type: "GET",
    url: url,
    data: data,
    dataType: "text",
    success: function (msg) {
      $(".wrapper").append(msg);
      $("#leavingByOne").modal("show");
      $("#leavingForm").oaForm(oaFormOption);
      $("#leavingByOne").on("hidden.bs.modal", function () {
        $(this).remove();
      });
    },
    error: showErrorPage
  });
}

function showTreeViewOptions(obj) {
  var options = $(obj).next(".ztreeOptions");
  var width = $(obj).outerWidth();
  departmentTriger = obj;
  options.outerWidth(width);
  $(obj)
    .children("option")
    .hide();
  if (options.html().length == 0) {
    $.fn.zTree.init(options, departmentOptionsZTreeSetting);
  }
  options.toggle();
  $("body").bind("click", hideTreeViewOptions);
  return false;
}

function hideTreeViewOptions(event) {
  if (
    !(
      $(event.target).hasClass("ztreeOptions") ||
      $(event.target).parents(".ztreeOptions").length > 0 ||
      event.target == departmentTriger
    )
  ) {
    $(".ztree.ztreeOptions").hide();
    $("body").unbind("click", hideTreeViewOptions);
  }
}

function importStaff() {
  oaWaiting.show();
  var formdata = new FormData();
  var fileObj = $(this).get(0).files;
  var url = "/hr/staff/import";
  if (fileObj) {
    formdata.append("staff", fileObj[0]);
    $.ajax({
      type: "POST",
      url: url,
      data: formdata,
      contentType: false,
      processData: false,
      success: function (msg) {
        if (msg["status"] === -1) {
          alert(msg["message"]);
          $("#import_result").html("");
          oaWaiting.hide();
        } else {
          table.draw();
          $("#import_result").html(msg);
          oaWaiting.hide();
        }
      },
      error: showErrorPage
    });
  }
  $(this).val("");
}

function exportStaff(e, dt, node, config) {
  var dataCount = dt.ajax.json().recordsFiltered;
  if (dataCount == 0) {
    alert("无可用信息");
  } else if (confirm("确认以当前条件导出？")) {
    oaWaiting.show();
    var params = dt.ajax.params();
    delete params.length;
    var url = "/hr/staff/export";
    $.ajax({
      type: "POST",
      url: url,
      data: params,
      dataType: "json",
      success: function (msg) {
        if (msg["state"] == 1) {
          var fileName = msg["file_name"];
          window.location.href = "/storage/exports/" + fileName + ".xlsx";
          oaWaiting.hide();
        }
      },
      error: showErrorPage
    });
  }
}

function getInfoFromIdCardNumber() {
  $(this).on("keyup", function () {
    var value = $(this).val();
    if (value.length == 18) {
      var birthday =
        value.substr(6, 4) +
        "-" +
        value.substr(10, 2) +
        "-" +
        value.substr(12, 2);
      var gender = value.substr(16, 1) % 2;
      var birthdayInput = $(this)
        .parents("form")
        .find("input[name=birthday]");
      var genderSelect = $(this)
        .parents("form")
        .find("select[name=gender_id]");
      if (
        new Date(birthday) != "Invalid Date" &&
        birthdayInput.attr("origin_value") == undefined
      ) {
        birthdayInput.val(birthday);
      }
      if (genderSelect.attr("origin_value") == undefined) {
        genderSelect.val(gender == 1 ? gender : 2);
      }
    }
  });
  $(this).on("blur", function () {
    $(this).off("keyup");
    $(this).off("blur");
  });
}

function showErrorPage(err) {
  document.write(err.responseText);
}

function oaFormSubmitSuccess(msg, obj) {
  table.draw(false);
  $(".close").click();
}

function oaFormListAfterAdd(li, obj) {
  li.oaSearch("all_staff");
}
