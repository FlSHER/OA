/*新建流程*/
var unicity = ["不能重复","不能为空"];
var token = $('meta[name="_token"]').attr('content');

//ul列表初始化
function ulListInit(){
    $(".on_changes div").remove();
    var url = "./flowFormSet";
    $.ajax({
        type: 'post',
        url: url,
        dataType: 'json',
        data: '',
        async:false,
        headers:{
          'X-CSRF-TOKEN':$('meta[name="_token"]').attr('content')  
        },
        success: function (msg) {
            var arr = [];
            var str = '';
            $.each(eval(msg), function (k, v) {
                if(-1 == $.inArray(v.classifyname, arr))
                {
                    arr.push(v.classifyname);
                }
            });
            $.each(arr,function(k,v){
                str += '<div class="category" style="padding-left:25px;font-weight:bold;font-size:12px;background:url(/images/tree/icon-folder.gif) 5px 50% no-repeat;">'+v+'</div>';
                $.each(eval(msg),function(mk,mv){
                    if(v == mv.classifyname)
                    {
                        str += "<div><li onclick='get("+'"'+mv.id
                            +'"'+","+'"'+mv.form_name
                            +'"'+")'>"
                            +mv.form_name
                            +"</li></div>";
                    }
                });
            });
            $(".on_changes").append(str);
        }
    });
}
//ul控制下拉框显示
function ulShow()
{
    var display =$('.on_changes');
    if(display.is(':hidden')){//如果node是隐藏的则显示node元素，否则隐藏
        $(".on_changes").show();
        if(''!=$("#form_name").val())//设置选中li背景色
        {
            $("#files").find('li').each(function(){
                if($("#form_name").val() == $(this).text())
                {
                    $(this).css('background-color','#FBEC88');
                }
            });
        }
    }else{
        $(".on_changes").hide();
    }
}
var mousClickSign = 0;//鼠标点击标记
//选择表单
$('#form_select').on('click',function(){
    ulListInit();
    ulShow();
    mousClickSign = 1;
    document.getElementById('form_name').focus();
});
//设置form_name输入框的值
function get(data1,data2){
    $("#form_id").val(data1);
    $("#form_name").val(data2);
    $(".on_changes").hide();
}
//失去焦点隐藏ul列表
document.onclick = function(e){
    if(e.srcElement)
    {
        if('li' != e.srcElement.localName)
        {
            if(0 == mousClickSign)
            {
                if('form_name'!=e.srcElement.id)
                {
                    $(".on_changes").hide();
                }
                else
                {
                    ulShow();
                }
            }
            mousClickSign = 0;//还原鼠标点击标记
        }
    }
}
$('#form_name').click(function(){
    document.getElementById('form_select').click();
    $('#form_name').keyup(function(){
        $('#files div').hide().filter(":contains("+$(this).val()+")").show();
    }).keyup();
});
$('#form_name').focus(function(){
    $(".on_changes").show();
    $('#form_name').keyup(function(){
        $('#files div').hide().filter(":contains("+$(this).val()+")").show();
    }).keyup();
});
//不能重复、不能为空验证函数
function checkRepetition(vesselId,field,url){
    var val = $('#'+vesselId).val();
    var text = $('#'+vesselId).parent().prev().text().match(/[\u4e00-\u9fa5]+/g);
    var data = {};
    data["flow_name"]=val;
    var id = $('.deleteId').find('input[name="id"]').val();
    if (id != null) {
        data.id = id;
    }
    var _this = $('#'+vesselId);
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        async:false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        success: function (msg) {
            if (msg == 'error') {
                _this.val(text+'不能重复');
                _this.css('color', 'red');
                $('#validateTijiao').val('0');
            } else if (val === '') {
                _this.val(text+'不能为空');
                _this.css('color', 'red');
                $('#validateTijiao').val('0');
            } else {
                $('#validateTijiao').val('1');
            }
        }
    });
}
//验证表单名称是否重复
$(".form-horizontal input[name='flow_name']").on('blur',function(){
    if($('#skip').length == 0)
    {
       var url = "./flowConfigValidateName";
        checkRepetition("flow_name","flow_name",url); 
    }
});
//获取焦点清空重复和空的值
$(".form-horizontal input[name='flow_name']").on('focus',function(){
    for(var i = 0; i < unicity.length; i++)
    {
        if(-1 != $(this).val().indexOf(unicity[i]) && "rgb(85, 85, 85)" != $(this).css("color"))
        {
            $(this).val('');
            $(this).css('color', '');
        }
    }
});
//保存
$('#save').on('click', function () {
    var url = "./flowConfigSubmit";
    var data = jsonData();
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        success: function (msg) {
            if (msg == 'success') {
                $('.close').click();
                alert("添加成功");
            } else if (msg == 'saveSuccess') {
                $('.close').click();
                alert("编辑成功");
            } else {
                $.each(JSON.parse(msg),function(i,v){
                    var text = $('#'+i).parents().prev('.control-label').text().match(/[\u4e00-\u9fa5]+/g);
                    alert(text+v);
                });
            }
        }
    });
});
//定义属性时保存
$('#updateSave').on('click', function () {
    var url = "./flowConfigUpdateSave";
    var data = jsonData();
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        success: function (msg) {
            if (String(msg) == 'saveSuccess') {
                alert("编辑成功");
                window.close();
            } 
            else if(String(msg) == 'exist'){
                var text = $('#flow_name').parents().prev('.control-label').text().match(/[\u4e00-\u9fa5]+/g);
                alert(text+"已经存在");
            }
            else
            {
                var msgTmp = JSON.parse(msg);
                $.each(msgTmp,function(i,v){
                    var text = $('#'+i).parents().prev('.control-label').text().match(/[\u4e00-\u9fa5]+/g);
                    alert(text+v);
                });
            }
        }
    });
});
//文本域框
 function note_click(target)
{ 
    if(target.value!='')
    {
        target.value="";
    }
}
//获取数据
function jsonData(){
    var list_flds_str = "";
    $("#list1 option").each(function(i){
        if(0 == i)
        {
           list_flds_str = $(this).val()+"/"; 
        }
        else
        {
            list_flds_str += $(this).val()+"/";
        }
    });
    if("rgb(85, 85, 85)" != $('#flow_name').css("color"))
    {
        $('#flow_name').val('');
        $('#flow_name').css('color', '');
    }           
    var jsonStr = {
        "flow_name":document.getElementById("flow_name").value,
        "flow_sort":$('#flow_sort option:selected').val(),
        "form_id":document.getElementById("form_id").value,
        "form_name":document.getElementById("form_name").value,
        "department_id":$('#department_id option:selected').val(),
        "flow_type":$('#flow_type option:selected').val(),
        "free_other":$('#free_other option:selected').val(),
        "view_priv":$('input:radio[name="view_priv"]:checked').val(),
        "view_user":document.getElementById("view_user_id").value,
        "view_dept":document.getElementById("view_dept_id").value,
        "view_role":document.getElementById("view_role_id").value,
        "flow_no":document.getElementById("flow_no").value,
        "auto_name":document.getElementById("auto_name").value,
        "auto_num":document.getElementById("auto_num").value,
        "auto_len":document.getElementById("auto_len").value,
        "auto_edit":$('#auto_edit option:selected').val(),
        "flow_desc":document.getElementById("flow_desc").value,
        "list_flds_str":list_flds_str
    }
    if($('#flow_id').length > 0)
    {
        jsonStr['flow_id'] = $("#flow_id").val();
    }
    return jsonStr;
}
$(function(){
    $('#addOption').click(function(){
        //获取选中的选项，删除并追加给对方
        $('#list1 option:selected').appendTo('#list2');
    });
    //移到左边
    $('#delOption').click(function(){
        $('#list2 option:selected').appendTo('#list1');
    });
    //全部移到右边
    $('#addAllOption').click(function(){
        //获取全部的选项,删除并追加给对方
        $('#list1 option').appendTo('#list2');
    });
    //全部移到左边
    $('#delAllOption').click(function(){
        $('#list2 option').appendTo('#list1');
    });
    //双击选项
    $('#list1').dblclick(function(){ //绑定双击事件
        //获取全部的选项,删除并追加给对方
        $("option:selected",this).appendTo('#list2'); //追加给对方
    });
    //双击选项
    $('#list2').dblclick(function(){
        $("option:selected",this).appendTo('#list1');
    });
    
});

//部门树形图配置
/* zTree start */
departmentOptionsZTreeSetting = {
    async: {
        url: "/hr/department/tree?_token="+token
    },
    callback: {
        onClick: function (event, treeId, treeNode) {
            if (treeNode.drag) {
                var options = $(event.target).parents(".ztreeOptions");
                options.prev().children("option[value=" + treeNode.id + "]").prop("selected", true);
                options.hide();
                options.prev().change();
            }
        }
    }
};
/* zTree End */
//显示部门树形图
function showTreeViewOptions(obj) {
    $(obj).parent().append('<div class="ztree ztreeOptions"></div>');
    var options = $(obj).next(".ztreeOptions");
    var width = $(obj).outerWidth();
    departmentTriger = obj;
    if (options.html().length == 0) {
        options.outerWidth(width);
    }
    $(obj).children("option").hide();
    $.fn.zTree.init(options, departmentOptionsZTreeSetting);
    options.toggle();
    $("body").bind("click", hideTreeViewOptions);
    return false;
}
//隐藏部门树形图
function hideTreeViewOptions(event) {
    if (!($(event.target).hasClass("ztreeOptions") || $(event.target).parents(".ztreeOptions").length > 0 || event.target == departmentTriger)) {
        $(".ztree.ztreeOptions").hide();
        $("body").unbind("click", hideTreeViewOptions);
    }
}
var wAlert = window.alert;
window.alert = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        var realert = iwindow.alert(message);
        iframe.parentNode.removeChild(iframe);
        return realert;
    } catch (exc) {
        return wAlert(message);
    }
};
var wConfirm = window.confirm;
window.confirm = function (message) {
    try {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var iwindow = alertFrame.window;
        if (iwindow == undefined) {
            iwindow = alertFrame.contentWindow;
        }
        var reConfirm = iwindow.confirm(message);
        iframe.parentNode.removeChild(iframe);
        return reConfirm;
    } catch (exc) {
        return wConfirm(message);
    }
};