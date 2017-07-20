//    -----------------------------------------------表单分类start-----------------------------  
//表单分类列表
var token = $('meta[name="_token"]').attr('content');
var formTable = $("#form-classify").dataTable({
    "columns": [
        {"data": "id", "title": "分类id"},
        {"data": "classifyname", "title": "分类名字"},
        {"data": "describe", "title": "描述", "sortable": true},
        {"data": "updated_at", "title": "上次修改时间", "searchable": false},
        {"data": "id", "title": "操作", "sortable": false,
            "render": function (data, type, row, meta) {
                return '<a href="#myModal-1" data-toggle="modal" class="edit" title="编辑"  urlid="' + data + '">编辑 </a> | <a href="javascript:void(0);" class="delete_classify" title="删除"  deleteId="' + data + '">删除</a>';
            }
        }
    ],
    "ajax": "/workflow/formClassifyList?_token="+token
});

//创建时清空表单分类的值
$('#createClassify').on('click', function () {
    $('#formClassifyTitle').text('创建表单分类');
    $('#form_classify_form input[name="classifyname"]').val('');
    $('#form_classify_form input[name="describe"]').val('');
    $('#form_classify_form .deleteId').html('');
//    $('.form-group input[name="classifyname" ]').next('p').text("");
    $("#validateTijiao").val('0');
});

//保存  表单分类
$('#form_classify_submit').on('click', function () {
    if ($("#validateTijiao").val() == '0') {
        return false;
    }
    var url = $('#form_classify_form').attr('action');
    var data = $('#form_classify_form').serialize();
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function (msg) {
            if (msg == 'success') {
                $('.close').click();
                formTable.fnDraw();
                $('.form_classify_hidden').text('添加成功');
                $('.form_classify_hidden').fadeIn(0);
                setTimeout(function () {
                    $(".form_classify_hidden").fadeOut(3000);
                }, 1000);
            } else if (msg == 'saveSuccess') {
                $('.close').click();
                formTable.fnDraw();
                $('.form_classify_hidden').text('编辑成功');
                $('.form_classify_hidden').fadeIn(0);
                $('.form_classify_hidden').fadeOut(3000);
            } else if (msg == 'error') {
                $('.close').click();
                formTable.fnDraw();
                $('.form_classify_hidden').text('保存失败');
                $('.form_classify_hidden').fadeIn(0);
                setTimeout(function () {
                    $(".form_classify_hidden").fadeOut(3000);
                }, 1000);
            }
        },
        error: function (error) {
            if (error.status == 422) {
                alert('该字段不能为空');
            }
        }
    });
});

//修改  表单分类
$(document).on('click', '.edit', function () {
//    $('.form-group input[name="classifyname" ]').next('p').text("");
    $("#classifyname").css('color', '');
    $("#validateTijiao").val('1');
    var id = $(this).attr('urlid');
    var url = "/workflow/formClassifySave";
    $.ajax({
        type: 'post',
        url: url,
        data: {id: id},
        dataType: 'json',
        success: function (msg) {
            $('#formClassifyTitle').text('编辑表单分类');
            var inputId = '<input type="hidden" name="id" value="' + msg.id + '"/>';
            $('#form_classify_form input[name="classifyname"]').val(msg.classifyname);
            $('#form_classify_form input[name="describe"]').val(msg.describe);
            $('#form_classify_form .deleteId').html(inputId);
        }
    });
});
//删除  表单分类
$(document).on('click', '.delete_classify', function () {
    var id = $(this).attr('deleteId');
    var url = "/workflow/formClassifyDelete";
    if (confirm("确认删除这个分类")) {
        $.ajax({
            type: 'post',
            url: url,
            data: {id: id},
            success: function (msg) {
                if (msg == 'success') {
                    formTable.fnDraw();
                    $('.form_classify_hidden').text('删除成功');
                    $('.form_classify_hidden').fadeIn(0);
                    setTimeout(function () {
                        $(".form_classify_hidden").fadeOut(3000);
                    }, 1000);
                } else {
                    $('.form_classify_hidden').text('删除失败');
                    $('.form_classify_hidden').fadeIn(0);
                    setTimeout(function () {
                        $(".form_classify_hidden").fadeOut(3000);
                    }, 1000);
                }

            }
        });
    }
});
//验证表单分类名称是否重复
$('.form-group input[name="classifyname" ]').on('blur', function () {
    var url = "/workflow/formClassifyVeridateName";
     checkRepetition("classifyname", "classifyname", url);
//    var _this_val = $(this).val();
//    var url = "/workflow/formClassifyVeridateName";
//    var data = {
//        classifyname: _this_val
//    };
//    var id = $('#form_classify_form .deleteId').find('input[name="id"]').val();
//    if (id != null) {
//        data.id = id;
//    }
//    $.ajax({
//        type: 'post',
//        url: url,
//        data: data,
//        headers: {
//            'X-CSRF-TOKEN': token
//        },
//        success: function (msg) {
//            if (msg == 'error') {
//                $('.form-group input[name="classifyname" ]').next('p').text("分类名称重复");
//                $("#validateTijiao").val('0');
//            } else if (_this_val === '') {
//                $('.form-group input[name="classifyname" ]').next('p').text("分类名称不能为空");
//                $("#validateTijiao").val('0');
//            } else {
//                $('.form-group input[name="classifyname" ]').next('p').text("");
//                $("#validateTijiao").val('1');
//            }
//        }
//    });
//    //$('.form-group input[name="classifyname" ]').next('p').text("分类名称重复");
});
//-----------------------------------------表单分类end--------------------------------------------------

var unicity = ["不能重复", "不能为空"];
//获取焦点清空重复和空的值
$("#classifyname").on('focus', function () {
    for (var i = 0; i < unicity.length; i++)
    {
        if (-1 != $(this).val().indexOf(unicity[i]) && "rgb(85, 85, 85)" != $(this).css("color"))
        {
            $(this).val('');
            $(this).css('color', '');
        }
    }
});