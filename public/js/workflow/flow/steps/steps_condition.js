//条件生成器
// 条件点击
function change_condition(value) {
    if (value == '=' || value == '<>') {
        $("#div_check_estimate").show();
        if ($("#check_type").is(':checked')) {
            $('#div_type').show();
            $('#div_value').hide();
        } else {
            $('#div_type').hide();
            $('#div_value').show();
        }
    } else {
        $("#div_check_estimate").hide();
        $('#div_type').hide();
        $('#div_value').show();
    }
}
//类型判断点击
function change_type(t) {
    if ($(t).is(':checked')) {
        $('#div_type').show();
        $('#div_value').hide();
    } else {
        $('#div_type').hide();
        $('#div_value').show();
    }
}

//添加转入条件  和  添加转出条件   num  0转入 1转出
function add_condition(num) {
    var field = $("#condition_item_name").val();
    var names = $("#condition_item_name option:selected").attr('names');
    var where = $("#condition_where").val();
    var value = $('#condition_item_value').val();
    if ($('#check_type').is(':checked')) {
        if (where === '=') {
            where = '==';
        } else if (where === '<>') {
            where = '!==';
        }
        
        if($('#condition_where').val() === '=' || $('#condition_where').val() === '<>'){
            value = $('#item_type').val();
        }else{
            value = $('#condition_item_value').val();
        }
        
    }
    if(value ==''){
        alert('值不能为空');
        return false;
    }
    var str = "'" + field + "'" + where + "'" + value + "'";
    var msg = '';
    var par_id;
    var hide_id;
    if (num === 0) {
        var tr_arr = $("#condition_in tr");
        par_id = 'condition_in';
        hide_id = 'prcs_in';
        
    } else {
        var tr_arr = $("#condition_out tr");
        par_id = 'condition_out';
        hide_id = "prcs_out";
    }
    if (tr_arr.length >= 1) {
        $.each(tr_arr, function () {
            var tr_val = $(this).find('td').eq(1).find('span').text();
            if (tr_val === str) {
                msg = 1;
            }
        });
    }
    if (msg === 1) {
        alert('条件重复');
        return false;
    }
    var tr = '<tr names='+names+'>\n\
                    <td></td>\n\
                    <td><span>' + str + '</span></td>\n\
                    <td>\n\
                            <a href="javascript:void(0)" onclick="edit_condition(this)">编辑</a>\n\
                             <a href="javascript:void(0);" onclick="dalete_condition('+"'"+par_id+"'"+',this)">删除</a>\n\
                    </td>\n\
                  </tr>';

    var hiddenVal = hiddenValue(names, str, hide_id);
    if (num === 0) {
        $('#condition_in').append(tr);
        $('#prcs_in').val(hiddenVal);
        var number = $("#condition_in tr").length;
        $("#condition_in tr:last").attr('prcs_in_id', number);
        $("#condition_in tr:last").find('td:first').text('[' + number + ']');
    } else {
        $('#condition_out').append(tr);
        $('#prcs_out').val(hiddenVal);
        var number = $("#condition_out tr").length;
        $("#condition_out tr:last").attr('prcs_in_id', number);
        $("#condition_out tr:last").find('td:first').text('[' + number + ']');
    }
}

//处理隐藏input的值
function hiddenValue(names, str, hide_id) {
    var hide_str;
    if ('' == $('#' + hide_id).val())
    {
        hide_str = names + '/' + str + ',';
    } else
    {
        var strtmp = $('#' + hide_id).val();
        strtmp += names + '/' + str + ',';
        hide_str = strtmp;
    }
    return hide_str;
//    var object = new Object();
//    object.name = names;
//    object.text = str;
//    object.value = value;
//    object.where = where;
//    if (hide == '') {
//        var arr = new Array();
//        arr.push(object);
//    } else {
//        var arr = JSON.parse(hide);
//        arr.push(object);
//    }
//    return JSON.stringify(arr);
}

//删除
function dalete_condition(par_id,_this) {
    var table = $(_this).parents('table');
    $(_this).parents('tr').remove();
    var tr = $('#'+par_id).find('tr');
    $.each(tr,function(i,v){
        $(this).attr('prcs_in_id',i+1);
        $(this).find('td:nth-child(1)').text('['+(i+1)+']');
    });
    //处理隐藏
    var str = '';
    $.each(tr,function(i,v){
        if(0 == i)
        {
            str = $(this).attr('names')+'/'+$(this).find('span').text()+',';
        }
        else
        {
            str += $(this).attr('names')+'/'+$(this).find('span').text()+',';
        }
    });
    table.next('input').val(str);
}

//编辑
function edit_condition(_this) {
    var val = $(_this).parent().prev().find('span').text();
    var valInput = $(_this).parent().prev().find('input').val();
    var check = $(_this).parent().prev().find('span');
    if (check.length > 0) {
        $(_this).parent().prev().html('<input onblur="blurThis(this)"  value="' + val + '"/>');
    } else {
        $(_this).parent().prev().html('<input  onblur="blurThis(this)" value="' + valInput + '"/>');
    }
}

function blurThis(_this) {
    var val = $(_this).val();
    var table = $(_this).parents('table');
    var tr = $(_this).parents('tbody').find('tr');
    $(_this).parent().html('<span>' + val + '</span>');
    var str = '';
    $.each(tr,function(i,v){
        if(0 == i)
        {
            str = $(this).attr('names')+'/'+$(this).find('span').text()+',';
        }
        else
        {
            str += $(this).attr('names')+'/'+$(this).find('span').text()+',';
        }
    });
    table.next('input').val(str);
}


