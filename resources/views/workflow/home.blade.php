@extends('layouts.admin')
@section('css')
@parent
<!--home-->
<link rel="stylesheet" href="{{asset('plug_in/datatables/css/dataTables.bootstrap.css')}}" />
<!-- zTree css -->
<link rel="stylesheet" href="{{asset('plug_in/ztree/css/metroStyle.css')}}" />
@endsection
@section('content')
@include('workflow.common.common')
<div class="col-lg-12">
    <section class="panel">
        <header class="panel-heading">
            运行流程
        </header>
       
        <!--列表数据start-->
        <div class="panel-body">
            <div class="adv-table">
                <table  class="display table table-bordered table-striped" id="flowRunTable">

                </table>
               
            </div>
        </div>
        <!--列表数据end-->
    </section>
</div>
@stop
@section('js')
@parent
<script type="text/javascript" src="{{asset('plug_in/datatables/js/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('plug_in/datatables/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('js/workflow/form/flow_run.js')}}"></script>
@stop
