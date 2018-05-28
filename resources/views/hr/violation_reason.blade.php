@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.dataTables.css')}}" />
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/buttons.bootstrap.css')}}" />

<div class="row">
    <div class="col-lg-9">
        <section class="panel">
            <header class="panel-heading">
                大爱原因列表
            </header>
            <div class="panel-body">
                <table class="table table-striped table-bordered dataTable no-footer" id="whyreason">
                </table>
            </div>
        </section>
    </div>
    <section id="board-right"></section>

<!--编辑大爱原因    start-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form class="form-horizontal" action="" id="lsification"method="post">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="form-group">
                                <label class="control-label col-md-2">类型：</label>
                                <div class="col-md-4 input-group">
                                    <input class="form-control" type="text" id="lsifreason3" value="" title="类型" style="background-color:#fff;"/>
                                </div>
                                <input class="form-control" name="type_id" value=""type="hidden" id="lsifreason4"/>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2">原因：</label>
                                <div class="col-md-4 input-group">
                                    <input class="form-control" name="content" type="text" id="lsifreason1" value="" title="原因" style="background-color:#fff;"/>
                                </div>
                                <input class="form-control" name="id" value=""type="hidden" id="lsifreason2"/>
                            </div>
							<div class="form-group">
                                <label class="control-label col-md-2">金额：</label>
                                <div class="col-md-4 input-group">
                                    <input class="form-control" name="prices" type="text" id="lsifreason5" value="" title="金额" style="background-color:#fff;"/>
                                </div>
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
        <!--编辑大爱原因    end-->
<section class="col-md-3">
            <div class="panel">
                <header class="panel-heading">添加大爱原因</header>
                <!-- 列表 start -->
                <div class="panel-body">
                    <form class="form-horizontal" action="" id="reasonwhy"method="post">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="form-group">
                                <label class="col-sm-3 control-label">类型：</label>
                                <div class="col-sm-9">
                                    <select class="form-control pull-left" name="type_id" style="width:85%" id="violation_type">
                                    @foreach($typed as $typesd)
                                        <option value="{{$typesd->id}}">{{$typesd->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">原因：</label>
                            <div class="col-md-8">
                                <input class="form-contro1" name="content" type="text"  value="" style="width:95%"title="原因" style="background-color:#fff;" />
                            </div>
                        </div>
						<div class="form-group">
                                <label class="control-label col-md-3">金额：</label>
                                <div class="col-md-4 input-group">
                                    <input class="form-control" name="prices" type="text" id="lsifreason1" value="" title="原因" style="background-color:#fff;"/>
                                </div>
                                <input class="form-control" name="id" value=""type="hidden" id="lsifreason2"/>
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
		</div>

@endsection
@section('js')
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/buttons.colVis.js')}}"></script>
<script type="text/javascript" src="{{asset('js/HR/violation.js')}}"></script>
@endsection
