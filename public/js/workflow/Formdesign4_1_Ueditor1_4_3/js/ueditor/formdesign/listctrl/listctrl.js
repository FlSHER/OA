function createElement(type, name)
{
    var element = null;
    try {
        element = document.createElement('<' + type + ' name="' + name + '">');
    } catch (e) {
    }
    if (element == null) {
        element = document.createElement(type);
        element.name = name;
    }
    return element;
}

var oNode = null, thePlugins = 'listctrl';
var rows_count = 5;
var adefaultDatatype = ['text', 'textarea', 'int', 'calc', 'select'];

window.onload = function () {
    db_connection_data_init();
    //initData();
    //合计，强制选择 int
    csum_checkBox();
}

//合计，强制选择 int
function csum_checkBox() {
    $(".csum").click(function () {
        if ($(this).attr("checked"))
        {
            var i = $(this).val();
            $("#coltype_" + i).val('int');
        }
    });
}

//弹出窗口初始化函数，这里主要是判断是编辑下拉列表还是新增
function initData() {
    if (UE.plugins[thePlugins].editdom) {
        oNode = UE.plugins[thePlugins].editdom;
        $G('orgname').value = oNode.getAttribute('title');
        var gWidth = oNode.getAttribute('orgwidth');
        var gTitle = oNode.getAttribute('orgtitle'),
                gFields = oNode.getAttribute('orgfields'),
                gColType = oNode.getAttribute('orgcoltype'),
                // gUnit = oNode.getAttribute('orgunit'),
                colwidthTmp = oNode.getAttribute('colwidth'),
                gSum = oNode.getAttribute('orgsum'), //合计
                gColValue = oNode.getAttribute('orgcolvalue'),
                gEquation = oNode.getAttribute('equation');//计算公式
        //gSelectCheckbox = oNode.getAttribute('selectcheckbox');
        $G('isAddtable').value = oNode.getAttribute('isaddtable');
        $G('source').value = oNode.getAttribute('source');
        $('#source').trigger('change');
        $G('dataSource').value = oNode.getAttribute('datasource');
        $G('rowNum').value = oNode.getAttribute('rownum');
        var aTitle = gTitle.split('`'),
                aColType = gColType ? gColType.split('`') : null,
                // aUnit = gUnit ? gUnit.split('`') : null,
                aSum = gSum ? gSum.split('`') : null,
                aColValue = gColValue ? gColValue.split('`') : null,
                aEquation = gEquation ? gEquation.split('`') : null,
                aFields = gFields ? gFields.split('`') : null,
                colwidthTmp = colwidthTmp ? colwidthTmp.split('`') : null;
        //aSelectCheckbox = gSelectCheckbox ? gSelectCheckbox.split('`') : null;
        $G('orgwidth').value = gWidth;
        rows_count = aTitle.length - 1;
        for (var i = 0; i < aTitle.length - 1; i++) {
            var str = createEle(i);
            $('#tbl1').append(str);
            buildClick($('#tbl1'));
            var sItem = 'item_' + (i + 1),
                    sColtype = 'coltype_' + (i + 1),
                    sUnit = 'unit_' + (i + 1),
                    sNum = 'sum_' + (i + 1),
                    sColValue = 'colvalue_' + (i + 1),
                    sEquation = 'equation_' + (i + 1),
                    colwidth = 'colwidth_' + (i + 1)
            sFields = 'field_' + (i + 1);
            //sSelectCheckbox = 'select_checkbox_' + (i + 1);
            $G(sItem).value = aTitle[i];
            if (gColType) {
                $('#' + sColtype).val(aColType[i]);
            }
            if ('text' != aColType[i] && 'int' != aColType[i]) {
                // $G(sUnit).value = '';
                // $G(sUnit).setAttribute('disabled','disabled');
                $G(sNum).checked = false;
                $G(sNum).setAttribute('disabled', 'disabled');
                $G(sEquation).value = '';
                $G(sEquation).setAttribute('disabled', 'disabled');
            } else {
                // $G(sUnit).value = aUnit[i];
                if (gSum) {
                    $G(sNum).checked = aSum[i] == 1 ? true : false;
                }
                if (gEquation) {//计算公式
                    $G(sEquation).value = aEquation[i];
                }
            }
            if (colwidthTmp) {
                $G(colwidth).value = colwidthTmp[i];
            }
            if (gColValue) {
                $G(sColValue).value = aColValue[i];
            }
            if (aFields) {
                $G(sFields).value = aFields[i];
            }
            // if (gSelectCheckbox) {
            //      $G(sSelectCheckbox).checked = aSelectCheckbox[i] == 1 ? true : false;
            // }
        }
        //选择数据表时 展示数据字段 和 查询
        dataScour($('#dataSource'));
        retuEitdom();
    }
    //没有tr标签时自动创建5个
    if (0 == $('#tbl1 tr').length)
    {
        for (var i = 0; i < rows_count; i++) {
            var str = createEle(i);
            $('#tbl1').append(str);
        }
    }
}
//    //获取数据来源 数据库数据类型数据
function db_connection_data_init() {
    $.ajax({
        type: 'get',
        url: '/workflow/dbConnectionInfo',
        dataType: 'json',
        asyns: false,
        success: function (data) {
            var str = '';
            str = '<option value="">选择数据来源</option>';
            $.each(data, function (k, v) {
                str += '<option value="' + v.id + '">' + v.database + '</option>';
            });
            $('#source').html(str);
            initData();
        }
    });
}
dialog.oncancel = function () {
    if (UE.plugins[thePlugins].editdom) {
        delete UE.plugins[thePlugins].editdom;
    }
};
dialog.onok = function () {
    var gName = $G('orgname').value.replace(/\"/g, "&quot;"), gWidth = $G('orgwidth').value;
    var checkSign = 'true';
    if (gName == '') {
        alert('控件名称不能为空');
        $G('orgname').focus();
        return false;
    }

    var gTitle = '', gColType = '', gUnitValue = '', gSum = '', gColValue = '', isAddTable = '',
            nCount = 0, source = '', dataSource = '', rowNum = '', dbFields = '', selectCheckbox = '',
            equation = '', colwidth = '', fields = '';

    for (var i = 1; i <= rows_count; i++) {
        var oItem = $G("item_" + i),
                oSum = $G('sum_' + i), oColType = $G('coltype_' + i),
                oColValue = $G('colvalue_' + i), /*oUnit = $G('unit_' + i),*/
                addTableTep = $G('isAddtable'), sourcetmp = $G('source'),
                dataSourceTmp = $G('dataSource'), rowNumTmp = $G('rowNum'),
                dbFieldsTmp = $G('fields_all_' + i), selectCheckboxTmp = $G('select_checkbox_' + i),
                equationTmp = $G('equation_' + i),
                colwidthTmp = $G('colwidth_' + i),
                fieldsTmp = $G('field_' + i);
        if (oItem.value != '') {
            if (gTitle.indexOf(oItem.value + '`') !== -1)
            {
                continue;//重复
            }
            gTitle += oItem.value + '`'; //表头
            nCount++;
            if (oSum.checked) { //合计
                gSum += '1`';
            } else {
                gSum += '0`';
            }
            gColType += oColType.value + '`';
            if ('int' == oColType.value && /[^\d+]/.test(oColValue.value)) {
                alert('表头(' + oItem.value + ")默认值与类型不符,请输入数字。");
                return false;
            }
            oColValue.value = (oColValue.value).replace(/，/g, ",");
            gColValue += oColValue.value + '`';
            // gUnitValue += oUnit.value + '`';
            isAddTable = addTableTep.value;
            source = sourcetmp.value;
            dataSource = dataSourceTmp.value;
            rowNum = rowNumTmp.value;
            if(dbFieldsTmp){
                dbFields += dbFieldsTmp.value + '`';//数据库字段
            }
            if(selectCheckboxTmp){
                if (selectCheckboxTmp.checked) {//查询
                    selectCheckbox += '1`';
                } else {
                    selectCheckbox += '0`';
                }
            }
            if ('' != equationTmp.value) {
                checkSign = checkFormula(equationTmp.value);
                if ('false' == checkSign) {
                    alert("计算公式不正确");
                    return false;
                }
                checkSign = isExeist(equationTmp.value, rows_count);
                if ('false' == checkSign) {
                    alert("公式中有不存在的序号");
                    return false;
                }
            }
            equation += equationTmp.value + '`';//计算公式
            colwidth += colwidthTmp.value + '`';
            fields += fieldsTmp.value + '`';
        }
    }

    if (nCount == 0) {
        alert("表头项目不能为空");
        return false;
    }
    //验证数据字段不能全部为空start
    var fields_length = $('.fields_all').length;
    var fields_value = false;
    if(0 < fields_length){
        $.each($('.fields_all'), function () {
            var value = $(this).val();
            if (value != 0) {
                fields_value = true;
            }
        });

        if (fields_value == false) {
            alert('请最少选择一个数据字段');
            return false;
        }
    }
    
    //验证数据字段不能全部为空end
    if (!oNode) {
        try {
            oNode = createElement('input', 'leipiNewField');
            oNode.setAttribute('leipiPlugins', thePlugins);
            oNode.setAttribute('type', 'text');
            oNode.setAttribute('value', '{列表控件}');
            oNode.setAttribute('readonly', 'readonly');
            oNode.setAttribute('title', gName);
            oNode.setAttribute('orgtitle', gTitle);
            oNode.setAttribute('orgcoltype', gColType);
            oNode.setAttribute('orgunit', gUnitValue);
            oNode.setAttribute('orgsum', gSum);
            oNode.setAttribute('orgcolvalue', gColValue);
            oNode.setAttribute('isaddtable', isAddTable);
            oNode.setAttribute('source', source);
            oNode.setAttribute('datasource', dataSource);
            oNode.setAttribute('rownum', rowNum);
            oNode.setAttribute('dbfields', dbFields);
            oNode.setAttribute('selectcheckbox', selectCheckbox);
            oNode.setAttribute('equation', equation);//计算公式
            if (gWidth != '') {
                oNode.style.width = gWidth;
            }
            oNode.setAttribute('orgwidth', gWidth);
            oNode.setAttribute('colwidth', colwidth);
            oNode.setAttribute('orgfields', fields);
            editor.execCommand('insertHtml', oNode.outerHTML);
            return true;
        } catch (e) {
            try {
                editor.execCommand('error');
            } catch (e) {
                alert('控件异常，请到 [雷劈网] 反馈或寻求帮助！');
            }
            return false;
        }
    } else {
        //修改
        oNode.setAttribute('leipiPlugins', thePlugins);
        oNode.setAttribute('title', gName);
        oNode.setAttribute('orgtitle', gTitle);
        oNode.setAttribute('orgcoltype', gColType);
        oNode.setAttribute('orgunit', gUnitValue);
        oNode.setAttribute('orgsum', gSum);
        oNode.setAttribute('orgcolvalue', gColValue);
        oNode.setAttribute('isaddtable', isAddTable);
        oNode.setAttribute('source', source);
        oNode.setAttribute('datasource', dataSource);
        oNode.setAttribute('rownum', rowNum);
        if (gWidth != '') {
            oNode.style.width = gWidth;
        } else
        {
            oNode.style.width = '';
        }
        oNode.setAttribute('orgwidth', gWidth);
        oNode.setAttribute('colwidth', colwidth);
        oNode.setAttribute('orgfields', fields);
        oNode.setAttribute('dbfields', dbFields);
        oNode.setAttribute('selectcheckbox', selectCheckbox);
        oNode.setAttribute('equation', equation);//计算公式
        delete UE.plugins[thePlugins].editdom; //使用后清空这个对象，变回新增模式
    }
};

//数据来源(点击当前数据数据库类型)
$('#source').change(function () {
    var sourceVal = $(this).val();
    if(sourceVal !=''){
        getDataTable(sourceVal);//根据当前id获取当前数据库表
    }else{  
    //$('#tbl1 tr td').find('.fields_all').html('');
    }
});
//点击当前数据表获取table表格的数据字段
$('#dataSource').change(function () {
    var _that = $(this);
    dataScour(_that);

});
$(function () {
    $('.delteTrs').each(function () {
        $(this).click(function () {
            $(this).parent().parent().remove();
        });
    });
});

/**添加**/
function add_row() {
    rows_count += 1;
    var num = $("#tbl tbody tr").length;
    var trs = $("#tbl tbody");
    var str = "";
    var selectFieldId = '';
    for (var i = 0; i < num + 1; i++) {
        if ($('#dataSource').val() == 1) {
            str = createEle(i);
        } else {
            str = '<tr><td><a title="删除" class="delteTrs"  onclick="delete_row(this)">X</a></td><td title="序号"><span class="badge">' + parseInt(i + 1) + '</span></td>'
                    + '<td title="表头"> <input id="item_' + parseInt(i + 1) + '"  type="text"></td>'
                    + '<td title="字段名"><input id="field_' + parseInt(i + 1) + '"  type="text"></td>'
                    + '<td title="列宽度"><input id="colwidth_' + parseInt(i + 1) + '"  type="text" value="10">px</td>'
                    + '<td title="类型">'
                    + '<select id="coltype_' + parseInt(i + 1) + '">'
                    + '<option value="text">单行输入框</option>'
                    + '<option value="textarea">多行输入框</option>'
                    + '<option value="select">下拉菜单</option>'
                    + '<option value="radio">单选框</option>'
                    + '<option value="checkbox">复选框</option>'
                    + '<option value="date">日期</option>'
                    + '<option value="datetime">日期+时间</option>'
                    + '<option value="int">数值</option></select></td>'
                    // + '<td title="单位"> <label><input type="text" class="input-mini" id="unit_' + parseInt(i + 1) + '" value=""> </label> </td>'
                    + '<td title="合计"> <label> <input type="checkbox" id="sum_' + parseInt(i + 1) + '" class="csum" value=""> </label> </td>'
                    + '<td title="计算公式"> <label> <input type="text" id="equation_' + parseInt(i + 1) + '" value=""> </label> </td>'
                    + '<td title="默认值"><input id="colvalue_' + parseInt(i + 1) + '"  type="text"/></td>'
                    + '<td><select id="fields_all_' + parseInt(i + 1)
                    + '"><option>单件</option></select></td><td><input id="select_checkbox_' + parseInt(i + 1)
                    + '"type="checkbox" value=""/></td></tr>';
            selectFieldId = 'fields_all_' + parseInt(i + 1);
        }
    }
    trs.append(str);
    dataScour($('#dataSource'), selectFieldId);
    buildClick(trs);
}
//click事件
function buildClick(obj) {
    var trs = obj.find('tr');
    trs.each(function (i, v) {
        var trThis = $(this);
        var selectTags = trThis.find('select[id^="coltype_"]');
        selectTags.change(function () {
            if ('text' != $(this).val() && 'int' != $(this).val()) {
                // trThis.find('input[id^="unit_"]').val('');
                // trThis.find('input[id^="unit_"]').attr('disabled','disabled');
                trThis.find('input[id^="sum_"]').prop("checked", false);
                trThis.find('input[id^="sum_"]').prop('disabled', 'disabled');
                trThis.find('input[id^="equation_"]').val('');
                trThis.find('input[id^="equation_"]').attr('disabled', 'disabled');
            } else {
                // trThis.find('input[id^="unit_"]').removeAttr('disabled');
                trThis.find('input[id^="sum_"]').removeAttr('disabled');
                trThis.find('input[id^="equation_"]').removeAttr('disabled');
            }
        });
    });
}
function delete_row(row) {
    rows_count -= 1;
    $(row).parent().parent().remove();
    var num = $("#tbl tbody tr").length;
    var trs = $("#tbl tbody tr");
    for (i = 0; i < num; i++) {
        if ($('#dataSource').val() == 1) {
            trs.find("td:eq(1)").each(function (i) {
                $(this).find('span').text(i + 1);
            });
            trs.find("td:eq(2)").each(function (i) {
                $(this).find('input').attr('id', 'item_' + parseInt(i + 1));
            });
            trs.find("td:eq(3)").each(function (i) {
                $(this).find('input').attr('id', 'field_' + parseInt(i + 1));
            });
            trs.find("td:eq(4)").each(function (i) {
                $(this).find('input').attr('id', 'colwidth_' + parseInt(i + 1));
            });
            trs.find("td:eq(5)").each(function (i) {
                $(this).find('select').attr('id', 'coltype_' + parseInt(i + 1));
            });
            // trs.find("td:eq(3)").each(function (i) {
            //     $(this).find('input').attr('id', 'unit_' + parseInt(i + 1));
            // });
            trs.find("td:eq(6)").each(function (i) {
                $(this).find('input').attr('id', 'sum_' + parseInt(i + 1));
            });
            trs.find("td:eq(7)").each(function (i) {
                $(this).find('input').attr('id', 'equation_' + parseInt(i + 1));
            });
            trs.find("td:eq(8)").each(function (i) {
                $(this).find('input').attr('id', 'colvalue_' + parseInt(i + 1));
            });
        } else {
            trs.find("td:eq(1)").each(function (i) {
                $(this).find('span').text(i + 1);
            });
            trs.find("td:eq(2)").each(function (i) {
                $(this).find('input').attr('id', 'item_' + parseInt(i + 1));
            });
            trs.find("td:eq(3)").each(function (i) {
                $(this).find('input').attr('id', 'field_' + parseInt(i + 1));
            });
            trs.find("td:eq(4)").each(function (i) {
                $(this).find('input').attr('id', 'colwidth_' + parseInt(i + 1));
            });
            trs.find("td:eq(5)").each(function (i) {
                $(this).find('select').attr('id', 'coltype_' + parseInt(i + 1));
            });
            // trs.find("td:eq(3)").each(function (i) {
            //     $(this).find('input').attr('id', 'unit_' + parseInt(i + 1));
            // });
            trs.find("td:eq(6)").each(function (i) {
                $(this).find('input').attr('id', 'sum_' + parseInt(i + 1));
            });
            trs.find("td:eq(7)").each(function (i) {
                $(this).find('input').attr('id', 'equation_' + parseInt(i + 1));
            });
            trs.find("td:eq(8)").each(function (i) {
                $(this).find('input').attr('id', 'colvalue_' + parseInt(i + 1));
            });
            trs.find("td:eq(9)").each(function (i) {
                $(this).find('select').attr('id', 'fields_all_' + parseInt(i + 1));
            });
            trs.find("td:eq(10)").each(function (i) {
                $(this).find('input').attr('id', 'select_checkbox_' + parseInt(i + 1));
            });
        }
    }
}

//封装内部数据选择表 数据表 填入表格中添加字段 ，查询
function dataScour(obj, selectFieldId) {
    var addThs = $('#tbl thead tr'),
        addTrs = $('#tbl1 tr');
    if (obj.val() == 1) {
        $('#tbl').css('width', '98%');
        if (addThs.find('th:last').text() == '查询') {
            var thdel = addThs.find('th'),
                    thLast = thdel.eq(thdel.length - 2),
                    thLastSec = thdel.eq(thdel.length - 1);
            thLast.remove();
            thLastSec.remove();
            addTrs.each(function () {
                var tddel = $(this).find('td'),
                        tdLast = tddel.eq(tddel.length - 2),
                        tdLastSec = tddel.eq(tddel.length - 1);
                tdLast.remove();
                tdLastSec.remove();
            });
        }
        return false;
    } else {
        if (addThs.find('th:last').text() == '查询') {
            var table = obj.val();
            getField(table, selectFieldId);//获取数据表的子段数
            return false;
        }
        $('#tbl').css('width', '98%');
        addThs.append('<th >数据库字段</th><th>查询</th>');
        addTrs.each(function (k, v) {
            $(this).append('<td><select class="fields_all" id="fields_all_' + parseInt(k + 1) + '"><option value="0">请选择数据库字段</option></select></td><td><input id="select_checkbox_' + parseInt(k + 1) + '" type="checkbox" value=""/></td></th>');
        });
        var table = obj.val();
        getField(table, null);//获取数据表的子段数
    }
}
//数据来源 

    




//数据来源 选择数据来源 得到数据表
function getDataTable(id) {
    $.ajax({
        type: 'get',
        url: "/workflow/getInternalDataTable",
        data:{id:id},
        dataType: 'json',
        async: false,
        success: function (data) {
            var str = '';
            $.each(data, function (k, v) {
                str += '<option value="' + v + '">' + v + '</option>';
            });
            $('#dataSource').find('optgroup:first').html(str);
        },
        error:function(msg){
            $('#dataSource').find('optgroup:first').html('');
        }
    });
}

//编辑回填数据表 (数据库字段，查询 回值)
function retuEitdom() {
    if (UE.plugins[thePlugins].editdom) {
        oNode = UE.plugins[thePlugins].editdom;
        $G('dataSource').value = oNode.getAttribute('datasource');
        var gTitle = oNode.getAttribute('orgtitle');
        var gDbFields = oNode.getAttribute('dbfields'),
                gSelectCheckbox = oNode.getAttribute('selectcheckbox');
        var aTitle = gTitle.split('`'),
                aDbFields = gDbFields ? gDbFields.split('`') : null,
                aSelectCheckbox = gSelectCheckbox ? gSelectCheckbox.split('`') : null;
        for (var i = 0; i < aTitle.length - 1; i++) {
            var sDbfields = 'fields_all_' + (i + 1),
                    sSelectCheckbox = 'select_checkbox_' + (i + 1);
            if (gDbFields) {
                $('#' + sDbfields).val(aDbFields[i]);
            }
            if (gSelectCheckbox) {
                $G(sSelectCheckbox).checked = aSelectCheckbox[i] == 1 ? true : false;
            }
        }
    }
}

//点击当前表时 获取该表的字段
function getField(table, selectFieldId) {
    var db_connection_id = $('#source').val();
    $.ajax({
        type: 'get',
        url: "/workflow/getInternalDataField",
        data: {table: table,id:db_connection_id},
        async: false,
        dataType: 'json',
        success: function (data) {
            var str = '';
            str = '<option value="0">请选择数据库字段</option>';
            $.each(data, function (k, v) {
                str += '<option value="' + v + '">' + v + '</option>';
            });
            if (selectFieldId != null) {
                $('#' + selectFieldId).html(str);
            } else {
                $('#tbl1 tr td').find('.fields_all').html(str);
            }
            //console.log($('#tbl1 tr td').eq(6)[0]);
        }
    });
}

//封装 ==1 html element
function createEle(i) {
    var str = '<tr><td><a title="删除" class="delteTrs"  onclick="delete_row(this)">X</a></td><td title="序号"><span class="badge">' + parseInt(i + 1) + '</span></td>'
            + '<td title="表头"> <input id="item_' + parseInt(i + 1) + '"  type="text"></td>'
            + '<td title="字段名"><input id="field_' + parseInt(i + 1) + '"  type="text"></td>'
            + '<td title="列宽度"><input id="colwidth_' + parseInt(i + 1) + '"  type="text" value="10">px</td>'
            + '<td title="类型">'
            + '<select id="coltype_' + parseInt(i + 1) + '">'
            + '<option value="text">单行输入框</option>'
            + '<option value="textarea">多行输入框</option>'
            + '<option value="select">下拉菜单</option>'
            + '<option value="radio">单选框</option>'
            + '<option value="checkbox">复选框</option>'
            + '<option value="date">日期</option>'
            + '<option value="datetime">日期+时间</option>'
            + '<option value="int">数值</option></select></td>'
            // + '<td title="单位"> <label><input type="text" class="input-mini" id="unit_' + parseInt(i + 1) + '" value=""> </label> </td>'
            + '<td title="合计"> <label> <input type="checkbox" id="sum_' + parseInt(i + 1) + '" class="csum" value=""> </label> </td>'
            + '<td title="计算公式"> <label> <input type="text" id="equation_' + parseInt(i + 1) + '" value=""> </label> </td>'
            + '<td title="默认值"><input id="colvalue_' + parseInt(i + 1) + '"  type="text" /></td>' + '</tr>';
    return str;
}
//隐藏显示说明
function tips() {
    if ('none' == $('#cal_tip').css('display')) {
        $('#cal_tip').css('display', 'block');
    } else {
        $('#cal_tip').css('display', 'none');
    }
}
//检查字符串
function checkFormula(str) {
    var checkSign = 'true';
    str = neatenStr(str);
    checkSign = illicitumStr(str);
    if ('true' != checkSign) {
        return checkSign;
    }
    checkSign = checkBrackets(str);
    if ('true' != checkSign) {
        return checkSign;
    }
    var braRe = /\(|\)/g;
    if (!braRe.test(str)) {
        checkSign = checkStrOrder(str);
        return checkSign;
    }
    var re = /\[\d+\]|\+|\-|\*|\/|\%|(\|\d+)|\(|\)/g;
    var strArr = str.match(re);
    var rightBktRe = /\)/;
    var arr = new Array();
    var strTmp = '';
    $.each(strArr, function (i, v) {
        if (/\)/.test(v)) {
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
            checkSign = checkStrOrder(strTmp);
            if ('true' == checkSign) {
                str = str.replace('(' + strTmp + ')', '[0]');
                checkSign = checkFormula(str);
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
//检查是否存在
function isExeist(str, rownum) {
    var checkSign = 'true';
    var re = /\[\d+\]/g;
    var rd = /\d+/;
    var strArr = str.match(re);
    $.each(strArr, function (i, v) {
        if (parseInt(v.match(rd)[0]) < 1 || parseInt(v.match(rd)[0]) > parseInt(rownum)) {
            checkSign = 'false';
            return false;
        }
    });
    return checkSign;
}
//检查非法字符串
function illicitumStr(str) {
    var checkSign = 'true';
    var re = /\[\d+\]|\+|\-|\*|\/|\%|(\|\d+)|\(|\)/g;
    str = str.replace(re, '');
    if (0 != str.length) {
        checkSign = 'false';
    }
    return checkSign;
}
//检查括号是否正确
function checkBrackets(str) {
    var checkSign = 'true';
    var re = /\(|\)/g;
    if (re.test(str)) {
        var liftBktRe = /\(/g;
        var rightBktRe = /\)/g;
        var liftBkt = str.match(liftBktRe);
        var rightBkt = str.match(rightBktRe);
        if (!liftBkt) {
            liftBkt = new Array();
        }
        if (!rightBkt) {
            rightBkt = new Array();
        }
        if (liftBkt.length != rightBkt.length) {
            checkSign = 'false';
        }
    }
    return checkSign;
}
/*整理字符串*/
function neatenStr(str)
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
//检查字符串顺序
function checkStrOrder(str) {
    var checkSign = 'true';
    var re = /\[\d+\]|\+|\-|\*|\/|\%|(\|\d+)/g;
    var strArr = str.match(re);
    var strArrTmp = strArr;
    var prevSign = -1;
    if (!(/(\[\d+\])/.test(strArrTmp.shift()))) {
        checkSign = 'false';
        return checkSign;
    }
    var strArrTmpPop = strArrTmp.pop();
    if (!(/(\[\d+\])|(\|\d+)/.test(strArrTmpPop))) {
        checkSign = 'false';
        return checkSign;
    }
    var obligateDecimals = str.match(/(\|\d+)/g);
    if (obligateDecimals && obligateDecimals.length > 1) {
        checkSign = 'false';
        return checkSign;
    }
    if (obligateDecimals && 1 == obligateDecimals.length && !(/(\|\d+)/.test(strArrTmpPop))) {
        checkSign = 'false';
        return checkSign;
    }
    $.each(strArr, function (i, v) {
        if (/(\[\d+\])/.test(v)) {
            var curSign = 1;
        } else if (/\+|\-|\*|\/|\%/.test(v)) {
            var curSign = 2;
        } else if (/(\|\d+)/.test(v)) {
            var curSign = 3;
        }
        if (0 == i) {
            prevSign = curSign;
        } else {
            switch (curSign) {
                case 1:
                    if (2 != prevSign) {
                        checkSign = 'false';
                        return false;
                    }
                    prevSign = curSign;
                    break;
                case 2:
                    if (1 != prevSign && 3 != prevSign) {
                        checkSign = 'false';
                        return false;
                    }
                    prevSign = curSign;
                    break;
                default:
                    if (1 != prevSign) {
                        checkSign = 'false';
                        return false;
                    }
                    prevSign = curSign;
            }
        }
    });
    return checkSign;
}