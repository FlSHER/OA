//-------------------------------------------------流程分类start-------------------------------------------
var token = $('meta[name="_token"]').attr('content');
//创建时清空流程分类的值
$('#createFlowClassify').on('click', function () {
    $('#flow_classify_title').text('创建流程分类');
    $('#flow_classify_form input[name="flow_classifyname"]').val('');
    $('#flow_classify_form input[name="flow_describe"]').val('');
    $('#flow_classify_form .flowClassifyDeleteId').html('');
//    $('#flow_classify_form input[name="flow_classifyname" ]').next('p').text("");
    $("#flowClassifyTijiao").val('0');
});
//流程分类列表
var flowTable = $("#flow_classify_list").dataTable({
    "columns": [
        {"data": "id", "title": "分类id"},
        {"data": "flow_classifyname", "title": "分类名字"},
        {"data": "flow_describe", "title": "描述"},
        {"data": "updated_at", "title": "上次修改时间", "searchable": false},
        {"data": "id", "title": "操作", "sortable": false,
            "render": function (data, type, row, meta) {
                return '<a href="#flowClassify" data-toggle="modal" class="flow_class_edit" title="编辑"  urlid="' + data + '">编辑 </a> | <a href="javascript:void(0);" class="delete_flowClassify" title="删除"  deleteId="' + data + '">删除</a>';
            }
        }
    ],
    "ajax": "/workflow/flowClassifyList?_token=" + token
});
//流程分类保存
$("#submit_flow").on('click', function () {
    if ($("#flowClassifyTijiao").val() == '0') {
        return false;
    }
    var url = $('#flow_classify_form').attr('action');
    var data = $('#flow_classify_form').serialize();
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
                flowTable.fnDraw();
                $('.flow_classify_hidden').text('添加成功');
                $('.flow_classify_hidden').fadeIn(0);
                setTimeout(function () {
                    $('.flow_classify_hidden').fadeOut(3000);
                }, '1000');

                // $('.nav-tabs li a[href="#contact3"]').click();
            } else if (msg == 'saveFlowClassify') {
                $('.close').click();
                flowTable.fnDraw();
                $('.flow_classify_hidden').text('编辑成功');
                $('.flow_classify_hidden').fadeIn(0);
                $('.flow_classify_hidden').fadeOut(3000);
            } else {
                $('.close').click();
                flowTable.fnDraw();
                $('.flow_classify_hidden').text('保存失败');
                $('.flow_classify_hidden').fadeIn(0);
                $('.flow_classify_hidden').fadeOut(3000);
            }
        },
        error: function (error) {
            if (error.status == 422) {
                alert('必填字段不能为空');
            }
        }
    });
});

//流程分类编辑
$(document).on('click', '.flow_class_edit', function () {
    $('#flow_classify_form input[name="flow_classifyname" ]').css('color', '');
    $("#flowClassifyTijiao").val('1');
    var url = "/workflow/flowClassifySave";
    var id = $(this).attr('urlid');
    $.ajax({
        type: 'post',
        url: url,
        data: {id: id},
        dataType: 'json',
        success: function (msg) {
            $('#flow_classify_title').text('编辑流程分类');
            var inputId = '<input type="hidden" name="id" value="' + msg.id + '"/>';
            $('#flow_classify_form input[name="flow_classifyname"]').val(msg.flow_classifyname);
            $('#flow_classify_form input[name="flow_describe"]').val(msg.flow_describe);
            $('#flow_classify_form .flowClassifyDeleteId').html(inputId);
        }
    });
});
//流程分类删除
$(document).on('click', '.delete_flowClassify', function () {
    var id = $(this).attr('deleteId');
    var url = "/workflow/flowClassifyDelete";
    if (confirm('确认删除这条流程分类？')) {
        $.ajax({
            type: 'post',
            url: url,
            data: {id: id},
            success: function (msg) {
                if (msg == 'success') {
                    flowTable.fnDraw();
                    $('.flow_classify_hidden').text('删除成功');
                    $('.flow_classify_hidden').fadeIn(0);
                    $('.flow_classify_hidden').fadeOut(3000);
                } else {
                    flowTable.fnDraw();
                    $('.flow_classify_hidden').text('删除失败');
                    $('.flow_classify_hidden').fadeIn(0);
                    $('.flow_classify_hidden').fadeOut(3000);
                }
            }
        });
    }
});
//验证流程分类名称是否重复
$('#flow_classify_form input[name="flow_classifyname" ]').on('blur', function () {
    var _this_val = $(this).val();
    var url = "/workflow/flowClassifyValidateName";
    var text = $(this).parent().prev().text().match(/[\u4e00-\u9fa5]+/g);
    var data = {
        flow_classifyname: _this_val
    };
    var id = $('#flow_classify_form .flowClassifyDeleteId').find('input[name="id"]').val();
    if (id != null) {
        data.id = id;
    }
    var _this = $(this);
    $.ajax({
        type: 'post',
        url: url,
        data: data,
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function (msg) {
            if (msg == 'error') {
                _this.val(text + '不能重复');
                _this.css('color', 'red');
                $('#flowClassifyTijiao').val('0');
            } else if (_this_val === '') {
                _this.val(text + '不能为空');
                _this.css('color', 'red');
                $("#flowClassifyTijiao").val('0');
            } else {
                $("#flowClassifyTijiao").val('1');
            }
        }
    });
    //$('.form-group input[name="classifyname" ]').next('p').text("分类名称重复");
});
var unicity = ["不能重复", "不能为空"];
//获取焦点清空重复和空的值
$('#flow_classify_form input[name="flow_classifyname" ]').on('focus', function () {
    for (var i = 0; i < unicity.length; i++)
    {
        if (-1 != $(this).val().indexOf(unicity[i]) && "rgb(85, 85, 85)" != $(this).css("color"))
        {
            $(this).val('');
            $(this).css('color', '');
        }
    }
});

//---------------------------------------------流程分类end-----------------------------------------