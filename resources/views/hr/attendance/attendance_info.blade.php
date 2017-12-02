@inject('authority','Authority')
@inject('currentUser','CurrentUser')
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
                <p>
                    提交时间：{{$submitted_at}}
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
                            @if($detail['is_assistor']==1)
                                <span class="label label-info">协助</span>
                            @endif
                            @if($detail['is_shift']==1)
                                <span class="label label-warning">倒班</span>
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
                            @if($detail['is_leaving'] == 1)
                                <span class="label label-warning">请假</span>
                            @endif
                            @if($detail['is_transferring'] == 1)
                                <span class="label label-info">调动</span>
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
                                        @if(!empty($detail['clock_log'][0]) && $detail['clock_log'][0]['start'] > $detail['working_start_at'])
                                            <div class="progress-bar"
                                                 style="width:{{
                                        (strtotime($detail['clock_log'][0]['start'])-strtotime($detail['working_start_at']))*100/$workingTime
                                        }}%;background-color:transparent;padding-right:0;"></div>
                                        @endif
                                        @foreach($detail['clock_log'] as $clockLog)
                                            <div class="progress-bar
@if($clockLog['type'] == 'w')
                                                    progress-bar-success
@elseif($clockLog['type'] == 't')
                                                    progress-bar-info
@elseif($clockLog['type'] == 'l')
                                                    progress-bar-warning
@endif
                                                    "
                                                 style="width:{{$clockLog['duration']*100/$workingTime}}%;padding-right:0;">

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
                            @include('hr/attendance/clock_records',['clocks'=>$detail['clocks']])
                        </div>
                    </li>
                @endforeach
            </ul>
            @if($authority->checkAuthority(122))
                <div style="padding:0 10%;" class="row">
                    @if($status != 2 || $auditor_sn == $currentUser->staff_sn)
                        <div class="col-sm-4">
                            <button class="btn btn-default"
                                    onClick="refresh({{$id}})">
                                刷新 <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                    @endif
                    @if($status == 1)
                        <div class="col-sm-4">
                            <button class="btn btn-danger" onClick="reject({{$id}})">
                                驳回 <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-success pull-right" onClick="pass({{$id}})">
                                通过 <i class="fa fa-check"></i>
                            </button>
                        </div>
                    @elseif($status == 2 && $auditor_sn == $currentUser->staff_sn)
                        <div class="col-sm-4">
                            <button class="btn btn-warning" onClick="revert({{$id}})">
                                撤回 <i class="fa fa-rotate-left"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

