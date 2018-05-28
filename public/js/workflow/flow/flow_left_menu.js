$(function () {
    //初始化流程左侧树形列表菜单
    leftMenuInit();
});
//流程左侧树形列表文件夹菜单
function leftMenuInit() {
    var url = "./flowConfigCreate";
    $.ajax({
        type: 'post',
        url: url,
        dataType: 'json',
        data: '',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        success: function (msg) {
            var str = "";
            $.each(msg, function (k, v) {
                str +=
                        '<li>' +
                        '<span >' +
                        '<span></span>' +
                        '<img src="/images/tree/tree_title.png" alt="">' +
                        '<a href="#" onclick="leftMenuFile(' + 'this,' + "'" + v.id + "'" + ')">' + v.flow_classifyname + '</a>' +
                        '</span>' +
                        '</li>';
            });
            str +=
                    '<li>' +
                    '<span>' +
                    '<span></span>' +
                    '<img src="/images/tree/tree_title.png" alt="">' +
                    '<a href="#" onclick="leftMenuFile(' + 'this,' + "'" + 0 + "'" + ')">未分类</a>' +
                    '</span>' +
                    '</li>';
            $('#flow_tree_menu').html(str);
        }
    });
}
//流程左侧树形列表文件菜单
function leftMenuFile(self, id) {
    if ($(self).parents("li").find('ul').length > 0)
    {
        $(self).parents("li").find('ul').remove();
    } else
    {
        var url = "./flowLeftMenu";
        var data = {"flow_sort": id};
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (msg) {
                if (msg.length > 0)
                {
                    var str = '<ul style="margin: 0 0 10px 25px;">';
                    $.each(msg, function (k, v) {
                        str +=
                                '<li>' +
                                '<span>' +
                                '<span></span>' +
                                '<img src="/images/tree/tree_content.png" alt="">' +
                                '<a href="#" onclick="flowAttribute(' + "'" + v.flow_id + "'" + ')">' + v.flow_name + '</a>' +
                                '</span>' +
                                '</li>';
                    });
                    str += '</ul>';
                    $(self).parents("li").append(str);
                }
            }
        });
    }
}
//流程属性
function flowAttribute(flow_id) {
    var url = './flowAttribute?flow_id=' + flow_id;
    $('#right_body').load(url);
}
//跳转
function flowSkip(url)
{
    //var url = './flowAttribute?flow_id='+flow_id+'&skip=1';
    //console.log(flow_id);
    window.open(url, 'newwindow');
}
//流程查询查询
$('#flow_search').focus(function () {
    $("#img_serach").hide();
    $(this).keyup(function () {
        if ($(this).val())
        {
            var url = "./flowLeftMenuSerach";
            var data = {"flow_name": $(this).val()};
            $.ajax({
                type: 'post',
                url: url,
                dataType: 'json',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (msg) {
                    if (msg.length > 0)
                    {
                        $('#flow_tree_menu').children().remove();
                        var str = '';
                        $.each(msg, function (k, v) {
                            str +=
                                    '<li>' +
                                    '<span>' +
                                    '<span></span>' +
                                    '<img src="/images/tree/tree_content.png" alt="">' +
                                    '<a href="#">' + v.flow_name + '</a>' +
                                    '</span>' +
                                    '</li>';
                        });
                        $('#flow_tree_menu').append(str);
                    } else
                    {
                        $('#flow_tree_menu').children().remove();
                        var str =
                                '<li>' +
                                '<span>' +
                                '<span></span>' +
                                '<a href="#" class="dynatree-title">没有匹配数据...</a>' +
                                '</span>' +
                                '</li>';
                        $('#flow_tree_menu').append(str);
                    }
                }
            });
        } else
        {
            leftMenuInit();
        }
    }).keyup();
}).blur(function () {
    $("#img_serach").show();
});
//图标旋转
function rotateImag(self) {
    if ($(self).children('img').length > 0)
    {
        var rotate_init = 'none';
        var rotate_90_deg = 'matrix(6.12323e-17, 1, -1, 6.12323e-17, 0, 0)';
        var rotate_0_deg = 'matrix(1, 0, 0, 1, 0, 0)';
        var imgObj = $('.panel-group').find('img');//找到所有的img标签
        var newstyle = "padding-bottom:4px;";
        var _height = "0px";
        $.each(imgObj, function () {
            if (-1 != rotate_90_deg.indexOf(String($(this).css("-webkit-transform"))))
            {
                $(this).css("-webkit-transform", "rotate(0deg)");//还原图标旋转
                $(this).attr("style", newstyle);
            }
        });
        // console.log($(self).parents('.panel-heading').siblings().attr('id'));
        // var siblingsId = $(self).parents('.panel-heading').siblings().attr('id');
        // console.log($('#'+siblingsId).css('height'));
        if (-1 != rotate_init.indexOf(String($(self).children('img').css("-webkit-transform"))))
        {
            $(self).children('img').css("-webkit-transform", "rotate(90deg)");
            return false;
        }
        // if(_height != String($('#'+siblingsId).css('height')))
        // {
        // 	//$(self).children('img').css("-webkit-transform","rotate(0deg)");
        // 	console.log("关闭");
        // }
        if (-1 != _height.indexOf(String($('#' + siblingsId).css('height'))))
        {
            $(self).children('img').css("-webkit-transform", "rotate(0deg)");
            return false;
        }
        if (-1 != rotate_0_deg.indexOf(String($(self).children('img').css("-webkit-transform"))))
        {
            //console.log($(self).children('img').css("-webkit-transform"));
            $(self).children('img').css("-webkit-transform", "rotate(0deg)");
            return false;
            //$(self).children('img').attr("style", newstyle);
        }
    }
}