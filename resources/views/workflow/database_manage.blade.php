@extends('layouts.admin')
@inject('HRM',HRM)

@section('content')
@include('workflow.common.common')
<div class="col-lg-12">
    <section class="panel">
        <header class="panel-heading">
            管理设置<span style="color: #65cea7;"> / </span>数据源管理
        </header>
        <article class="col-lg-2 col-md-2 col-sm-3">
            <div class="panel">
                <div class="panel-body">
                    <span><a href="javascript:databaseManageForm.add_database_manage();">新建数据源</a></span>
                </div>
            </div>
            <div class="panel">
                <div class="panel-body" id="database_name">
                    @foreach($data as $v)
                    <p database_manage_id="{{$v['id']}}" style="cursor :pointer">{{$v['database']}}</p>
                    @endforeach
                </div>
            </div>
        </article> 

        <article class="col-lg-10 col-md-10 col-sm-9" id="database_section" style="display:none;">
            <div class="panel">
                <div class="panel-body">
                    <section  class="col-lg-5 col-md-8 col-sm-7 ">
                        <form class="form-horizontal adminex-form" method="post" id="databaseManage_form" action="{{asset(route('workflow.databaseManageAdd'))}}" >
                            <div class="form-group">
                                <label class="col-lg-3 col-sm-12   control-label"><i class="faX">*</i>数据源名称：</label>
                                <div class="col-sm-12 col-lg-9">
                                    <input type="text" class="form-control" name="database" maxlength="20">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3  col-sm-12 control-label"><i class="faX">*</i>数据库类型：</label>
                                <div class=" col-sm-12 col-lg-9">
                                    <select  class="form-control" name="connection">
                                        <option value="mysql">MySql</option>
                                        <option value="sqlsrv">SQL SERVER</option>
                                        <!--<option value="oracle">Oracle</option>-->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3  col-sm-12 control-label"><i class="faX">*</i>IP地址：</label>
                                <div class=" col-sm-12 col-lg-9">
                                    <input type="text" class="form-control"name="host" value="127.0.0.1"  maxlength="30"onkeyup="this.value = value.replace(/[^\d|.]/g, '');
                                            if (this.value == '')
                                                (this.value = '');">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3  col-sm-12 control-label">端口：</label>
                                <div class=" col-sm-12 col-lg-9">
                                    <input class="form-control" type="text" name="port" maxlength="5"value="3306"  onkeyup="this.value = this.value.replace(/[^\d]+/g, '');">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3  col-sm-12 control-label"><i class="faX">*</i>用户名：</label>
                                <div class=" col-sm-12 col-lg-9">
                                    <input class="form-control" type="text" name="username" maxlength="20">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3  col-sm-12 control-label">密码：<i class="fa fa-eye"  id="password_click" style="cursor:pointer;"></i></label>
                                <div class=" col-sm-12 col-lg-9">
                                    <input class="form-control" type="password" name="password" maxlength="30" >
                                </div>
                            </div>
                            <div id="hidden_input_id">

                            </div>
                            <input type="button" value="保存设置" onclick="databaseManageForm.server_submit();">
                            <input type="button" value="删除配置" onclick="databaseManageForm.delServer();">
                            <input type="button" value="测试链接" onclick="databaseManageForm.conn_submit();">
                        </form>
                    </section>
                    <section  class="col-lg-5 col-md-6 col-sm-5 " style="border:1px solid #c1baba;float:right;background:#FFFCEB;">
                        <h2> 说明：</h2>

                        <p style="color:red;">注：此模块功能专为技术支持和实施人员设定,客户请在相关技术人员的指导下配合使用!</p>

                        <b>第一步： 设置数据源，保存ERP系统数据源的配置信息,以供连接使用。</b>
                        <p>数据源名称：数据源的名称标识 如:ERP系统数据源</br>
                            数据源地址：数据源服务器的IP地址 如:192.168.0.1</br>
                            数据源端口：数据源数据库的端口号 如:1433</br>
                            用户名：数据源数据库的用户名 如:sa</br>
                            密码：数据源数据库的用户密码,用于连接数据库验证</p>

                        <b>第二步： 在已经配置好的数据源中配置相关的外部数据源列表信息。</b>

                        <b>第三步： 工作流设计表单时，指定所需要使用的数据源信息。</b>
                    </section>
                </div>
            </div>
        </article>

    </section>
</div>
@endsection
@section('js')
@parent
<script type="text/javascript">
    var databaseManageForm = {
        query: $('#databaseManage_form'), //表单id
        database_section: $('#database_section'), //表单内容
    };

    $(function () {
        //数据源名称的点击事件 获取编辑数据
        $('#database_name p').on('click', function () {
            var id = $(this).attr('database_manage_id');
            var url = '/workflow/databaseManageUpdateBefore';
            $.ajax({
                type: 'post',
                data: {id: id},
                url: url,
                dataType: 'json',
                success: function (data) {
                    if (data != '') {
                        databaseManageForm.database_section.show();
                        databaseManageForm.query.find('#hidden_input_id').html('<input type="hidden" name="id" value="' + data.id + '"/>');
                        databaseManageForm.query.find('input[name="database"]').val(data.database);
                        databaseManageForm.query.find('select[name="connection"]').val(data.connection);
                        databaseManageForm.query.find('input[name="host"]').val(data.host);
                        databaseManageForm.query.find('input[name="password"]').val(data.password);
                        databaseManageForm.query.find('input[name="port"]').val(data.port);
                        databaseManageForm.query.find('input[name="username"]').val(data.username);
                        databaseManageForm.query.find('input[name="password"]').attr('type', 'password');
                    }
                }
            });
        });

        //数据源管理点击图标显示password 为文本
        databaseManageForm.query.find('#password_click').on('click', function () {
            var type = databaseManageForm.query.find('input[name="password"]').attr('type');
            if (type === 'password') {
                databaseManageForm.query.find('input[name="password"]').attr('type', 'text');
            } else if (type === 'text') {
                databaseManageForm.query.find('input[name="password"]').attr('type', 'password');
            }
        });

    });


    //新建数据源
    databaseManageForm.add_database_manage = function () {
        this.database_section.show();
        this.query.find('#hidden_input_id').html('');
        this.query[0].reset();
        this.query.find('input[name="password"]').attr('type', 'password');
    }


    //保存设置
    databaseManageForm.server_submit = function () {

        var url = this.query.attr('action');
        var data = this.query.serialize();
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            data: data,
            success: function (data) {
                if (data.result === 'checkError') {//表单验证失败
                    alert(data.msg);
                } else if (data.result === 'insertSuccess') {
                    location.reload();
                } else if (data.result === 'updateSuccess') {
                    location.reload();
                }
            }
        });
    }

    //删除配置
    databaseManageForm.delServer = function () {
        var id = this.query.find('#hidden_input_id input[name="id"]').val();
        if (id) {
            var url = '/workflow/databaseManageDelete';
            $.ajax({
                type: 'post',
                url: url,
                data: {id: id},
                success: function (data) {
                    if (data.result === 'deleteSuccess') {
                        location.reload();
                    } else {
                        alert("删除失败");
                    }
                }
            });
        } else {
            alert("删除失败");
        }
    }


//测试连接
    databaseManageForm.conn_submit = function () {
        var data = this.query.serialize();
        var url = '/workflow/databaseTestCheck';
        $.ajax({
            type: 'post',
            url: url,
            data: data,
            success: function (data) {
                if (data === 'success') {
                    alert('测试连接成功');
                }else{
                    alert('测试连接失败');
                }
            },
            error:function(msg){
                 alert('测试连接失败');
            }
        });
    }
</script>
@endsection
