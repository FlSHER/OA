//基本设置js
var token = $('meta[name="_token"]').attr('content');
 $(function () {
    //验证步骤id是否重复
    $('#prcs_id').on('blur', function () {
        var url = "./prcsIdRepetition";
        var data = {'flow_id':$('#flow_id').val(),'prcs_id':$("#prcs_id").val(),'id':$('#key_id').val(),'_token':token};
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            data: data,
            async:false,
            success: function (msg) {
                if(0 == msg.status)
                {
                    $('#prcs_id').next('p').text(msg.msg);
                    $("#submit_check_form").val('0');
               }else if(1 == msg.status)
               {
                    $('#prcs_id').next('p').text(msg.msg);
                     $("#submit_check_form").val('0');
               }else{
                   $("#submit_check_form").val('1');
               }
            }
        });
    });

    //验证步骤id是否重复
    $('#prcs_id').on('focus', function () {
        $('#prcs_id').next('p').text('');
    });

    //步骤名称的值
    $('#prcs_name').on('blur', function () {
        $('.flow_name').text($(this).val());
    });

    var key='';  //记录shift键
    var firstClickIndex = -1; //初始索引
    var firstClick = 0;//点击次数
    var curClickIndex = -1;//当前点击索引
    var lastClickIndex = -1;//上一次点击的索引

    var alternativeFirstClickIndex = -1; //备选步骤 初始索引
    var alternativeFirstClick = 0;//备选步骤 点击次数
    var alternativeCurClickIndex = -1;//备选步骤 当前点击索引
    var alternativeLastClickIndex = -1;//备选步骤 上一次点击的索引
    $(window).keydown(function(e){
        if(e.ctrlKey)
        {
            key='ctrl';
        }
        if(e.shiftKey)
        {
            key='shift';
        }
    }).keyup(function(){
            key='';
    });
    
    //下一步骤 点击当前添加样式
    $("#alternative_next tr").on('click',function (event) {
        event.stopPropagation();//阻止冒泡
        if('ctrl' == key){
            if(String($(this).attr('class')) == 'ui-selected')
            {
                $(this).removeClass('ui-selected');
            }
            else
            {
                $(this).addClass('ui-selected');
            }
        }
        else if('shift' == key)
        {
            firstClick++;
            if(1 >= firstClick)
            {
                firstClickIndex = $(this).index();
            }
            else
            {
                curClickIndex = $(this).index();
            }
            
            if(-1 != firstClickIndex && -1 != curClickIndex && firstClickIndex > curClickIndex)
            {
                if(-1 != lastClickIndex && lastClickIndex == curClickIndex)
                {
                    $(this).removeClass('ui-selected');
                }
                else
                {
                    $.each($("#alternative_next tr"),function(i){
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#alternative_next tr"),function(i){
                        if(firstClickIndex >= i && i >= curClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    lastClickIndex = curClickIndex;
                }
            }
            else if(-1 != firstClickIndex && -1 != curClickIndex && firstClickIndex < curClickIndex)
            {
                if(-1 != lastClickIndex && lastClickIndex == curClickIndex)
                {
                    $(this).removeClass('ui-selected');
                }
                else
                {
                    $.each($("#alternative_next tr"),function(i){
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#alternative_next tr"),function(i){
                        if(curClickIndex >= i && i >= firstClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    lastClickIndex = curClickIndex;
                }
            }
            else
            {
                if(firstClickIndex == curClickIndex)
                {
                    $.each($("#alternative_next tr"),function(i){
                        $(this).removeClass('ui-selected');
                    });
                }
                if(String($(this).attr('class')) == 'ui-selected')
                {
                    $(this).removeClass('ui-selected');
                }
                else
                {
                    $(this).addClass('ui-selected');
                }
            }
        }
        else
        {
            $.each($("#alternative_next tr"),function(i,v){
                $(this).removeClass('ui-selected');
            });
            $(this).addClass('ui-selected');
        }
    });

    //下一步骤 点击取消全部选中样式
    $("#next_step_div").on('click', function (event) {
        $("#alternative_next tr").removeClass('ui-selected');
    });

    //下一步骤  双击选项
    // $("#alternative_next tr").dblclick(function(){
    //     if('ctrl' != key && 'shift' != key)
    //     {
    //         $(this).appendTo('#alternative_tr');
    //         bClick('alternative_tr');
    //     }
    // });

     //备选步骤点击当前添加样式
    $("#alternative_tr tr").on('click', function (event) {
        event.stopPropagation();//阻止冒泡
        if('ctrl' == key){
            if(String($(this).attr('class')) == 'ui-selected')
            {
                $(this).removeClass('ui-selected');
            }
            else
            {
                $(this).addClass('ui-selected');
            }
        }
        else if('shift' == key)
        {
            alternativeFirstClick++;
            if(1 >= alternativeFirstClick)
            {
                alternativeFirstClickIndex = $(this).index();
            }
            else
            {
                alternativeCurClickIndex = $(this).index();
            }
            
            if(-1 != alternativeFirstClickIndex && -1 != alternativeCurClickIndex && alternativeFirstClickIndex > alternativeCurClickIndex)
            {
                if(-1 != alternativeLastClickIndex && alternativeLastClickIndex == alternativeCurClickIndex)
                {
                    $(this).removeClass('ui-selected');
                }
                else
                {
                    $.each($("#alternative_tr tr"),function(i){
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#alternative_tr tr"),function(i){
                        if(alternativeFirstClickIndex >= i && i >= alternativeCurClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    alternativeLastClickIndex = alternativeCurClickIndex;
                }
            }
            else if(-1 != alternativeFirstClickIndex && -1 != alternativeCurClickIndex && alternativeFirstClickIndex < alternativeCurClickIndex)
            {
                if(-1 != alternativeLastClickIndex && alternativeLastClickIndex == alternativeCurClickIndex)
                {
                    $(this).removeClass('ui-selected');
                }
                else
                {
                    $.each($("#alternative_tr tr"),function(i){
                        $(this).removeClass('ui-selected');
                    });
                    $.each($("#alternative_tr tr"),function(i){
                        if(alternativeCurClickIndex >= i && i >= alternativeFirstClickIndex)
                        {
                            $(this).addClass('ui-selected');
                        }
                    });
                    alternativeLastClickIndex = alternativeCurClickIndex;
                }
            }
            else
            {
                if(alternativeFirstClickIndex == alternativeCurClickIndex)
                {
                    $.each($("#alternative_tr tr"),function(i){
                        $(this).removeClass('ui-selected');
                    });
                }
                if(String($(this).attr('class')) == 'ui-selected')
                {
                    $(this).removeClass('ui-selected');
                }
                else
                {
                    $(this).addClass('ui-selected');
                }
            }
        }
        else
        {
            $.each($("#alternative_tr tr"),function(i,v){
                $(this).removeClass('ui-selected');
            });
            $(this).addClass('ui-selected');
        }
    });

    //备选步骤 点击取消全部选中样式
    $("#alternative_div").on('click', function (event) {
        $("#alternative_tr tr").removeClass('ui-selected');
    });

    //备选步骤  双击选项
    // $("#alternative_tr tr").dblclick(function(){
    //     if('ctrl' != key && 'shift' != key)
    //     {
    //         $(this).appendTo('#alternative_next');
    //         bClick('alternative_next');
    //     }
    // });  


    $("#list div").click(function(){
        var i=$(this).index();
        if(ibe!=-1&&key){
            $(this).siblings().removeAttr("class");
            val=",";
            for(var ii=Math.min(i,ibe);ii<=Math.max(i,ibe);ii++){
                val+=ii+",";
                $("#list div").eq(ii).addClass("on");
            }
        }else{
            if(val.indexOf(","+i+",")!=-1){
                val=val.replace(","+i+",",",");
                $(this).removeAttr("class");
            }else{
                val+=i+",";
                $(this).addClass("on");
                ibe=i;
            }
        }
        $("#tt").val(val);
    });

    //点击向上图标
    $('#change_up').on('click', function () {
        var cur_tr = '';
        var pre_tr = '';
        var alternative_next_tr = $("#alternative_next tr");
        $.each(alternative_next_tr,function(i,v){
            var _this = $(this);
            if(0 == _this.index() && -1 != String($(this).attr('class')).indexOf('ui-selected'))
            {
                cur_tr = _this.text();
                var nextAll = _this.nextAll('tr');
                $.each(nextAll,function(i,v){
                    $(this).prev('tr').text($(this).text());
                });
                $(this).removeClass('ui-selected');
                $("#alternative_next tr").last().text(cur_tr);
                $("#alternative_next tr").last().addClass('ui-selected');
                return false;
            }
            else
            {
                cur_tr = $("#alternative_next .ui-selected").text();
                pre_tr = $("#alternative_next .ui-selected").prev('tr').text();
                $("#alternative_next .ui-selected").text(pre_tr);
                $("#alternative_next .ui-selected").prev('tr').text(cur_tr);
                $("#alternative_next .ui-selected").prev('tr').addClass('ui-selected');
                $("#alternative_next .ui-selected").next('tr').removeClass('ui-selected');
                return false;
            }
        });
    });

    //点击向下图标
    $('#change_down').on('click',function(){
        var cur_tr = '';
        var nex_tr = '';
        var alternative_next_tr = $("#alternative_next tr");
        $.each(alternative_next_tr,function(i,v){
            var _this = $(this);
            if(alternative_next_tr.length-1 == _this.index() && -1 != String($(this).attr('class')).indexOf('ui-selected'))
            {
                cur_tr = _this.text();
                var prevAll = _this.prevAll('tr');
                $.each(prevAll,function(i,v){
                    $(this).next('tr').text($(this).text());
                });
                $(this).removeClass('ui-selected');
                $("#alternative_next tr").first().text(cur_tr);
                $("#alternative_next tr").first().addClass('ui-selected');
                return false;
            }
            if(String('ui-selected') == _this.attr('class'))
            {
                cur_tr = $("#alternative_next .ui-selected").text();
                nex_tr = $("#alternative_next .ui-selected").next('tr').text();
                $("#alternative_next .ui-selected").text(nex_tr);
                $("#alternative_next .ui-selected").next('tr').text(cur_tr);
                $("#alternative_next .ui-selected").next('tr').addClass('ui-selected');
                $("#alternative_next .ui-selected").prev('tr').removeClass('ui-selected');
                return false;
            }
        });
    });
});
