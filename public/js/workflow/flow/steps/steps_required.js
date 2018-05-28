//必填字段
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
    if ($("#key_id").val() == '') {//判断新增时加载所有必填字段  否则执行编辑的数据
        alternativeListInit('r_alternative_tbody');
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

    //本步骤必填字段 点击当前添加样式
    $("#r_next_step_tab tr").on('click', function (event) {
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
                    $.each($("#r_next_step_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#r_next_step_tab tr"), function (i) {
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
                    $.each($("#r_next_step_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#r_next_step_tab tr"), function (i) {
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
                    $.each($("#r_next_step_tab tr"), function (i) {
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
            $.each($("#r_next_step_tab tr"), function (i, v) {
                $(this).removeClass('ui-selected');
            });
            $(this).addClass('ui-selected');
        }
    });

    //本步骤必填字段 点击取消全部选中样式
    $("#r_next_step_div").on('click', function (event) {
        $("#r_next_step_tab tr").removeClass('ui-selected');
    });

    //本步骤必填字段  双击选项
    // $("#r_next_step_tab tr").dblclick(function(){
    //     if('ctrl' != key && 'shift' != key)
    //     {
    //         $(this).appendTo('#r_alternative_tbody');
    //         bClick('r_alternative_tbody');
    //     }
    // });

    //备选字段 点击当前添加样式
    $("#r_alternative_tab tr").on('click', function (event) {
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
                    $.each($("#r_alternative_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#r_alternative_tab tr"), function (i) {
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
                    $.each($("#r_alternative_tab tr"), function (i) {
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#r_alternative_tab tr"), function (i) {
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
                    $.each($("#r_alternative_tab tr"), function (i) {
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
            $.each($("#r_alternative_tab tr"), function (i, v) {
                $(this).removeClass('ui-selected');
            });
            $(this).addClass('ui-selected');
        }
    });

    //备选字段 点击取消全部选中样式
    $("#r_alternative_div").on('click', function (event) {
        $("#r_alternative_tab tr").removeClass('ui-selected');
    });

    //备选字段  双击选项
    // $("#r_alternative_tab tr").dblclick(function(){
    //     if('ctrl' != key && 'shift' != key)
    //     {
    //         $(this).appendTo('#r_next_step_tbody');
    //         bClick('r_next_step_tbody');
    //     }
    // });    

});


