<div class="row">
    @if(empty($clocks))
        <div class="col-sm-12 col-md-12">
            <h4 class="text-danger text-center">无打卡记录</h4>
        </div>
    @else
        @foreach($clocks as $clock)
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
            '21': '调动到达',
            '22': '调动出发',
            '31': '请假返回',
            '32': '请假离开',
        };
        var title = titleGroup[clock.attendance_type + '' + clock.type];
        $('#viewMore h4').html(title);
        $('#viewMore p').html('店铺代码：' + clock.shop_sn + '<br>');
        $('#viewMore p').append('打卡时间： <span style="font-weight:400;font-size:14px;">' + clock.clock_at + '</span><br>');
        $('#viewMore p').append('标准时间： <span style="font-weight:400;font-size:14px;">' + clock.punctual_time + '</span><br>');
        $('#viewMore p').append('<i class="fa fa-map-marker fa-fw"></i> ' + clock.address);
        $('#viewMore').modal('show');
    }
</script>
