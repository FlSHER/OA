@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />

<div class="col-lg-8">
        <section class="panel">
            <header class="panel-heading">
                大爱类型列表
            </header>
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="classification">
                </table>
            </div>
        </section>
    </div>
    <section id="board-right"></section>
<!--编辑大爱类型    start-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form class="form-horizontal" action="" id="lsificationform"method="post">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="form-group">
                                <label class="control-label col-md-2">类型：</label>
                                <div class="col-md-4 input-group">
                                    <input class="form-control" name="name" type="text" id="lsificationform1" value="" title="类型" style="background-color:#fff;"/>
                                </div>
                                <input class="form-control" name="id" value=""type="hidden" id="lsificationform2"/>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-default">修改</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--编辑大爱类型    end-->
<section class="col-md-4">
            <div class="panel">
                <header class="panel-heading">添加大爱类型</header>
                <!-- 列表 start -->
                <div class="panel-body">
                    <form class="form-horizontal" action="" id="classificationform"method="post">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="form-group">
                            <label class="control-label col-md-2">类型：</label>
                            <div class="col-md-4 input-group">
                                <input class="form-control" name="name" type="text"  value="" title="姓名" style="background-color:#fff;" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-default">添加</button>
                            </div>
                        </div>
                    </form>   
                </div>
                <!-- 列表 end -->
            </div>
        </section>

@endsection
@section('js')
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<script type="text/javascript" src="{{asset('js/HR/violation.js')}}"></script>
@endsection
