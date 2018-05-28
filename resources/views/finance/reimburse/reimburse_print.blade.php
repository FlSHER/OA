<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
<style>
    tr:before, tr:after {
        display: none !important;
    }

    tr {
        line-height: 30px;
    }

    td {
        padding: 5px;
    }
</style>
<table style="width:900px;">
    <tr class="row">
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
        <td style="width:75px;"></td>
    </tr>
    <tr class="row" style="border-bottom:2px solid #000;font-weight:700;font-family:Microsoft YaHei">
        <td colspan="5">成都阿喜杰尼威尼服饰有限公司</td>
        <td colspan="7" class="text-right">{{$data->reim_sn}} @if($data->status_id<4)<span
                    style="color:red; font-size:60%; font-weight:normal;">({{$data->status->name}})</span>@endif</td>
    </tr>
    <tr class="row" style="border-bottom:1px solid #000;line-height:40px;">
        <td colspan="2">提交人：</td>
        <td colspan="2">{{$data->realname}}</td>
        <td colspan="2" style="border-left:1px solid #000;">报销部门：</td>
        <td colspan="3">{{$data->department_name}} — {{$data->reim_department->name}}</td>
        <td colspan="3" style="border-left:1px solid #000;">报销日期：{{substr($data->send_time,0,10)}}</td>
    </tr>
    <tr class="row" style="font-weight:700;">
        <td colspan="2">类型</td>
        <td colspan="7" style="border-left:1px dotted #666;">消费明细</td>
        <td colspan="1" style="border-left:1px dotted #666;" class="text-right">发票</td>
        <td colspan="2" style="border-left:1px dotted #666;" class="text-right">汇总金额</td>
    </tr>
    <?php $bills_sum = 0;?>
    @foreach($data->expenses as $v)
        @if($data->status_id == 3)
            <tr class="row" style="border-top:1px dotted #666;">
                <td colspan="2">{{$v->type->name}}</td>
                <td colspan="2" style="border-left:1px dotted #666;">{{$v->date}}</td>
                <td colspan="5" style="border-left:1px dotted #666;">{{$v->description}}</td>
                <td colspan="1" style="border-left:1px dotted #666;" class="text-right">{{count($v->bills)}}</td>
                <td colspan="2" style="border-left:1px dotted #666;" class="text-right">￥{{$v->send_cost}}</td>
            </tr>
            <?php $bills_sum += count($v->bills);?>
        @elseif($data->status_id == 4)
            @if($v->is_audited == 1)
                <tr class="row" style="border-top:1px dotted #666;">
                    <td colspan="2">{{$v->type->name}}</td>
                    <td colspan="2" style="border-left:1px dotted #666;">{{$v->date}}</td>
                    <td colspan="5" style="border-left:1px dotted #666;">{{$v->description}}</td>
                    <td colspan="1" style="border-left:1px dotted #666;" class="text-right">{{count($v->bills)}}</td>
                    <td colspan="2" style="border-left:1px dotted #666;" class="text-right">
                        ￥{{$v->audited_cost or 0}}</td>
                </tr>
            <?php $bills_sum += count($v->bills);?>
        @endif
    @endif
@endforeach
<!-- 总计 -->
    <tr class="row" style="border-top:2px solid #000;line-height:40px;">
        <td colspan="3">合计人民币（大写）：</td>
        <td colspan="6">{{$data->costCn}} 整</td>
        <td colspan="1" style="border-left:1px dotted #666;" class="text-right">{{$bills_sum}}张</td>
        <td colspan="2" style="border-left:1px dotted #666;" class="text-right">￥{{$data->cost}}</td>
    </tr>
    <!-- 收款人信息 -->
    <tr class="row" style="border-top:2px solid #000;line-height:36px;">
        <td colspan="9">收款人：{{$data->payee_name}} {{$data->payee_bank_other}} {{$data->payee_bank_account}}</td>
        <td colspan="3">付款日期：</td>
    </tr>
    <!-- 人员 -->
    <tr class="row" style="border-top:2px solid #000;font-weight:700;line-height:50px;">
        <td colspan="4">部门负责人
            ：@if(!empty($data->approver_staff_sn)){{$data->approver_name}}@else{{$data->realname}}@endif</td>
        <td colspan="4">会 计 ：</td>
        <td colspan="4">审核人 ：</td>

    </tr>
    <tr class="row" style="font-weight:700;line-height:50px;">
        <td colspan="4">审 计 ：</td>
        <td colspan="4">审批人 ：</td>
        <td colspan="4" class="text-right" style="color:#999;font-size:12px;font-weight:400;">
            打印时间：{{date('Y-m-d H:i:s',time())}} @if($data->print_count > 0)({{$data->print_count}})@endif</td>
    </tr>

</table>

<script>
    window.print();

    setTimeout(function () {
        var seconds = 3;
        windowCloseCountDown();
        setInterval(windowCloseCountDown, 1000);

        function windowCloseCountDown() {
            if (seconds == 0) {
                window.top.close();
            }
            var countDownDom = document.getElementById('count_down');
            if (countDownDom == undefined) {
                document.getElementsByTagName('body')[0].innerHTML = '<h1 style="text-align:center;margin-top:200px;color:#666;">页面将在 <span id="count_down" style="color:#333;">' + seconds + '</span> 秒后关闭<h1>';
            } else {
                countDownDom.innerHTML = seconds;
            }
            seconds--;
        }
    }, 400);
</script>