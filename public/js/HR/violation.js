
var oaFormOption = {
    callback: {
        submitSuccess: oaFormSubmitSuccess,
        afterInit: oaFormAfterInit
    }
};
//大爱选择框类型
$("#violationtype").on("change", function () {
    var type_id = $(this).find("option:selected").val();
    $("#violationreason option").hide();
    $("#violationreason option").each(function () {
        if ($(this).attr("type_id") == type_id || $(this).attr("type_id") == 0) {
            $(this).show();
        }
    });
    var currentOption = $("#violationreason option:selected");
    if (currentOption.attr("type_id") != type_id && currentOption.attr("type_id") != 0) {
        currentOption.prop("selected", false);
    }
    $("#violationreason").change();
});
//大爱选择原因
$("#violationreason").on("change", function () {
    var reasonContent = $(this).find("option:selected").val();
    if (reasonContent === "0") {
        $("#violationotherreason").prop("disabled", false);
    } else {
        $("#violationotherreason").prop("disabled", true).val("");
    }
});
/*隐藏其他框 */
$("#conceal").hide();
//大爱单编辑类型获取
//大爱单编辑类型获取
$("#violation_type").on("change", function () {
    var type_id = $(this).find("option:selected").val();
    $("#violation_reason option").hide();
    $("#violation_reason option").each(function () {
        if ($(this).attr("type_id") == type_id || $(this).attr("type_id") == 0) {
            $(this).show();
        }
    });
    var currentOption = $("#violation_reason option:selected");
    if (currentOption.attr("type_id") != type_id && currentOption.attr("type_id") != 0) {
        currentOption.prop("selected", false);
    }
    $("#violation_reason").change();
});


$("#violation_type").change();
/*隐藏金额*/
$('#reveal ').hide();
//大爱单编辑原因获取
$("#violation_reason").on("change", function () {
    var reasonContent = $(this).find("option:selected").val();
    if (reasonContent === "0") {
     $("#violation_other_reason").prop("disabled", false);
	 $("#conceal").show();
	 $('#reveal').show();
	 $('#reveal input').attr('disabled',false);
    } else {
        $("#violation_other_reason").prop("disabled", true).val("");
		$("#conceal").hide();
		//$('#reveal').hide();
    }
});
/*选择市场还是后台*/
	$('#choose input[type="radio"]').each(function(){
			
		$(this).click(function(){
			if($(this).val()=='后台'){
				$('#reveal').hide();
			} if ($(this).val()=='市场'){
				$('#reveal').show();
				$('#reveal input').attr('disabled',false);
			}
			
		});
	});
/*金额正则*/
//员工选择
function searchStaff(obj) {
    var name = $(obj).parent().prev().attr("name");
    var sn = name.replace("_name", "_sn");
    var url = "/hr/staff/search";
    var data = {"target": {"staff_sn": sn, "realname": name}};
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
            document.write(err.responseText);
        }
    });
}
//开单人选择
function searchStaf(obj) {
    var name = $(obj).parent().prev().attr("name");
    var sn = name.replace("_name", "_sn");
    var url = "/hr/staff/search";
    var data = {"target": {"staff_sn": sn, "realname": name}};
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
            document.write(err.responseText);
        }
    });
}
/* 添加大爱单 start*/
/*编辑获取大爱单*/
function addDataTolsf(datas) {
    var url = '/hr/violation/info';
    var data = {'id': datas};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#jsform1').val(data.staff_name);
            $('#jsform2 select').val(data.type_id);
            $("#violationreason").val(0);
            $('#violationreason option').each(function () {
                if ($(this).val() == data.reason) {
                    $("#violationreason").val(data.reason);
                    return false;
                }
            });
            if($("#violationreason").val()==0){
                $("#violationotherreason").val(data.reason);
            }
          
            $('#jsform7').val(data.staff_sn);
            return false;
        }
    });
    //staffColumns.fnDraw();
}
/*编辑大爱单*/
$('#lsform').on('submit', daasfslsf);
function daasfslsf() {
    var url = '/hr/violation/enter/edit';
    var data = $(this).serialize();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            staffColumns.fnDraw();
			return false;
        }
    });
    return false;
}
/*删除大爱单*/
function deleteviolation(datas) {
    var _confirm = confirm("确认删除？");
    if (_confirm) {
        var url = '/hr/violation/enter/delete';
        var data = {'id': datas};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                if (msg === 1) {
                    staffColumns.fnDraw();
					iication.fnDraw();
					
                }
            }
        });
    }
}

/*保存大爱单*/
$("#jsformlsf").on('submit', daasflsf);
function daasflsf() {
	$("#jsformlsf form").oaForm(oaFormOption);
    if ($("input[name = 'committed_at']").val() === "") {

        alert("对不起，违纪时间不能为空！");
        return false;
    }
    if ($("input[name = 'supervisor_name']").val() === "") {

        alert("对不起，开单人不能为空！");
        return false;
    }
	if($("#choose :radio:checked").length==0){
		alert("对不起，选择项不能为空！");
        return false;
	}
    var url = '/hr/violation/enter/add';
    var data = $(this).serializeArray();
    for (i = 1; i < data.length; i++) {
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
			alert(data);
			if(data['state']==0){
				alert(data['file_name']);
				staffColumns.fnDraw();
			}else{
		         document.getElementById("jsformlsf").reset(); 
			       staffColumns.fnDraw();
			}
           
        }
    });
    return false;
}

/* 添加大爱单 end*/


/*大爱类型 start*/

/*获取大爱类型列表*/
var ification = $("#classification").dataTable({
   // "info": false,
    "lengthChange": false,
    //"searching": false,
    //"paging": false,
    "columns": [
       // {"data": "id", "title": "id"},
        {"data": "name", "title": "类型"},
//        {"data": "brand", "title": "品牌"},
//        {"data": "department", "title": "部门"},
//        {"data": "position", "title": "职位"},
//        {"data": "reason", "title": "原因"},
//        {"data": "committed_at", "title": "违纪时间"},
//        {"data": "price", "title": "金额"},
//        {"data": "supervisor_name", "title": "开单人"},
        {"data": "id", "title": "操作", "sortable": false,
            "render": function (datas, type, row, meta) {
                return '<a href="#" onclick="editorDataTolsf(' + datas + ')"  vid="' + datas + '" title="编辑"data-toggle="modal" data-target="#myModal"class="edit btn btn-sm btn-primary" ><i class="fa fa-edit fa-fw"></i></a><a href="#" title="删除"class="btn btn-sm btn-danger" vid="' + datas + '" onclick="deletecategory(' + datas + ')" ><i class="fa fa-trash-o fa-fw"></i></a>';
            }
        }
    ],
    "ajax": "/hr/violation/category/list",
});
/*添加大爱类型*/
$('#classificationform').on('submit', daasfsfy);
function daasfsfy() {
    var url = '/hr/violation/category/add';
    var data = $(this).serialize();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            ification.fnDraw();
        }
    });
    return false;
}
//编辑获取大爱类型
function editorDataTolsf(datas) {
    var url = '/hr/violation/category/info';
    var data = {'id': datas};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#lsificationform1').val(data.name);
            $('#lsificationform2').val(data.id);
            ification.fnDraw();
        }
    });
}
;
//编辑大爱类型
$('#lsificationform').on('submit', lsfdaasfs);
function lsfdaasfs() {
    var url = '/hr/violation/category/edit';
    var data = $(this).serialize();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            ification.fnDraw();
        }
    });
    return false;
}
//删除大爱类型
function deletecategory(datas) {
    var _confirm = confirm("确认删除？");
    if (_confirm) {
        var url = '/hr/violation/category/delete';
        var data = {'id': datas};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                if (msg === 1) {
                    ification.fnDraw();
                }
            }
        });
    }
}
/*大爱类型 end*/


/*大爱原因 start*/
/*获取大爱原因列表*/
var whyreaso = $("#whyreason").dataTable({
   //"info": false,
    //"lengthChange": false,
//"searching": false,
   // "paging": false,
    "columns": [
       // {"data": "id", "title": "id"},
        {"data": "type.name", "title": "类型"},
        {"data": "content", "title": "原因"},
        {"data": "prices", "title": "金额"},
//        {"data": "position", "title": "职位"},
//        {"data": "reason", "title": "原因"},
//        {"data": "committed_at", "title": "违纪时间"},
//        {"data": "price", "title": "金额"},
//        {"data": "supervisor_name", "title": "开单人"},
        {"data": "id", "title": "操作", "sortable": false,
            "render": function (datas, type, row, meta) {
                return '<a href="#" onclick="editorreason(' + datas + ')" data-toggle="modal" vid="' + datas + '" title="编辑" data-target="#myModal"class="edit btn btn-sm btn-primary" ><i class="fa fa-edit fa-fw"></i></a><a href="#" title="删除"class="btn btn-sm btn-danger" vid="' + datas + '" onclick="deletereason(' + datas + ')" ><i class="fa fa-trash-o fa-fw"></i></a>';
                whyreaso.fnDraw();
            }
        }
    ],
    "ajax": "/hr/violation/reason/list" ,
});
/*添加大爱原因*/
$('#reasonwhy').on('submit', daasfsf);
function daasfsf() {
    var url = '/hr/violation/reason/add';
    var data = $(this).serialize();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            whyreaso.fnDraw();
        }
    });
    return false;
}
//编辑大爱原因
$('#lsification').on('submit', daalsfsfs);
function daalsfsfs() {
    var url = '/hr/violation/reason/edit';
    var data = $(this).serialize();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            whyreaso.fnDraw();
        }
    });
    return false;
}
/*编辑获取大爱原因*/
function editorreason(datas) {
    var url = '/hr/violation/reason/info';
    var data = {'id': datas};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#lsifreason1').val(data.content);
            $('#lsifreason2').val(data.id);
            $('#lsifreason3').val(data.type.name);
            $('#lsifreason4').val(data.type.id);
			$('#lsifreason5').val(data.prices);
            whyreaso.fnDraw();
        }
    });
}



//删除大爱原因
function deletereason(datas) {
    var _confirm = confirm("确认删除？");
    if (_confirm) {
        var url = '/hr/violation/reason/delete';
        var data = {'id': datas};
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                if (msg === 1) {
                    whyreaso.fnDraw();
                }
            }
        });
    }
}
/*大爱原因 end*/



/*提交大爱单*/
function violationlsf(obj) {
    var datas = staffColumns.api();
    var data = datas.ajax.json();
    var url = '/hr/violation/enter/submit';
    $.ajax({
        type: "POST",
        url: url,
        data: data,
		dataType:'json',
        success: function (dataes) {
			if(dataes['state']==1){
				alert(dataes['file_name']);
				staffColumns.fnDraw();
			}else{
				alert('提交完成');
				staffColumns.fnDraw();
			}
        }
    });
    return false;
}


/*获取提交的大爱单*/
function addDataTolsfla(datas) {
    var url = '/hr/violation/info';
    var data = {'id': datas};
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#jsform1').val(data.staff_name);
            $('#jsform2 select').val(data.type);
            $("#violationreason").val(0);
            $('#violationreason option').each(function () {
                if ($(this).val() == data.reason) {
                    $("#violationreason").val(data.reason);
                    return false;
                }
            });
            if($("#violationreason").val()==0){
                $("#violationotherreason").val(data.reason);
            }
            $('#jsform4').val(data.committed_at);
            $('#jsform5').val(data.price);
            $('#jsform6').val(data.supervisor_name);
            $('#jsform7').val(data.staff_sn);
            $('#jsform8').val(data.brand);
            $('#jsform9').val(data.department);
            $('#jsform10').val(data.position);
            $('#jsform11').val(data.supervisor_sn);
            $('#jsform12').val(data.id);
        }
    });
    staffColumns.fnDraw();
}
/*确认已交钱*/
function delivery(datas){
	var url = '/hr/violation/deliver';
    var data = {'id': datas};
	$.ajax({
		type:"POST",
		url:url,
		data:data,
		success:function (data){
			iication.fnDraw();
		}
	})
}
/*修改大爱单*/
$('#violationform').on('submit', daasfs);
function daasfs() {
    var url = '/hr/violation/amend';
    var data = $(this).serialize();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            iication.fnDraw();
        }
    });
    return false;
}
/*导出*/
function ertae() {
    var tableApi = iication.api();
    var dataCount = tableApi.ajax.json().recordsFiltered;
    if (dataCount == 0) {
        alert("无可用信息");
    } else if (confirm("确认以当前条件导出？")) {
        oaWaiting.show();
        var params = tableApi.ajax.params();
        delete params.length;
        var url = "/hr/violation/export";
        $.ajax({
            type: "POST",
            url: url,
            data: params,
            dataType: 'json',
            success: function (msg) {
                if (msg['state'] == 1) {
                    var fileName = msg['file_name'];
                    window.location.href = '/storage/exports/' + fileName + '.xlsx';
                    oaWaiting.hide();
                }
            },
            error: showErrorPage
        });
    }
}
function showErrorPage(err) {
    document.write(err.responseText);
}

 /*批量导入*/
            $("#import").on("change", function () {
                var formdata = new FormData();
                var fileObj = $(this).get(0).files;
                var url = "{{asset('hr/violation/enter/import')}}";
                formdata.append("import", fileObj[0]);
                formdata.append("_token", "{{csrf_token()}}");
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formdata,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (msg) {
                        if (msg.status === 1) {
                            alert("成功上传" + msg.count + "条违纪");
                            location.href = "{{asset('hr/violation')}}";
                        } else if (msg.status === -1) {
                            alert(msg.message);
                            location.reload();
                        } else {
                            alert("上传失败");
                            location.reload();
                        }
                    },
                    error: function (err) {
                        alert(err.responseText);
                    }
                });
            });
			
			
			$("#bulkimpo").css({
				"position":"absolute",
				"top":"-44px",
				"left":"36%",
			});
			function bulkimport(){
				$("#bulkimpo").css({
				"position":"absolute",
				"top":"-0px",
				"left":"36%",
			});
			$("#bulkimp").unbind('click');
			}
			
function oaFormAfterInit(obj) {
    var formType = $(this).attr("id").replace("Form", "");
    var openBtn = $('#' + formType + 'ByOne').modal('show');
}

function oaFormSubmitSuccess(msg, obj) {
    table.fnDraw(false);
    $(".close").click();
}