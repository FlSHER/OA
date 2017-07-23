<div class="col-sm-4">
    <div class="panel">
        <div class="panel-body">
            <label class="control-label col-sm-3"><strong>今日合照：</strong></label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    @if (empty($attachment))
                    无照片
                    @else
                    <img height="200" src="{{config('api.url.attendance.public').$attachment}}" />
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-4">
    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>员工姓名</th>
                        <th>上班时间</th>
                        <th>下班时间</th>
                        <th>员工业绩</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                    <tr>
                        <th>{{$detail['staff_name']}}</th>
                        <th>{{array_get($detail,'sign_time')}}</th>
                        <th>{{array_get($detail,'down_time')}}</th>
                        <th>{{$detail['achievement'] or 0}}</th>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>