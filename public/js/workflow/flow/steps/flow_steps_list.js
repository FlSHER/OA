/*校验字符串是否正确*/
function checkStr(str, bracket)//括号标记bracket：1表示有括号，0表示无括号
{
    var passSign = 'true';
    /*匹配'[数字]'、逻辑符、括号还有其他字符*/
    var re = /(\[\d+\])|(and)|(or)|(AND)|(OR)|\(|\)|\[|\]|\w+|[\u4E00-\u9FA5\uF900-\uFA2D]/g;
    /*组合新的标准字符串*/
    var prevSign = -1;//记录前一次字符标记(0:表示‘[and|or]’,1:表示'[数字]',2:表示‘(’,3:表示')')
    var strArr = str.match(re);
    if (1 == bracket) {
        if (3 >= strArr.length) {
            /*不符合要求的字符串*/
            passSign = 'false';
            return passSign;
        }
    } else {
        if (3 > strArr.length) {
            /*不符合要求的字符串*/
            passSign = 'false';
            return passSign;
        }
    }
    /*检查最后一个元素*/
    if (!(/(\[\d+\])|\)/.test(strArr.pop()))) {
        /*不符合要求的字符串*/
        passSign = 'false';
        return passSign;
    }
    $.each(strArr, function (i, v) {
        if (/(\[\d+\])/.test(v)) {
            var curSign = 1;
        } else if (/\(/.test(v)) {
            var curSign = 2;
        } else if (/\)/.test(v)) {
            var curSign = 3;
        } else {
            var curSign = 0;
        }
        if (0 == i)//第一个字符
        {
            //如果字符串一开始就是[‘and|or|)’]，就肯定不正确
            if (0 == curSign || 3 == curSign) {
                passSign = 'false';
                return false;
            }
            prevSign = curSign;
        } else
        {
            switch (curSign) {
                case 1:
                    //当前(1:表示‘[数字]’),如果上一字符串不是[0:表示‘[and|or]’,2:表示‘(’],则字符串不符合标准
                    if (0 != prevSign && 2 != prevSign) {
                        passSign = 'false';
                        return false;
                    }
                    prevSign = curSign;
                    break;
                case 2:
                    //当前[2:表示‘(’],如果上一字符串不是(0:表示‘[and|or]’),则字符串不符合标准
                    if (0 != prevSign) {
                        passSign = 'false';
                        return false;
                    }
                    prevSign = curSign;
                    break;
                case 3:
                    //当前[3:表示')'],如果上一字符串不是(1:表示‘[数字]’),则字符串不符合标准
                    if (1 != prevSign) {
                        passSign = 'false';
                        return false;
                    }
                    prevSign = curSign;
                    break;
                default:
                    //当前(0:表示‘[and|or]’),如果上一字符串不是[1:表示‘[数字]’,3:表示')'],则字符串不符合标准
                    if (1 != prevSign && 3 != prevSign) {
                        passSign = 'false';
                        return false;
                    }
                    prevSign = curSign;
            }
        }
    });
    return passSign;
}
/*整理字符串*/
function neaten(str)
{
    //整理字符串
    str = str.replace(/（/g, "(");
    str = str.replace(/）/g, ")");
    //将字符串转小写
    str = str.toLocaleLowerCase();
    //整理字符串去除空格
    str = str.replace(/\s+/g, "");
    return str;
}
/*重新组合字符串*/
function newStr(str) {
    var nStr = neaten(str);
    /*匹配'[数字]'和逻辑符括号*/
    var re = /(\[\d+\])|(and)|(or)|(AND)|(OR)|\(|\)|\w+|[\u4E00-\u9FA5\uF900-\uFA2D]/g;
    /*组合新的标准字符串*/
    var strTmp = '';
    var strArr = nStr.match(re);
    $.each(strArr, function (i, v) {
        if (/(\[\d+\])/.test(v)) {
            strTmp += v + ' ';
        } else if (/\(/.test(v)) {
            strTmp += v;
        } else if (/\)/.test(v)) {
            strTmp = strTmp.replace(/(^\s*)|(\s*$)/g, "");
            strTmp += v + ' ';
        } else {
            strTmp += v.toLocaleUpperCase() + ' ';
        }
    });
    return strTmp.replace(/(^\s*)|(\s*$)/g, "");
}
/*校验条件设置，转入、转出条件是否正确,如果正确就返回新的字符串*/
function rollInOut(id)
{
    var checkSign = 'true';
    var str = $('#' + id).val();
    //整理字符串
    str = neaten(str);
    /*递归检查*/
    checkSign = recursionBrackets(str);
    return checkSign;
}
/*递归检查*/
function recursionBrackets(str)
{
    var checkSign = 'true';
    /*反向查询,如果有非下面的字符出现则 return false 表示字符串不正确*/
    var re = /\[\d+\]|and|or|AND|OR|\(|\)/g;
    var s = str;
    s = s.replace(re, '');
    if (0 != s.length) {
        checkSign = 'false';
        return checkSign;
    }
    /*检查左括号数量*/
    var re = /\(/g;
    var lPair = new Array();
    lPair = str.match(re);
    lPair = lPair ? lPair.length : '';
    /*检查右括号数量*/
    var re = /\)/g;
    var rPair = new Array();
    rPair = str.match(re);
    rPair = rPair ? rPair.length : '';
    /*检查是否成对出现*/
    if (lPair != rPair) {
        checkSign = 'false';
        return checkSign;
    }
    /*无括号*/
    if (0 == lPair && 0 == rPair) {
        checkSign = checkStr(str, 0);
        return checkSign;
    }
    var re = /(\[\d+\])|(and)|(or)|(AND)|(OR)|\(|\)/g;
    var strArr = str.match(re);
    var rPairIterator = 0;
    var strTmp = '';
    var arr = new Array();
    $.each(strArr, function (i, v) {
        if (/\)/.test(v)) {
            rPairIterator++;
        }
        if (1 == rPairIterator) {
            rPairIterator = 0;
            var rArr = new Array();
            for (var ai = arr.length - 1; ai >= 0; ai--) {
                if (/\(/.test(arr[ai])) {
                    break;
                }
                rArr.push(arr[ai]);
            }
            for (var rai = rArr.length - 1; rai >= 0; rai--) {
                strTmp += rArr[rai];
            }
            strTmp = strTmp.replace(/(^\s*)|(\s*$)/g, "");
            checkSign = checkStr(strTmp, 0);
            if ('true' == checkSign) {
                str = str.replace('(' + strTmp + ')', '[0]');
                checkSign = recursionBrackets(str);
                if ('false' == checkSign) {
                    return false;
                }
            } else {
                return false;
            }
            strTmp = '';
        }
        arr.push(v);
    });
    return checkSign;
}
/*截取[数字]中的数字*/
function catOutDigit(str)
{
    var re = /(?!\[)\d+?(?=\])/g;
    var numArr = str.match(re);
    return numArr;
}
/*检查条件是否在tbody条件列表中*/
function existTbody(id, arr)
{
    var tdStr = '';
    var checkSign = 'true';
    $('#' + id).find('tr').filter(function (i) {
        tdStr += $(this).find('td').eq(0).text();
    });
    tdStr = tdStr.replace(/(^\s*)|(\s*$)/g, "");
    $.each(arr, function (i, v) {
        if (-1 === tdStr.indexOf(v)) {
            checkSign = 'false';
            return false;
        }
    });
    return checkSign;
}
/*流程步骤设置*/
$(function () {
    //autoBrowserHeight('nav-right-container', $(document.body).outerHeight(true)-100);
    var block = new Array();
    $.each($('#address li'), function () {
        if ('block' == $(this).css('display')) {
            block.push($(this).index());
        }
    });
    startIndex = block[0];
    popIndex = block.pop();
    //编辑步骤触发点击菜单栏
    var id_name = $('#id_name').val();
    if (id_name != '')
    {
        $("#" + id_name + "" + "_li").find('a').trigger('click');
        if (startIndex == $("#" + id_name + "" + "_li").index()) {
            $("#btn_prev").attr("disabled", "ture");
            $("#next_step").removeAttr("disabled");
        } else if (popIndex == $("#" + id_name + "" + "_li").index()) {
            $("#next_step").attr("disabled", "ture");
            $("#btn_prev").removeAttr("disabled");
        } else {
            $("#btn_prev").removeAttr("disabled");
            $("#next_step").removeAttr("disabled");
        }
    }

    //点击上一步
    prevStep('btn_prev', 'next_step');
    //点击下一步
    nextStep('btn_prev', 'next_step');

    //流程步骤保存
    $('#prcs_checkForm').on('click', function () {
        var check = $("#submit_check_form").val();
        var flow_type = $('#flow_type').val();//流程类型(1-固定流程,2-自由流程)
        var prcs_name = $("#prcs_name").val();//步骤名称

        var checkSign = 'true';
        var prcs_in_set = $('#prcs_in_set').val();
        var prcs_out_set = $('#prcs_out_set').val();
        if ('' != $('#prcs_in_set').val()) {
            //校验tbody条件列表中是否存在条件
            var strNum = catOutDigit(prcs_in_set);
            var eTb = existTbody('condition_in', strNum);
            if ('false' == eTb) {
                alert('转入条件公式中有不存在的条件,请检查转入条件列表');
                return false;
            }
            //校验条件设置，转入条件是否正确
            checkSign = rollInOut('prcs_in_set');
            if ('true' != checkSign) {
                alert('转入条件公式不正确！');
                return false;
            }
            $('#prcs_in_set').val(newStr(prcs_in_set));
        }
        if ('' != $('#prcs_out_set').val()) {
            //校验tbody条件列表中是否存在条件
            var strNum = catOutDigit(prcs_out_set);
            var eTb = existTbody('condition_out', strNum);
            if ('false' == eTb) {
                alert('转出条件公式中有不存在的条件,请检查转出条件列表');
                return false;
            }
            //校验条件设置，转出条件是否正确
            checkSign = rollInOut('prcs_out_set');
            if ('true' != checkSign) {
                alert('转出条件公式不正确！');
                return false;
            }
            $('#prcs_out_set').val(newStr(prcs_out_set));
        }
        var url = './submitFlowSteps';
        var data = $("#flow_step_define").serialize();
//----------------------------------------------------------下一步骤id获取 处理 start------------------------------
        var prcs_to = allField.next_steps('alternative_next');//下一步骤id 字符串

//--------------------------------------------------------下一步骤id字符串end--------------------------------------

//------------------------------------------------本步骤可写子段start ---------------------------------------
        var o_prcs_item_all = allField.fieldPermissions('writable_left_tbody');
        var prcs_item = o_prcs_item_all;
//-----------------------------------------------本步骤可写子段end----------------------------------------------

//------------------------------------------------本步骤保密子段start 
        var o_hidden_item_all = allField.fieldPermissions('h_next_step_tbody');
        var hidden_item = o_hidden_item_all;

//----------------------------------------------本步骤可保密段end------------------------------------------------------

//------------------------------------------------本步骤必填字段start -----------------------------------------
        var o_required_item_all = allField.fieldPermissions('r_next_step_tbody');
        var required_item = o_required_item_all;
//----------------------------------------------本步骤必填字段end---------------------------------------------

        if (check == 0) {
            alert("步骤id不能为空并且步骤id不能重复");
            return false;
        }

        if (prcs_name == '') {
            alert("步骤名称不能为空");
            return false;
        }
//        var prcs_user = $('#prcs_user').val();
//        var prcs_dept = $('#prcs_dept').val();
//        var prcs_priv = $('#prcs_priv').val();
//        if (flow_type == 1) {//流程类型(1-固定流程,2-自由流程)
//            if (prcs_user == '' && prcs_dept == '' && prcs_priv == '') {
//                alert('该流程为固定流程！请必须添加授权范围(人员或部门或角色)');
//                return false;
//            }
//        }

//智能选人 自动选人规则start
        var str = smart.auto_type();
        if (str == 'nullAll') {
            alert('请选择经办人');
            return false;
        }
//智能选人 自动选人规则start

//列表控件模式数据start
        var listCtrldata = listCtrl.listdata();
        
//列表控件模式数据end
        var data_next = "&prcs_to=" + prcs_to + "&prcs_item=" + prcs_item + "&hidden_item=" + hidden_item + "&required_item=" + required_item + "&list_ctrl=" + listCtrldata;
       
        $.ajax({
            type: 'post',
            url: url,
            data: data + data_next,
            headers: {
                'X-CSRF-TOKEN': $("meta[name='_token']").attr('content')
            },
            success: function (msg) {
                if (msg == 'error') {
                    alert("步骤id重复");
                } else if (msg == 'insertSuccess') {
                    alert("保存成功");
                    window.close();
                    window.opener.location.reload();
                } else if (msg == 'saveSuccess') {
                    alert("修改成功");
                    window.close();
                    window.opener.location.reload();
                } else if (msg == 'flow_type_error') {
                    alert('该流程为固定流程！请必须添加授权范围(人员或部门或角色)');
                } else {
                    alert("保存失败");
                }

            },
            error: function (error) {
                if (error.status == 422) {
                    alert("请你不要非法操作!");
                }
            }
        });
    });

//可写字段、保密字段、必填字段共用一个fieldPermissions方法 
//下一步骤id 字符串 next_steps方法
    var allField = {
        fieldPermissions: function (id_name) {
            var tr_arr = $("#" + id_name + " tr");
            var str = "";
            if (tr_arr.length > 0)
            {
                str = '{';
                $.each(tr_arr, function () {
                    var _this_val = $(this).find('td').text();
                    var _this_name = $(this).find('td').attr('name');

                    str += '"' + _this_name + '":' + '"' + _this_val + '"' + ',';
                });
                str = str.substring(0, str.length - 1);

                str += '}';
            }
            return str;
        },
        next_steps: function (id_name) {
            var prcs_to_arr = $("#" + id_name + " tr");
            var prcs_to = '';
            $.each(prcs_to_arr, function () {
                var _this_val = $(this).find('td').text();
                var str = new Array();
                str = _this_val.split(':');//分割字符串
                var _this_id = str[0];
                prcs_to += _this_id + ',';
            });
            prcs_to = prcs_to.substring(0, prcs_to.length - 1);//转交步骤id串
            return prcs_to;
        }
    };

    //判断点击哪一个选项卡
    $('#address li').on('click', function () {
        if ('' == $('#prcs_name').val())
        {
            alert('[步骤名称不能为空]');
            //$(this).find('a').css('background-color','#ffffff');
            //$(this).find('a').removeAttr('style');
            // $(this).hover(function(){
            //     $(this).find('a').css('background-color','#f5f5f5');
            // },function(){
            //     $(this).find('a').css('background-color','#ffffff');
            // });
            return false;
        }
        // else
        // {
        //     $(this).find('a').css('background-color','#0088cc');
        //     //return false;
        // }
        if (startIndex == $(this).index())
        {
            $("#btn_prev").attr("disabled", "ture");
            $("#next_step").removeAttr("disabled");
        } else if (popIndex == $(this).index())
        {
            $("#next_step").attr("disabled", "ture");
            $("#btn_prev").removeAttr("disabled");
        } else
        {
            $("#btn_prev").removeAttr("disabled");
            $("#next_step").removeAttr("disabled");
        }
    });
    //hover还原样式
    // if(''==$('#prcs_name').val())
    // {
    //     $('#address li').hover(function(){
    //         if('active' != $(this).attr('class'))
    //         {
    //             $(this).find('a').css('background-color','#f5f5f5');
    //         }
    //     },function(){
    //         if('active' != $(this).attr('class'))
    //         {
    //             $(this).find('a').css('background-color','#ffffff');
    //         }
    //     });
    // }
});


//初始化备选字段
//vessel_id:盛装容器id
function alternativeListInit(vessel_id) {
    var url = "./stepsWritableTemplate";
    var jsonstr = '{"data_1":"1","data_2":"2","data_3":"3"}';
    var t = eval('(' + jsonstr + ')');
    var data = {'flow_id': $('#flow_id').val(), '_token': token};
    $.ajax({
        type: 'post',
        url: url,
        dataType: 'json',
        data: data,
        async: false,
        success: function (msg) {
            $.each(msg, function (i) {
                var str = "";
                if (msg[i].checkboxs)
                {
                    //str += '<td class="step">' + msg[i].title + '</td>';
                    $.each(msg[i].checkboxs, function (k) {
                        str = '<tr style="cursor: pointer;">';
                        str += '<td class="step"  name="' + msg[i].checkboxs[k].name + '">' + msg[i].checkboxs[k].value + '</td>';
                        str += '</tr>';
                    });
                } else
                {
                    str = '<tr style="cursor: pointer;">';
                    str += '<td class="step"  name="' + msg[i].name + '">' + msg[i].title + '</td>';
                    str += '</tr>';
                }
                $('#' + vessel_id).append(str);
            });
        }
    });
}

//全选
function selectAll(self, select_id) {
    var select_tr = $('#' + select_id).find('tr');
    if (0 == select_tr.length)
    {
        return false;
    }
    if ('全选' == $('#' + self.id).text())
    {
        $.each(select_tr, function () {
            $(this).addClass('ui-selected');
        });
        $('#' + self.id).text('取消');
    } else
    {
        $.each(select_tr, function () {
            $(this).removeClass('ui-selected');
        });
        $('#' + self.id).text('全选');
    }
}
//左右移动
function selectPlug(self, move_id, receive, but_id) {
    var move_tr = $('#' + move_id).find('tr');
    if (move_tr.length > 0)
    {
        $.each(move_tr, function () {
            if (-1 != String($(this).attr('class')).indexOf('ui-selected'))
            {
                var str = '<tr style="cursor: pointer;">' +
                        $(this).html() +
                        '</tr>';

                $('#' + receive).append(str);
                //动态绑定事件
                bClick(receive);
                $(this).remove();
            }
        });
        if ($('#' + move_id).find('tr').length == 0)
        {
            $('#' + but_id).text('全选');
        }
    }
    init_listCtrl();//可写字段 列表控件模式 的权限
}
//动态绑定点击事件
function bClick(parent_id)
{
    $('#' + parent_id).on('click', 'tr', function () {
        event.stopPropagation();//阻止冒泡
        $.each($("#" + parent_id + " tr"), function (i, v) {
            $(this).removeClass('ui-selected');
        });
        $(this).addClass('ui-selected');
    });
}
//上一步
function prevStep(prev_id, next_id) {
    $('#' + prev_id).on('click', function () {
        var li = $('#address li');
        $("#" + next_id).removeAttr("disabled");
        var obj = new Array();
        $.each(li, function () {
            obj[$(this).index()] = $(this);
        });
        var newObj = new Array();
        var i = 0;
        $.each(obj, function () {
            if ('block' == $(this).css('display'))
            {
                newObj[i] = $(this);
                i++;
            }
        });
        var rev_obj = newObj.reverse();
        var i = 0;
        for (var key in rev_obj)
        {
            if ('active' == String(rev_obj[key].attr('class')))
            {
                i = 1;
                continue;
            }
            if (1 == i)
            {
                $('#' + rev_obj[key][0].id).find('a').trigger('click');
                if (rev_obj.length - 1 == key)
                {
                    $("#" + prev_id).attr("disabled", "ture");
                }
                return false;
            }
        }
    });
}
//下一步
function nextStep(prev_id, next_id) {
    $('#' + next_id).on('click', function () {
        if ('' == $('#prcs_name').val())
        {
            alert('[步骤名称不能为空]');
            return false;
        }
        var li = $('#address li');
        $("#" + prev_id).removeAttr("disabled");
        var obj = new Array();
        $.each(li, function () {
            if ('block' == $(this).css('display'))
            {
                obj[$(this).index()] = $(this);
            }
        });
        $.each(li, function () {
            if (li.length - 1 != $(this).index() && String('active') == $(this).attr('class'))
            {
                var nextAll = $(this).nextAll('li');
                $.each(nextAll, function () {
                    if ('block' == $(this).css('display'))
                    {
                        $('#' + this.id).find('a').trigger('click');
                        if (obj.pop().index() == $(this).index())
                        {
                            $("#" + next_id).attr("disabled", "ture");
                        }
                        return false;
                    }
                });
                return false;
            }
        });
    });
}
//自动适用浏览器高度
function autoBrowserHeight(id, browserHeight) {
    $('#' + id).css('height', browserHeight);
}
