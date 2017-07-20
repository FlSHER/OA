$(function () {
    /* dataTables start */
    table = $('#datatable_list').dataTable({
        "columns": columns,
        "ajax": "/system/authority/list",
        "scrollX": 746
    });
    /* dataTables end */
    /* zTree start */
    $.fn.zTree.init($("#authority_tree_view"), {//权限排序
        async: {
            url: "/system/authority/treeview"
        },
        edit: {
            enable: true,
            showRemoveBtn: false,
            showRenameBtn: false,
            drag: {
                isCopy: false,
                isMove: true,
                inner: false,
                prev: dropCallback,
                next: dropCallback
            }
        },
        callback: {
            onDrop: updateOrder
        }
    });
    /* zTree End */

    $('.nav-tabs li a').on("click", function () {
        var link = $(this).attr("href");
        switch (link) {
            case "#treeview":
                $.fn.zTree.getZTreeObj("authority_tree_view").reAsyncChildNodes(null, "refresh");
                break;
        }
    });
});

function updateOrder(event, treeId, treeNodes, targetNode, moveType) {
    if (moveType === null) {
        return false;
    }
    $("#waiting").fadeIn(200);
    var nodes = $.fn.zTree.getZTreeObj(treeId).getNodes();
    nodes = getNodesId(nodes);
    var url = '/system/authority/order';
    var data = {'nodes': nodes};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        success: function (msg) {
            if (msg['status'] === 1) {
                $("#waiting").fadeOut(300);
                return true;
            } else if (msg['status'] === -1) {
                $("#waiting").fadeOut(300, function () {
                    alert(msg['message']);
                });
            }
        }
    });
}

function getNodesId(nodes) {
    return nodes.map(function (item) {
        return {"id": item.id, "children": getNodesId(item.children)};
    });
}

function expandAll() {
    $.fn.zTree.getZTreeObj("authority_tree_view").expandAll(true);
}

function collapseAll() {
    $.fn.zTree.getZTreeObj("authority_tree_view").expandAll();
}

function dropCallback(treeId, treeNodes, targetNode) {
    if (treeNodes[0].parentTId != targetNode.parentTId) {
        return false;
    } else {
        return true;
    }
}
