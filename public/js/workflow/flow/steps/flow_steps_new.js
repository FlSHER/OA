//新建步骤
function add_new_prcs(url)
{
    // var cur_sequence = $('#guide_list tr').length;
    // cur_sequence += 1;
    // var new_url = url + "&cur_sequence=" + cur_sequence;
    window.open(url, 'newwindows');
}

//编辑该步骤的各个属性
function add_prcs(id, id_name) {
    var url = './updateFlowStepsList?id=' + id + '&id_name=' + id_name;
    window.open(url, 'newwindows');
}

/**
 * 删除流程步骤
 * @param {type} id
 * @returns {undefined}
 */
function delete_item(id) {
    if (confirm("确认删除该步骤？")) {
        var url = './deleteFlowSteps';
        $.ajax({
            type: 'post',
            url: url,
            data: {id: id},
            headers:{
                'X-CSRF-TOKEN':$('meta[name="_token"]').attr('content')
            },
            success: function (data) {
                if (data == "success") {
                    alert("删除成功");
                    location.reload();
                } else {
                    alert("删除失败");
                }
            }
        });
    }
}
//流程步骤克隆
function clone_item(id) {
    if(confirm("确认克隆该步骤？")){
        var url = "./cloneFlowSteps";
        $.ajax({
           type:'post',
           url:url,
           data:{id:id},
            headers:{
                'X-CSRF-TOKEN':$('meta[name="_token"]').attr('content')
            },
           success:function(data){
               if(data =="success"){
                   alert("克隆成功");
                   location.reload();
               }else if(data == "error"){
                   alert("克隆失败");
               }
           }
        });
    }
}