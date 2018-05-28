<div class="fail-info panel-group">
    @if(count($fails)>0)
    <p>{{date('Y-m-d H:i:s',time())}} 成功上传 <span class="badge badge-success">{{$count}}</span> 条,失败 <span class="badge badge-important">{{count($fails)}}</span> 条</p>
    <div class="panel">
        <div class="panel-heading dark">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">失败明细</a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse">
            <div style="overflow:auto;max-height:400px;width:100%;">
                <table class="table table-sm table-bordered" style="margin-bottom:0;color:#333;">
                    <thead>
                        <tr><th class="col-lg-2">员工姓名</th><th class="col-lg-4">部门</th><th class="col-lg-6">失败原因</th></tr>
                    </thead>
                    <tbody>
                        @foreach($fails as $v)
                        <?php $v['reason'] = is_string($v['reason']) ? explode(';', $v['reason']) : $v['reason']; ?>
                        @foreach($v['reason'] as $reason)
                        <tr>
                            @if($loop->first)
                            <td style="vertical-align:middle;" rowspan="{{count($v['reason'])}}">{{$v['realname'] or ''}}</td><td style="vertical-align:middle;" rowspan="{{count($v['reason'])}}">{{$v['department']['full_name'] or ''}}</td>
                            @endif
                            <td>{{$reason}}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <a href="{{Storage::url('exports/'.$failReport.'.xlsx')}}">下载失败明细</a>
    @elseif($count==0)
    <p class="text-danger text-center">无法读取有效数据</p>
    @else
    <p>{{date('Y-m-d H:i:s',time())}} 成功上传 <span class="badge badge-success">{{$count}}</span> 条</p>
    @endif
</div>