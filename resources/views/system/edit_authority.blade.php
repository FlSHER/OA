<button id="openEditAuthority" data-toggle="modal" href="#editAuthorityByOne" class="hidden"></button>
<div id="editAuthorityByOne" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">权限管理</h4>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <div class="btn-group">
                    <button class="btn btn-sm btn-default" onclick="$.fn.zTree.getZTreeObj('authority_treeview').checkAllNodes(true);">全选</button>
                    <button class="btn btn-sm btn-default" onclick="$.fn.zTree.getZTreeObj('authority_treeview').checkAllNodes();">清除选择</button>
                    <button class="btn btn-sm btn-default" onclick="$.fn.zTree.getZTreeObj('authority_treeview').expandAll(true);">全部展开</button>
                    <button class="btn btn-sm btn-default" onclick="$.fn.zTree.getZTreeObj('authority_treeview').expandAll();">全部收起</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 ztree" id="authority_treeview"></div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-success" onclick="saveAuthoritiesByAjax(authorityZTreeSetting.async.otherParam)">确认</button>
            </div>
        </div>
    </div>
</div>
<script>
    var authorityZTreeSetting = {
        async: {
            url: "/system/authority/treeview?_token={{csrf_token()}}"
        },
        check: {
            enable: true,
            chkboxType: {"Y": "ps", "N": "s"}
        },
        view: {
            showIcon: false
        }
    };

    function editAuthority(param) {
        $("#waiting").fadeIn(200);
        authorityZTreeSetting.async.otherParam = param;
        $.fn.zTree.init($("#authority_treeview"), authorityZTreeSetting);
        $("#waiting").fadeOut(300);
        $("#openEditAuthority").click();
    }

    function saveAuthoritiesByAjax(param) {
        $("#waiting").fadeIn(200);
        var checkedNodes = $.fn.zTree.getZTreeObj('authority_treeview').getCheckedNodes(true);
        checkedNodes = checkedNodes.map(function (item) {
            return item.id;
        });
        param['authorities'] = checkedNodes;
        var url = "/system/authority/set";
        $.ajax({
            type: "POST",
            url: url,
            data: param,
            dataType: 'json',
            success: function (msg) {
                if (msg['status'] === 1) {
                    $(".close").click();
                    $("#waiting").fadeOut(300);
                }
            },
            error: function (err) {
                alert(err.responseText);
            }
        });
    }
</script>