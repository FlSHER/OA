<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <style>
            .section{border:1px solid #cccccc;width:80%;}
            .section thead{border:1px solid red;}
            .page{width:80%;height:40px;}
            .page ul{width:30%;height: 40px;margin:0 auto;text-align:center;font-size:12px;}
            .page ul li{width:10%;border: 1px solid #cccccc;height: 30px;margin:0 auto;float: left;list-style-type: none;margin:0 5px;padding: 3px;line-height:30px;cursor: pointer;}
        </style>
        <script src="{{source('/js/jquery-3.1.1.min.js')}}"></script>
    </head>
    <body>
        <table id="title" class="TableBlock" border="0" width="100%" style="background:#c4de83;"> 
            <tbody>
                <tr class="TableHeader">
                    <td> 
                        查询条件:  未设置查询条件！ 
                        <input type="button" class="one_Add" id="oneAdd" value="一键添加" onclick="one_Add()"> 
                    </td>
                </tr> 
            </tbody>
        </table> 
        <table class="section" id="section">
            <thead style="background:#989898;"> 
                <tr>
                    <td><input type="checkbox" onclick="checkAll(this)"/>全选</td>
                    @foreach($fields as $k=>$v)
                    <td>{{$v}}</td>
                    @endforeach
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $k=>$v)
                <tr>
                    <td><input type="checkbox"/></td>
                    @foreach($v as $key=>$val)
                    <td class="fieldsData">{{$val}}</td>
                    @endforeach
                    <td><a href="javascript:void(0)" onclick="addListData(this)">+添加</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="page">

            <ul>
                <li id="prev_page"onclick="prevPage()" hidden><</li>
                <li id="next_page" onclick='nextPage()'>></li>
            </ul>
            <p>
                第<input id='gopage' value='' onkeyup="this.value=this.value.replace(/\D/g,'')" style="width:30px;">页
                <button onclick="goto()">go</button>
                &nbsp;
                页码:<i id='p'>{{$page['p']}}</i>/<i id="pages">{{$page['pages']}}</i> 
                总条数： <span>{{$page['count']}}</span>
            </p>
            <input type="hidden" id="id" value="{{$page['id']}}"/>
            <input type="hidden" id="table" value="{{$page['table']}}"/>
            <input type="hidden" id="fields_arr" value="{{$page['fields_arr']}}"/>
            <input type="hidden" id="opener_table_id" value="{{$table_id}}"/>
        </div>
    </body>
</html>
<script>
var opener_table_id = $('#opener_table_id').val()//父页面table的id

//全选
    function checkAll(i){
        if ($(i).is(':checked')){
            $('#section tbody tr input[type="checkbox"]').attr('checked', true);
        } else{
            $('#section tbody tr input[type="checkbox"]').attr('checked', false);
        }
    }
//点击添加
    function addListData(t){
    var parent_table = $('#'+opener_table_id, window.opener.document);
    var td_obj = $(t).parents('tr').find('.fieldsData');
    var td_arr = new Array();
    $.each(td_obj, function(i, v){
        td_arr.push($(v).text());
    });
    var dbfields = parent_table.attr('dbfields');
    dbfields = dbfields.replace(/`$/, '');
    var dbfieldsArr = dbfields.split('`');
    $.each(dbfieldsArr, function(i, v){
        if ("0" !== v){
            parent_table.find('tbody tr').eq({{ $index }}).find('td').eq((i + 1)).children().val(td_arr.slice(0, 1));
            td_arr.shift();
        }
             window.close();
        });
    }

//一键添加
function one_Add(){
    var parent_table = $('#'+opener_table_id, window.opener.document);
    var tr_arr = $("#section tbody tr");
    var eq =<?php echo $index;?>;
    var num =eq;
    var td_arr_all =new Array();
    var check = false;
    $.each(tr_arr,function(){
        if($(this).find('td input[type="checkbox"]').is(':checked')){
            check = true;
            num++;
            var td_object = $(this).find('.fieldsData');
            var td_arr =new Array();
            $.each(td_object,function(k,v){
                td_arr.push($(v).text())
            });
            td_arr_all.push(td_arr);
 
        }
    });
    if(check == false){
        alert('请至少选择一个，才能一键添加');
        return false;
    }
    addTr(parent_table,num);//给父页面添加tr标签
    dbfieldsAttr(parent_table,eq,num,td_arr_all);//把子页面的值装入父页面去
}

//给父页面添加tr标签
function addTr(parent_table,num){
    var parent_tr_length = parent_table.find('tbody tr').length-1;
    var add =parent_table.next('span').find('.add_new');
   if(num>parent_tr_length){
       for(var i=1;i<=num- parent_tr_length;i++){
          add.trigger('click');
       }
   }
}
//把子页面的值装入父页面去
function dbfieldsAttr(parent_table,eq,num,td_arr_all){

     var dbfields = parent_table.attr('dbfields');
    dbfields = dbfields.replace(/`$/, '');
    var dbfieldsArr = dbfields.split('`');
    $.each(dbfieldsArr, function(i, v){
        if ("0" !== v){
            for(var j=eq,k=0;j<num,k<td_arr_all.length;j++,k++){
                parent_table.find('tbody tr').eq(j).find('td').eq((i + 1)).children().val(td_arr_all[k].slice(0, 1));
                td_arr_all[k].shift();
            }
        }
        window.close();
     });
}

//上一页
    function prevPage(){
         var p =$('#p').text();
          p = parseInt(p);
         var pages = $('#pages').text();
         pages = parseInt(pages);
          if(p>1){
              p--;
          }
          $('#p').text(p);
          getdata(p);
          if(p < pages){
               $("#next_page").show();
          }
          if(p<=1){
               $("#prev_page").hide();
          }
    }
//下一页
    function nextPage(){
         var p =$('#p').text();
         p = parseInt(p);
        var pages = $('#pages').text();
        pages = parseInt(pages);
        if(p < pages){
             p++;
        }
        $('#p').text(p);
        if(p>=pages){
            $("#next_page").css('display','none');
            $("#prev_page").css('display','block');
        }
        if(p>1){
            $("#prev_page").show();
        }
        getdata(p);
    }
    //跳转页
    function goto(){
        var p =$("#gopage").val();
        if(p==''){
            return false;
        }
        var pages = $("#pages").text();
        pages = parseInt(pages);
        p = parseInt(p);
        if(p>pages){
            p=pages;
        }
        if(p<1){
            p=1;
        }
        $("#gopage").val(p)
        $('#p').text(p);
       getdata(p);
    }
 
    
    function getdata(p){
        var id = $('#id').val();
        var table = $('#table').val();
        var fields_arr = $('#fields_arr').val();
        var url = "/workflow/fieldsPage";
        $.ajax({
            type:'post',
            url:url,
            data:{p:p, id:id, table:table, fields_arr:fields_arr},
            dataType:'JSON',
            headers:{
                'X-CSRF-TOKEN':"{{csrf_token()}}"
            },
            success:function(data){
             var str='';
             $.each(data,function(k,v){
                 var td_arr ='';
               $.each(v,function(j,i){
                   td_arr +='<td class="fieldsData">'+i+'</td>';
               })
                 str +='<tr>'+
                    '<td><input type="checkbox"/></td>'+td_arr+'<td><a href="javascript:void(0)" onclick="addListData(this)">+添加</a></td>'+
                '</tr>';
             })
             $('#section tbody').html(str);
            }
        })
    }
</script>