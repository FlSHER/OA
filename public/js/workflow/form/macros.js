

/**
 *宏控件人员列表下拉框加载更多
 */
$('select[orgtype="y_sys_list_user_list"]').on('change', function () {
    if ($(this).val() == 'add_page') {
        var _this = $(this);
        var length = $(this).find('option:selected').attr('length');
        var p = $(this).find('option:selected').attr('p');

        length = parseInt(length);
        p = parseInt(p);
        p++;
        jQuery(this).val('');//去除点击加载更多的值
        var url = '/workflow/macrosUserInfoPage';
        $.ajax({
            type: 'post',
            url: url,
            data: {p: p, length: length},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (msg) {
                if (msg.data != '') {
                    _this.find('option:last').before(msg.data);
                    _this.find('option:last').attr('p', msg.p);
                    _this.find('option:last').attr('length', msg.length);
                }
            }
        });
    }
});


