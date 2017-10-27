<style>
    .label {
        margin-left: 4px;
        padding-top: 0.3em;
        top: -1px;
        position: relative;
    }
</style>
<div class="col-lg-4">
    <div class="panel">
        <header class="panel-heading">
            {{$shop_sn}} {{$shop_name}}
            @if($status == 2)
                <span class="label label-success">已审核</span>
            @elseif($status == -1)
                <span class="label label-danger">已驳回</span>
            @endif
            <span style="float:right;">{{$attendance_date}}</span>
        </header>
        <div class="panel-body">
            <b>
                <p>
                    上班时间：{{$shop['clock_in']}}
                </p>
                <p>
                    下班时间：{{$shop['clock_out']}}
                </p>
                <p>
                    店铺业绩：￥
                    {{sprintf('%.2f',$sales_performance_lisha+$sales_performance_go+$sales_performance_group+$sales_performance_partner)}}
                </p>
            </b>
            <ul class="list-group">
                @foreach($details as $detail)
                    <li class="list-group-item">
                        <div href="#collapse_{{$detail['staff_sn']}}" data-toggle="collapse">
                            <b>{{$detail['staff_name']}}</b> <span style="color:#999">{{$detail['staff_sn']}}</span>
                            @if($detail['shop_duty_id']==1)
                                <span class="label label-success">{{$detail['shop_duty']['name']}}</span>
                            @else
                                <span class="label label-default">{{$detail['shop_duty']['name']}}</span>
                            @endif
                            @if($detail['is_missing']==1)
                                <span class="label label-danger">漏签</span>
                            @endif
                            @if($detail['late_time']>0)
                                <span class="label label-danger">迟到
                                    {{max(sprintf('%.1f',round($detail['late_time'],1)),0.1)}}
                                    小时</span>
                            @endif
                            @if($detail['early_out_time']>0)
                                <span class="label label-danger">早退
                                    {{max(sprintf('%.1f',round($detail['early_out_time'],1)),0.1)}}
                                    小时</span>
                            @endif
                            <span style="float:right;font-weight:700;">
                                ￥ {{sprintf('%.2f',$detail['sales_performance_lisha']+
                                $detail['sales_performance_go']+
                                $detail['sales_performance_group']+
                                $detail['sales_performance_partner'])}}
                            </span>
                            <p></p>
                            <div class="row">
                                <div class="col-sm-2" style="text-align:center;">
                                    {{substr($detail['working_start_at'],0,5)}}
                                </div>
                                <div class="col-sm-8" style="padding:3px 0 0;">
                                    <div class="progress">
                                        <?php $workingTime = strtotime($detail['working_end_at']) - strtotime($detail['working_start_at']); ?>
                                        {{--@if(!empty($detail['clock_log'][0]) && $detail['clock_log'][0]['start'] > $detail['working_start_at'])--}}
                                            {{--<div class="progress-bar"--}}
                                                 {{--style="width:{{--}}
                                                 {{--(strtotime($detail['clock_log'][0]['start'])-strtotime($detail['working_start_at']))*100/$workingTime--}}
                                                 {{--}}%"></div>--}}
                                        {{--@endif--}}
                                        @foreach($detail['clock_log'] as $clockLog)
                                            <div class="progress-bar
@if($clockLog['type'] == 'w')
                                                    progress-bar-success
@elseif($clockLog['type'] == 't')
                                                    progress-bar-info
@elseif($clockLog['type'] == 'l')
                                                    progress-bar-warning
@endif
                                                    " style="width:{{$clockLog['duration']*100/$workingTime}}%;padding-right:0;">

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-sm-2" style="text-align:center;">
                                    {{substr($detail['working_end_at'],0,5)}}
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="collapse_{{$detail['staff_sn']}}">
                            <div class="row">
                                @if(empty($detail['clocks']))
                                    <div class="col-sm-12 col-md-12">
                                        <h4 class="text-danger text-center">无打卡记录</h4>
                                    </div>
                                @else
                                    @foreach($detail['clocks'] as $clock)
                                        <div class="col-sm-4 col-md-3">
                                            <div class="thumbnail" onClick="viewMore({{json_encode($clock)}})">
                                                <img src="{{$clock['thumb']}}">
                                                <h4 style="text-align:center;color:
                                                @if($clock['attendance_type'] == 1)
                                                        #5cb85c
                                                @elseif($clock['attendance_type'] == 2)
                                                        #5bc0de
                                                @elseif($clock['attendance_type'] == 3)
                                                        #f0ad4e
                                                @endif">
                                                    <i class="fa fa-caret-square-o-{{$clock['type'] == 1?'up':'down'}}"
                                                    ></i>
                                                    {{substr($clock['clock_at'],11,5)}}
                                                </h4>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            @if($status == 1)
                <div style="padding:0 10%;">
                    <button class="btn btn-lg btn-danger" onClick="reject({{$id}})">驳回 <i class="fa fa-times"></i>
                    </button>
                    <button class="btn btn-lg btn-success pull-right" onClick="pass({{$id}})">通过 <i
                                class="fa fa-check"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="viewMore">
    <div class="modal-dialog modal-sm">
        <div class="thumbnail">
            <img src="" width="100%">
            <h4 style="font-weight:700;"></h4>
            <p></p>
        </div>
    </div>
</div>

<script>
    function viewMore(clock) {
        $('#viewMore img').attr('src', clock.photo);
        var titleGroup = {
            '11': '上班打卡',
            '12': '下班打卡',
            '21': '调动出发',
            '22': '调动到达',
            '31': '请假离开',
            '32': '请假返回',
        };
        var title = titleGroup[clock.attendance_type + '' + clock.type];
        $('#viewMore h4').html(title);
        $('#viewMore p').html('<i class="fa fa-clock-o fa-fw"></i> <span style="font-weight:400;font-size:14px;">' + clock.clock_at + '</span><br>');
        $('#viewMore p').append('<i class="fa fa-map-marker fa-fw"></i> ' + clock.address);
        $('#viewMore').modal('show');
    }
</script>
