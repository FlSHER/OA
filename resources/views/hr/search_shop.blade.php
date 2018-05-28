<div id="searchShopResult{{$mark}}" class="modal fade">
    <button id="openSearchShopResult{{$mark}}" data-toggle="modal" href="#searchShopResult{{$mark}}" class="hidden"></button>
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
            $("#searchShopResult{{$mark}}").on("click", function (event) {
                if (event.target.id === "searchShopResult{{$mark}}" || event.target.getAttribute("data-dismiss") === "modal") {
                    $("#searchShopResult{{$mark}},#searchShopResult{{$mark}}+.modal-backdrop").fadeOut(300, function () {
                        $(this).remove();
                    });
                }
            });
            var target = <?php echo $target; ?>;
            /* dataTables start */
            var searchTable<?php echo $mark; ?> = $('#search_result{{$mark}}').dataTable({
                "columns": [
                    {"data": "shop_sn", "title": "编号", searchable: false},
                    {"data": "name", "title": "姓名"},
                    {"data": "department.full_name", "title": "所属部门", searchable: false},
                    {"data": "brand.name", "title": "所属品牌", searchable: false},
                    {"data": "province.name", "title": "店铺地址(省)", visible: false, searchable: false},
                    {"data": "city.name", "title": "店铺地址（市）", visible: false, searchable: false},
                    {"data": "county.name", "title": "店铺地址（区）", visible: false, searchable: false},
                    {"data": "address", "title": "店铺地址", sortable: false, searchable: false,
                        createdCell: function (nTd, sData, oData, iRow, iCol) {
                            var provinceName = oData.province ? oData.province.name + "-" : "";
                            var cityName = oData.city ? oData.city.name + "-" : "";
                            var countyName = oData.county ? oData.county.name : "";
                            var html = provinceName + cityName + countyName + " " + sData;
                            $(nTd).html(html);
                        }
                    },
                    {"data": "manager_name", "title": "店长", searchable: false}
                ],
                "ajax": "/hr/shop/list?",
                "scrollY": 900,
                "info": false,
                "pageLength": 20,
                "lengthChange": false,
                "pagingType": "numbers",
                "stateSave": false,
                "order": [[0, "asc"]],
                "language": {"search": "输入店铺名称查找"},
                "search": {"search": "{{$name}}"},
                "createdRow": function (row, data, dataIndex) {
                    if (typeof searchShopClick<?php echo $mark; ?> === "function") {
                        $(row).on("click", "", data, searchShopClick<?php echo $mark; ?>);
                    } else {
                        $(row).on("click", function () {
                            for (var i in target) {
                                $("input[name='" + target[i] + "']").val(data[i]);
                            }
                            $("#searchShopResult{{$mark}} .close").click();
                        });
                    }
                }
            });
            /* dataTables end */
        });
    </script>
</div>
