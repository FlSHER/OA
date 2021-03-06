var table;
$(function() {
  $(".modal form").oaForm({
    callback: {
      submitSuccess: function(msg, obj) {
        obj.query.closest(".modal").modal("hide");
        table.draw();
      }
    },
    oaTime: {
      enableSeconds: false
    },
    oaFormList: {
      min: 0,
      unit: {
        addBtn: false
      },
      callback: {
        afterAdd: function(li, obj) {
          li.oaSearch("all_staff");
        }
      }
    }
  });
  /* dataTables start  */
  table = $("#shop_list").oaTable({
    columns: columns,
    ajax: {
      url: "/hr/shop/list"
    },
    scrollX: 746,
    buttons: buttons,
    filter: $("#filter")
  });
  /* dataTables end  */

  /* zTree start */
  departmentOptionsZTreeSetting = {
    async: {
      url: "/hr/department/tree",
      dataFilter: function(treeId, parentNode, responseData) {
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
      onClick: function(event, treeId, treeNode) {
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
                function(node) {
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
});
//添加店铺
function addShop() {
  oaWaiting.show();
  $("#addForm")[0].reset();
  oaWaiting.hide();
}
//编辑店铺
function edit(id) {
  oaWaiting.show();
  var form = $("#editForm");
  $("#editForm")
    .oaForm()[0]
    .fillData("/hr/shop/info", { id: id });
  oaWaiting.hide();
}

function del(id) {
  var _confirm = confirm("确认删除当前店铺？");
  if (_confirm) {
    oaWaiting.show();
    var url = "/hr/shop/delete";
    var data = { id: id };
    $.ajax({
      type: "POST",
      url: url,
      data: data,
      async: false,
      dataType: "json",
      success: function(msg) {
        if (msg["status"] == 1) {
          table.draw();
          oaWaiting.hide();
        } else if (msg["status"] === -1) {
          oaWaiting.hide(function() {
            alert(msg["message"]);
          });
        }
      }
    });
  }
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

$("#poiByOne").on("show.bs.modal", function(event) {
  var button = $(event.relatedTarget);
  var name = button.data("name"),
      lng = button.data('lng'),
      lat = button.data('lat');

  $("#shop-id").val(button.data("id"));
  $(this).find("#shop-name").val(name);

  AMapUI.loadUI(["misc/PositionPicker", "misc/PoiPicker"], function(
    PositionPicker,
    PoiPicker
  ) {
    var center = null;
    if (lng && lat) {
      center = [lng, lat];
    }
    var map = new AMap.Map("containerMap", {
      zoom: 16,
      scrollWheel: false,
      center: center, // 地图初始坐标
      //zoom: 13 // 缩放比例
    });
    // 搜索定位
    var poiPicker = new PoiPicker({
      input: "poi-name",
      placeSearchOptions: {
        map: map,
        pageSize: 6
      }
    });
    poiPicker.on("poiPicked", function(poiResult) {
      poi = poiResult.item,
      lat = poi.location.lat
      lng = poi.location.lng
      setVal({
        lat: lat,
        lng: lng,
        name: poi.name,
        address: poi.district + poi.address
      });
      map.panTo([lng, lat]);
    });

    // 拖拽定位
    var positionPicker = new PositionPicker({
      mode: "dragMap", // dragMap: 拖拽地图模式 dragMarker: 拖拽标记模式
      map: map
    });
    positionPicker.on("success", function(poi) {
      setVal({
        name: poi.nearestRoad,
        lat: poi.position.lat,
        lng: poi.position.lng,
        address: poi.address
      });
    });
    positionPicker.on("fail", function(poi) {
      alert("定位失败请重试!");
    });
    positionPicker.start();
    map.panBy(0, 1); // 地图显示比例
  });
  function setVal(poi) {
    $("#poi-name").val(poi.address);
    $("#latitude").val(poi.lat);
    $("#longitude").val(poi.lng);
    $("#location").val(poi.address);
    $("#geohash").val(encodeGeoHash(poi.lat, poi.lng));
  }
});
