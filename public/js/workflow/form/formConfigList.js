var unicity = ["不能重复", "不能为空"];

//表单设置列表
var token = $('meta[name="_token"]').attr('content');
var formConfigTable = $("#formConfigTable").dataTable({
    "columns": [
        {"data": "id", "title": "表单id"},
        {"data": "form_name", "title": "表单名称"},
        {"data": "classify.classifyname", "title": "表单分类","sortable":false},
        {"data": "form_describe", "title": "表单描述"},
//        {"data": "formdesign.fields", "title": "字段数",
//            "render": function (data, type, row, meta) {
//                if (data == null) {
//                    return "0";
//                }
//                return data;
//            }
//        },
        {"data": "updated_at", "title": "最后修改时间", "searchable": false},
        {"data": "sort", "title": "排序"},
        {"data": "formdesign.id", "title": "设计状态",
            "render": function (data, type, row, meta) {
                var html = data > 0 ? "是" : "否";
                return html;
            }
        },
        {"data": "flowtype.flow_status", "title": "使用状态",
            "render": function (data, type, row, meta) {
                var html = "否";
                data.map(function (value) {
                    if (value == 1)
                        html = "是";
                });
                return html;
            }
        },
        {"data": "id", "title": "表单操作", "sortable": false,
            "render": function (data, type, row, meta) {
                return '<a href="javascript:formDesign(' + data + ')"  title="智能设计">智能设计</a> | <a href="javascript:phoneDesign(' + data + ')"  title="移动设计">移动设计</a> | <a href="javascript:preview(' + data + ')"  title="预览">预览</a> | <a href="javascript:excelBlade(' + data + ')" title="导出">导出</a>';
            }
        },
        {"data": "id", "title": "操作", "sortable": false,
            "render": function (data, type, row, meta) {
                return '<a href="#myModal-1" data-toggle="modal" title="编辑" class="edit" urlid="' + data + '">编辑 </a> | <a href="javascript:void(0);" title="删除"class="delete_config" deleteId="' + data + '">删除</a>';
            }
        }
    ],
    "ajax": "/workflow/formConfigTableList?_token=" + token
});

//创建时清空表单的值和获取分类数据
$('#createClassify').on('click', function () {
    $('#formConfigTitle').text('创建表单');
    $('.form-group input[name="form_name"]').val('');
    $('.form-group input[name="form_describe"]').val('');
    $('.form-group input[name="sort"]').val('');
    $('.deleteId').html('');
    $('select[name="form_classify_department_id"]').val(1);
    $('#excelForm').val('');
    $('#validateTijiao').val('0');
    var url = "/workflow/formConfigCreate";
    $.ajax({
        type: 'post',
        url: url,
        dataType: 'json',
        data: '',
        success: function (msg) {
            var str = '';
            $.each(msg, function (k, v) {
                str += ' <option value="' + v.id + '">' + v.classifyname + '</option>';
            });
            $('#classifyId_select').html(str);
        }
    });
});
//----------------------------创建表单所属部门start---------------------------
//部门树形图配置
/* zTree start */
departmentOptionsZTreeSetting = {
    async: {
        url: "/hr/department/tree?_token=" + token
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
//---------------------------创建表单所属部门end------------------

//保存
$('.btn-primary').on('click', function () {
    if ($('#validateTijiao').val() == '0') {
        return false;
    }
    var url = $('.modal-body form').attr('action');
    var form = $('.modal-body form')[0];
    var data = new FormData(form);
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        success: function (msg) {
            if (msg == 'success') {
                $('.close').click();
                formConfigTable.fnDraw();
                $('.formConfigHidden').text('添加成功');
                $('.formConfigHidden').fadeIn(0);
                $('.formConfigHidden').fadeOut(3000);
            } else if (msg == 'saveSuccess') {
                $('.close').click();
                formConfigTable.fnDraw();
                $('.formConfigHidden').text('编辑成功');
                $('.formConfigHidden').fadeIn(0);
                $('.formConfigHidden').fadeOut(3000);

            } else if (msg == 'excelError') {
                alert('该流程正在使用！不允许导入模板');
                formConfigTable.fnDraw();
                $('.close').click();
            } else if (msg == 'titleError') {
                alert('该模板的某些字段不含有title属性！请重新编辑模板再导入');
            } else {
                $('.close').click();
                formConfigTable.fnDraw();
                $('.formConfigHidden').text('保存失败');
                $('.formConfigHidden').fadeIn(0);
                $('.formConfigHidden').fadeOut(3000);
            }
        },
        error: function (error) {
            if (error.status == 422) {
                alert('请输入必填项');
            }
        }
    });
});
//修改
$(document).on('click', '.edit', function () {
    $('#excelForm').val('');
    $(".form-horizontal input[name='form_name']").css('color', '');
    $('#validateTijiao').val('1');
    var id = $(this).attr('urlid');
    var url = "/workflow/formConfigSave";
    $.ajax({
        type: 'post',
        url: url,
        data: {id: id},
        dataType: 'json',
        success: function (msg) {
            $('#formConfigTitle').text('编辑表单');
            var inputId = '<input type="hidden" name="id" value="' + msg.data.id + '"/>';
            $('.form-group input[name="form_name"]').val(msg.data.form_name);
            $('.form-group input[name="form_describe"]').val(msg.data.form_describe);
            $('select[name="form_classify_department_id"]').val(msg.data.form_classify_department_id);
            $('.form-group input[name="sort"]').val(msg.data.sort);
            $('.modal-body form .deleteId').html(inputId);
            var str = '';
            $.each(msg.classify, function (k, v) {
                str += ' <option value="' + v.id + '">' + v.classifyname + '</option>';
            });
            $('#classifyId_select').html(str);
            $('#classifyId_select').val(msg.data.classify.id);
        }
    });
});
//智能设计表单
function formDesign(id) {
    var url = "/workflow/formDesignList" + "?id=" + id;
    window.open(url, '', 'height=' + (screen.availHeight - 0) + ', width=' + (screen.availWidth - 0) + ', top=0, left=0,toolbar=no, menubar=no, scrollbars=no, resizable=no, location=n o, status=no');
}
//移动设计
function phoneDesign(id) {
    $('form[name="form_formDesign"]').find('input[name="id"]').val(id);
    var url = "/workflow/formDesignPhonePreview";
    document.form_formDesign.target = "mywin";
    window.open('', 'mywin', "menubar=0,toolbar=0,status=0,resizable=1,left=0,top=100,scrollbars=1,width=" + (screen.availWidth - 1410) + ",height=" + (screen.availHeight - 250) + "\"");

    document.form_formDesign.action = url;
    document.form_formDesign.submit(); //提交表单
}
//列表预览
function preview(id) {
    $('form[name="form_formDesign"]').find('input[name="id"]').val(id);
    var url = "/workflow/formDesignPreview";
    document.form_formDesign.target = "mywin";
    window.open('', 'mywin', "menubar=0,toolbar=0,status=0,resizable=1,left=0,top=0,scrollbars=1,width=" + (screen.availWidth - 10) + ",height=" + (screen.availHeight - 50) + "\"");

    document.form_formDesign.action = url;
    document.form_formDesign.submit(); //提交表单

}

//导出模板
function excelBlade(id) {
    window.location.href = "/workflow/formConfigExcelBlade?id=" + id;
}
//删除
$(document).on('click', '.delete_config', function () {
    var id = $(this).attr('deleteId');
    var url = "/workflow/formConfigDelete";
    if (confirm("确认删除这个表单？")) {
        $.ajax({
            type: 'post',
            url: url,
            data: {id: id},
            success: function (msg) {
                if (msg == 'success') {
                    formConfigTable.fnDraw();
                    $('.formConfigHidden').text('删除成功');
                    $('.formConfigHidden').fadeIn(0);
                    $('.formConfigHidden').fadeOut(3000);
                } else {
                    formConfigTable.fnDraw();
                    $('.formConfigHidden').text('删除失败');
                    $('.formConfigHidden').fadeIn(0);
                    $('.formConfigHidden').fadeOut(3000);
                }
            }
        });
    }
});

//验证重复
function checkRepetition(vesselId, field, url) {
    var val = $('#' + vesselId).val();
    var text = $('#' + vesselId).parent().prev().text().match(/[\u4e00-\u9fa5]+/g);
    var data = {};
    data[field] = val;
    var id = $('.deleteId').find('input[name="id"]').val();
    if (id != null) {
        data.id = id;
    }
    var _this = $('#' + vesselId);
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        success: function (msg) {
            if (msg == 'error') {
                _this.val(text + '不能重复');
                _this.css('color', 'red');
                $('#validateTijiao').val('0');
            } else if (val === '') {
                _this.val(text + '不能为空');
                _this.css('color', 'red');
                $('#validateTijiao').val('0');
            } else {
                $('#validateTijiao').val('1');
            }
        }
    });
}
//验证表单名称是否重复
$(".form-horizontal input[name='form_name']").on('blur', function () {
    var url = "/workflow/formConfigValidateName";
    checkRepetition("form_name", "form_name", url);
//     var val = $(this).val();
//     var url = "/workflow/formConfigValidateName";
//     var data = {
//         'form_name': val
//     };
//     var id = $('.deleteId').find('input[name="id"]').val();
//     if (id != null) {
//         data.id = id;
//     }
//     var _this = $(this);
//     console.log(data);
//     $.ajax({
//         type: 'post',
//         url: url,
//         data: data,
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//         },
//         success: function (msg) {
//             console.log(msg);
//             if (msg == 'error') {
//                 _this.val('表单名称重复');
// //                _this.attr('placeholder','表单名称重复');
//                 _this.css('color', 'red');
//                 $('#validateTijiao').val('0');
//             } else if (val === '') {
//                 _this.val('表单名称不能为空');
// //                _this.attr('placeholder','表单名称不能为空');
//                 _this.css('color', 'red');
//                 $('#validateTijiao').val('0');
//             } else {
//                 $('#validateTijiao').val('1');
//             }
//         }
//     });
});
//获取焦点清空重复和空的值
// $(".form-horizontal input[name='form_name']").on('focus', function () {
//     if ($(this).val() == "表单名称不能为空" || $(this).val() == "表单名称不能重复") {
//         $(this).val('');
//         $(this).css('color', '');
//     }
// });
//获取焦点清空重复和空的值
$(".form-horizontal input[name='form_name']").on('focus', function () {
    for (var i = 0; i < unicity.length; i++)
    {
        if (-1 != $(this).val().indexOf(unicity[i]) && "rgb(85, 85, 85)" != $(this).css("color"))
        {
            $(this).val('');
            $(this).css('color', '');
        }
    }
});

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

