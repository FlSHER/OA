<div id="searchStaffResult{{$mark}}" class="modal fade">
    <button id="openSearchStaffResult{{$mark}}" data-toggle="modal" href="#searchStaffResult{{$mark}}" class="hidden"></button>
    <button aria-hidden="true" data-dismiss="modal" class="close hidden" type="button">×</button>
    <div class="modal-dialog modal-lg">
        <div class="panel">
            <div class="panel-body">
                <table class="table table-sm table-hover table-bordered dataTable no-footer" id="search_result{{$mark}}"></table>
            </div> 
        </div>
    </div>
    <script>
        $(function () {
            $("#searchStaffResult{{$mark}}").on('hidden.bs.modal', function () {
                $(this).remove();
            });
            var target = <?php echo $target; ?>;
            /* dataTables start */
            if (typeof window['searchTable{{$mark}}'] === 'undefined') {
                var searchTable<?php echo $mark; ?> = $('#search_result{{$mark}}').dataTable({
                    "columns": [
                        {"data": "staff_sn", "title": "编号", "searchable": false},
                        {"data": "realname", "title": "姓名"},
                        {"data": "brand.name", "title": "品牌", "searchable": false},
                        {"data": "department.full_name", "title": "部门全称", "searchable": false},
                        {"data": "position.name", "title": "职位", "searchable": false},
                        {"data": "status.name", "title": "状态", "searchable": false},
                        {"data": "shop.name", "title": "所属店铺", "visible": false, "searchable": false}
                    ],
                    "ajax": "/hr/staff/list",
                    "scrollY": 900,
                    "info": false,
                    "pageLength": 20,
                    "lengthChange": false,
                    "pagingType": "numbers",
                    "stateSave": false,
                    "order": [[0, "asc"]],
                    "language": {"search": "输入姓名查找"},
                    "search": {"search": "{{$realname}}"},
                    "createdRow": function (row, data, dataIndex) {
                        if (typeof searchStaffClick<?php echo $mark; ?> === "function") {
                            $(row).on("click", "", data, searchStaffClick<?php echo $mark; ?>);
                        } else {
                            $(row).on("click", function () {
                                for (var i in target) {
                                    var key = i.split('.');
                                    var value = data;
                                    for (var j in key) {
                                        value = value[key[j]];
                                    }
                                    $("input[name='" + target[i] + "']").val(value);
                                }
                                $("#searchStaffResult{{$mark}}").modal('hide');
                            });
                        }
                    }
                });
            } else {
                window['searchTable{{$mark}}'].fnDraw();
            }
            /* dataTables end */
        });
    </script>
</div>
