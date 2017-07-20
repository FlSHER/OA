//可写字段
$(function () {
    var key = '';  //记录shift键
    var firstClickIndex = -1; //初始索引
    var firstClick = 0;//点击次数
    var curClickIndex = -1;//当前点击索引
    var lastClickIndex = -1;//上一次点击的索引

    var alternativeFirstClickIndex = -1; //备选步骤 初始索引
    var alternativeFirstClick = 0;//备选步骤 点击次数
    var alternativeCurClickIndex = -1;//备选步骤 当前点击索引
    var alternativeLastClickIndex = -1;//备选步骤 上一次点击的索引

    //初始化备选字段,此函数定义于flow_steps_list.js
    if ($("#key_id").val() == '') {//判断新增时加载所有备选字段  否则执行编辑的数据
        alternativeListInit('writable_right_tbody');
    }

    $(window).keydown(function (e) {
        if (e.ctrlKey)
        {
            key = 'ctrl';
        }
        if (e.shiftKey)
        {
            key = 'shift';
        }
    }).keyup(function () {
        key = '';
    });

    //下一步骤 点击当前添加样式
    $("#write_next_step_tab tr").on('click', function (event) {
        event.stopPropagation();//阻止冒泡
        if ('ctrl' == key) {
            if (String($(this).attr('class')) == 'ui-selected')
            {
                $(this).removeClass('ui-selected');
            } else
            {
                $(this).addClass('ui-selected');
            }
        } else if ('shift' == key)
        {
            firstClick++;
            if (1 >= firstClick)
            {
                firstClickIndex = $(this).index();
            } else
            {
                curClickIndex = $(this).index();
            }

            if (-1 != firstClickIndex && -1 != curClickIndex && firstClickIndex > curClickIndex)
            {
                if (-1 != lastClickIndex && lastClickIndex == curClickIndex)
                {
                    $(this).removeClass('ui-selected');
                } else
                {
                    $.each($("#write_next_step_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#alternative_next tr"), function (i) {
                        if (firstClickIndex >= i && i >= curClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    lastClickIndex = curClickIndex;
                }
            } else if (-1 != firstClickIndex && -1 != curClickIndex && firstClickIndex < curClickIndex)
            {
                if (-1 != lastClickIndex && lastClickIndex == curClickIndex)
                {
                    $(this).removeClass('ui-selected');
                } else
                {
                    $.each($("#write_next_step_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#write_next_step_tab tr"), function (i) {
                        if (curClickIndex >= i && i >= firstClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    lastClickIndex = curClickIndex;
                }
            } else
            {
                if (firstClickIndex == curClickIndex)
                {
                    $.each($("#write_next_step_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                }
                if (String($(this).attr('class')) == 'ui-selected')
                {
                    $(this).removeClass('ui-selected');
                } else
                {
                    $(this).addClass('ui-selected');
                }
            }
        } else
        {
            $.each($("#write_next_step_tab tr"), function (i, v) {
                $(this).removeClass('ui-selected');
            });
            $(this).addClass('ui-selected');
        }
    });

    //下一步骤 点击取消全部选中样式
    $("#write_next_step_div").on('click', function (event) {
        $("#write_next_step_tab tr").removeClass('ui-selected');
    });

    //下一步骤  双击选项
    // $("#write_next_step_tab tr").dblclick(function(){
    //     if('ctrl' != key && 'shift' != key)
    //     {
    //         $(this).appendTo('#writable_right_tbody');
    //         bClick('writable_right_tbody');
    //     }
    // });

    //备选步骤点击当前添加样式
    $("#write_alternative_tab tr").on('click', function (event) {
        event.stopPropagation();//阻止冒泡
        if ('ctrl' == key) {
            if (String($(this).attr('class')) == 'ui-selected')
            {
                $(this).removeClass('ui-selected');
            } else
            {
                $(this).addClass('ui-selected');
            }
        } else if ('shift' == key)
        {
            alternativeFirstClick++;
            if (1 >= alternativeFirstClick)
            {
                alternativeFirstClickIndex = $(this).index();
            } else
            {
                alternativeCurClickIndex = $(this).index();
            }

            if (-1 != alternativeFirstClickIndex && -1 != alternativeCurClickIndex && alternativeFirstClickIndex > alternativeCurClickIndex)
            {
                if (-1 != alternativeLastClickIndex && alternativeLastClickIndex == alternativeCurClickIndex)
                {
                    $(this).removeClass('ui-selected');
                } else
                {
                    $.each($("#write_alternative_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#write_alternative_tab tr"), function (i) {
                        if (alternativeFirstClickIndex >= i && i >= alternativeCurClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    alternativeLastClickIndex = alternativeCurClickIndex;
                }
            } else if (-1 != alternativeFirstClickIndex && -1 != alternativeCurClickIndex && alternativeFirstClickIndex < alternativeCurClickIndex)
            {
                if (-1 != alternativeLastClickIndex && alternativeLastClickIndex == alternativeCurClickIndex)
                {
                    $(this).removeClass('ui-selected');
                } else
                {
                    $.each($("#write_alternative_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#write_alternative_tab tr"), function (i) {
                        if (alternativeCurClickIndex >= i && i >= alternativeFirstClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    alternativeLastClickIndex = alternativeCurClickIndex;
                }
            } else
            {
                if (alternativeFirstClickIndex == alternativeCurClickIndex)
                {
                    $.each($("#write_alternative_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                }
                if (String($(this).attr('class')) == 'ui-selected')
                {
                    $(this).removeClass('ui-selected');
                } else
                {
                    $(this).addClass('ui-selected');
                }
            }
        } else
        {
            $.each($("#write_alternative_tab tr"), function (i, v) {
                $(this).removeClass('ui-selected');
            });
            $(this).addClass('ui-selected');
        }
    });

    //备选步骤 点击取消全部选中样式
    $("#write_alternative_div").on('click', function (event) {
        $("#write_alternative_tab tr").removeClass('ui-selected');
    });

    //备选步骤  双击选项
    // $("#write_alternative_tab tr").dblclick(function(){
    //     if('ctrl' != key && 'shift' != key)
    //     {
    //         $(this).appendTo('#writable_left_tbody');
    //         bClick('writable_left_tbody');
    //     }
    // });    


});

//列表控件模式start
init_listCtrl();//列表控件模式初始化
updateData();//列表控件编辑回填数据
//列表控件模式 控制 字段权限设置 按钮
function init_listCtrl() {
    var listCtrl_tr = $('#list_item table tbody tr');
    var td_arr = tdArray();
    if (listCtrl_tr) {
        $.each(listCtrl_tr, function () {
            var listCtrl_title = $(this).find('td:first').text();
            if (-1 != $.inArray(listCtrl_title, td_arr)) {
                $(this).find('td .remind_checkbox span button').attr('disabled', false);
                $(this).find('td .remind_checkbox label input').attr('disabled', false);
            } else {
                $(this).find('td .remind_checkbox span button').attr('disabled', true);
                $(this).find('td .remind_checkbox label input').attr('disabled', true);
            }

        });

    }
}


//得到本步骤可写字段的值  返回数组
function tdArray() {
    var writable_left_tbody_tr = $('#writable_left_tbody tr');
    var arr = new Array();
    $.each(writable_left_tbody_tr, function () {
        arr.push($(this).find('td').text());
    });
    return arr;
}


//字段权限设置点击
function open_bootcss_modal(light, fade) {
    $('#' + light).show();
    $('#' + fade).show();
}

//字段权限设置点击关闭
function close_bootcss_modal(light, fade) {
    $('#' + light).hide();
    $('#' + fade).hide();
}

//列表控件模式 全选
function checked_all(_this, t, id) {
    var tr = $('#' + id + ' .modal-body table tbody tr');
    if (t == 'update') {
        $.each(tr, function () {
            if ($(_this).is(':checked')) {
                $(this).find('td:eq(2)').find('input:eq(0)').attr('checked', true);
            } else {
                $(this).find('td:eq(2)').find('input:eq(0)').attr('checked', false);
            }
        });
    } else if (t == 'secrecy') {
        $.each(tr, function () {
            if ($(_this).is(':checked')) {
                $(this).find('td:eq(2)').find('input:eq(1)').attr('checked', true);
            } else {
                $(this).find('td:eq(2)').find('input:eq(1)').attr('checked', false);
            }
        });
    } else if (t == 'readonly') {
        $.each(tr, function () {
            if ($(_this).is(':checked')) {
                $(this).find('td:eq(2)').find('input:eq(2)').attr('checked', true);
            } else {
                $(this).find('td:eq(2)').find('input:eq(2)').attr('checked', false);
            }
        });
    }
}


//列表控件模式 编辑回填数据
function updateData() {
    var id = $('#key_id').val();
    if (id != '') {
        var updateListCtrlData = $('#updateListCtrlData').text();
        if ('' == updateListCtrlData) {
            return false;
        }
        var data = JSON.parse(updateListCtrlData);//编辑时的数据
        var init_tr_arr = $('#head2 #list_item').find('table tbody .tr_attr');//初始tr数据
        $.each(data, function (key, value) {
            //模式回填
            $.each(value.model.checkbox, function (k, v) {
                init_tr_arr.eq(value.model.tr_index).find('.remind_checkbox input[value="' + v + '"]').attr('checked', true);
            });
            //字段权限回填
            $.each(value.field_permissions, function (i, v) {
                $.each(v, function (vi, vv) {
                    $.each(vv.checkbox, function (key, val) {
                        var chid_tr = init_tr_arr.eq(value.model.tr_index).find('.white_content tbody tr').eq(vv.tr_index).find('input[value="' + val + '"]').attr('checked', true);
                    });
                });
            });
        });
    }
}
//可写字段里的 列表控件模式  数据提交
var listCtrl = {
    query_tr: function () {
        return $('#head2 #list_item').find('table tbody .tr_attr');
    },
    //列表控件模式 数据获取
    listdata: function () {
        var tr_arr = this.query_tr();
        var data = new Array();
        $.each(tr_arr, function (k, v) {
            var object = new Object();
            var o_checkbox = $(this).find('td:eq(1)').find('.remind_checkbox input');
            var input_checked_array = listCtrl.checkbox_value(o_checkbox, k);//添加与删除 模式
            var o_field_permissions_tr = $(this).find('td:eq(2)').find('table tbody tr');
            var field_permissions_arr = listCtrl.field_permissions(o_field_permissions_tr);
            var name = $(this).find('td:eq(0)').attr('listCtrlName');//控件名字

            //检测是否有权限
            var check = listCtrl.dataCheck(name);
            if (check != 0) {
                object.title = $(this).find('td:eq(0)').text();//列表控件模式的 标题
                object.name = name;//控件名字
                object.model = input_checked_array;//列表控件模式的 添加模式和删除模式
                object.field_permissions = field_permissions_arr;//列表控件模式 字段权限设置
                data.push(object);
            }
        });
        return JSON.stringify(data);
    },
    //数据获取之前检测是否有权限
    dataCheck: function (name) {
        var write_field = $('#writable_left_tbody').find('tr');
        var write_name_array = new Array();
        $.each(write_field, function (k, v) {
            write_name_array.push($(this).find('td').attr('name'));
        });
        if ($.inArray(name, write_name_array) == -1) {//无权限
            return 0;
        }
    },
    //处理添加模式和删除模式
    checkbox_value: function (input, k) {
        var object_input = new Object();
        var input_arr = new Array();
        var selSign = 0;
        $.each(input, function () {
            if ($(this).is(':checked')) {
                input_arr.push($(this).val());
                selSign = 1;
            }
        });
        if (1 == selSign) {
            object_input.tr_index = k;//tr索引
        }
        object_input.checkbox = input_arr;
        return object_input;
    },
    //字段权限设置
    field_permissions: function (tr) {
        var arr = new Array();
        $.each(tr, function (k, v) {
            var object = new Object();
            var o_checked = $(this).find('td:eq(2)').find('input');
            object.orgtitle = $(this).find('td:eq(1)').text();
            object.model_permissions = listCtrl.checkbox_value(o_checked, k);
            if (object.model_permissions.checkbox.length > 0) {
                arr.push(object);
            }
        });
        return arr;
    },
};

//列表控件模式end
