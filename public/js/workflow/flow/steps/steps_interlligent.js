//智能选人 自动选人规则
var smart = {
    token: $('meta[name="_token"]').attr('content'),
    //获取自动选人规则上一步骤的规则
    auto_type: function () {
        var data = $("#flow_step_define").serialize();
        var url = '/workflow/check_auto_type';
        var str = '';
        $.ajax({
            type: 'post',
            url: url,
            data: data,
            async: false,
            headers: {
                'X-CSRF-TOKEN': this.token
            },
            success: function (data) {
                str = smart.switch_auto_type_value(data);
            }
        });
        return str;
    },
    //判断上一步的规则，对当前步骤的数据进行判断
    //data 上一步骤的规则value值
    switch_auto_type_value: function (data) {
        var prcs_user = $('#prcs_user').val();
        var prcs_dept = $('#prcs_dept').val();
        var prcs_priv = $('#prcs_priv').val();
        var str = '';
        switch (data) {
            case '1':
                if (prcs_user != '') {
                    alert('你已在上一步骤选择了自动选择流程发起人');
                }
                break;
            case '2':
                if (prcs_user != '') {
                    alert('你已在上一步骤选择了自动选择本部门主管');
                }
                break;
            case 'is_null':

                break;
            default:
                if (prcs_user == '' && prcs_dept == '' && prcs_priv == '') {
                    str = 'nullAll';
//                       alert('请选择经办人');
//                       return false;
                }
        }
        return str;
    }
}


