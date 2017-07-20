var table, zTreeSetting;
var csrfToken = $("meta[name='_token']").attr("content"); 

$(function () {

    /* dataTables start  */ 
    // console.log(TRANSFER.list)
    table = $('#transfer').dataTable({
        "columns": columns,
        // "ajax": "/hr/transfer/list?_token=" + csrfToken,  
        "ajax": {
            url:TRANSFER.list,
            dataType:'JSONP'
        },
        "scrollX": 746,
        "order": [[0, "asc"]],
        "dom": "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>r>" +
                "t" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": buttons
    });
 
})
    //导出数据
    function addShop(){
        $('#addDepartmentForm input').val('');
    	$("#waiting").fadeIn(200);
    	$("#waiting").fadeOut(300);
        $.ajax({
            type:'post',
            url:TRANSFER.export,
            success:function(msg){
                window.location.href = msg;
                console.log(msg);

            },
            error:function(err){
                console.log(err.responseText); 
            }
        })
    	// $("#openAddByOne").click();
    }


    function submitByAjax(form) {
    	$("#waiting").fadeIn(50);
    	var url = $(form).attr("action");
    	var data = $(form).serialize();
    	var type = $(form).attr('method');
    	$.ajax({
    		type: type,
    		url: url,
    		data: data,
    		dataType: 'jsonp',
    		success: function (msg) {
    				table.fnDraw();
    				$(".close").click();
    				$("#waiting").fadeOut(300);
                     console.log(msg)
    		},
    		error: function (err) {
    			console.log('error')
    		}
    	});
    	return false;
    }

    //
    function showStaffInfo(obj){
        // console.log(obj)
        var url = "/statistic/getstaffdetail";
        var data = {staff_sn:obj.staff_sn};
        $.ajax({
            type:"POST",
            url:url,
            data:data,
            dataType:'json',
            success:function(response){
                //功能展示关闭
                // var str = '';
                // str += '<div class="col-sm-4 panel" style="padding-top:10px">';

                // str += '<div class="clearfix">';
                // str +=    '<label class="control-label col-lg-4"><strong>迟到：</strong></label>';
                //     str +=    '<div class="col-lg-6">';
                //     response['arrive'].forEach(function(val){
                //         str +=      '<p class="form-control-static">'
                //         str +=       val.sign_time;
                //         str +=      '</p>'
                //     })
                //     str +=    '</div>'
                // str +=  '</div>'

                // str += '<div class="clearfix">';
                // str +=    '<label class="control-label col-lg-4"><strong>请假：</strong></label>';
                //     str +=    '<div class="col-lg-6">';
                //     response['holiday'].forEach(function(val){
                //         str +=      '<p class="form-control-static">'
                //         str +=       val.start_time;
                //         str +=      '</p>'
                //     })
                //     str +=    '</div>'
                // str +=  '</div>'

                // str += '</div>';

                // $("#board-right").html(str);
            }
        })




        
    }


    function searchStaff(obj) {
    	var name = $(obj).parent().prev().val();
        // console.log(name);
    	var url = "/hr/staff/search?_token=" + csrfToken;
    	var data = {"target": {"staff_sn": "staff_sn", "realname": "staff_name"}};
    	$.ajax({
    		type: "POST",
    		url: url,
    		data: data,
    		dataType: 'text',
    		success: function (msg) {
    			$("body").append(msg);
    			$("#openSearchStaffResult").click();
    		},
    		error: function (err) {
    			alert(err.responseText);
    		}
    	});
    }

    function searchShop(obj,str) {
        var name = $(obj).parent().prev().val();

        var url = "/hr/shop/search?_token=" + csrfToken;
        // var data = {"target": {"id": str+"_shop_sn", "shop_name": str+"_shop_name"}};
        var data = {"target": {"shop_sn": str+"_shop_sn", "name": str+"_shop_name"}};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'text',
            success: function (msg) {
                $("body").append(msg);
                $("#openSearchShopResult").click();
            },
            error: function (err) {
                alert(err.responseText);
            }
        });
    }




    function setStaff(id) {
        // $("#waiting").fadeIn(200);
        var url = "/hr/shop/shop_modal_set?_token=" + csrfToken;
        var data = {"role_id": id};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'text',
            success: function (msg) {
                $("body").append(msg);
                $("#openSetStaff").click();
                $("#setStaff").bind("remove", function () {
                    table.fnDraw();
                });
                $("#waiting").fadeOut(300);
                // console.log(msg)
            },
            error: function (err) {
                document.write(err.responseText);
            }
        });
        // $("#openEditStaff").click();
    }