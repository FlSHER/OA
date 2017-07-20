<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
<style>
    tr:before,tr:after{display:none !important;}
    tr{line-height:30px;}
    td{padding:5px;}
</style>
<table style="width:900px;">
    <tr class="row">
        <td style="width:75px;"></td><td style="width:75px;"></td><td style="width:75px;"></td><td style="width:75px;"></td>
        <td style="width:75px;"></td><td style="width:75px;"></td><td style="width:75px;"></td><td style="width:75px;"></td>
        <td style="width:75px;"></td><td style="width:75px;"></td><td style="width:75px;"></td><td style="width:75px;"></td>
    </tr>
    <tr class="row" style="border-bottom:2px solid #000;font-weight:700;font-family:Microsoft YaHei">
        <td colspan="5">成都阿喜杰尼威尼服饰有限公司</td>
        <td colspan="7" class="text-right">{{$reimbursements['reim_sn']}}</td>
    </tr>
    <tr class="row" style="border-bottom:1px solid #000;line-height:40px;">
        <td colspan="2">提交人：</td>
        <td colspan="2">{{$reimbursements['user_name']}}</td>
        <td colspan="2" style="border-left:1px solid #000;">报销部门：</td>
        <td colspan="3">{{$reimbursements['custom_department']['name']}} - {{$reimbursements['reim_department']['name']}}</td>
        <td colspan="3" style="border-left:1px solid #000;">报销日期：{{substr($reimbursements['send_time'],0,10)}}</td>
    </tr>
    <tr class="row" style="font-weight:700;">
        <td colspan="2">类型</td>
        <td colspan="7" style="border-left:1px dotted #666;">消费明细</td>
        <td colspan="1" style="border-left:1px dotted #666;" class="text-right">发票</td>
        <td colspan="2" style="border-left:1px dotted #666;" class="text-right">汇总金额</td>
    </tr>
    @foreach($reimbursements['expenses'] as $expense)
    <tr class="row" style="border-top:1px dotted #666;">
        <td colspan="2">{{$expense['type']['name']}}</td>
        <td colspan="2" style="border-left:1px dotted #666;">{{$expense['date']}}</td>
        <td colspan="5" style="border-left:1px dotted #666;">{{$expense['description']}}</td>
        <td colspan="1" style="border-left:1px dotted #666;" class="text-right">{{count($expense['bills']['pic_path'])}}</td>
        <td colspan="2" style="border-left:1px dotted #666;" class="text-right">￥@if($reimbursements['status_id']>4){{$expense['audited_cost']}}@else{{$expense['approved_cost']}}@endif</td>
    </tr>
    @endforeach
    <!-- 总计 -->
    <tr class="row" style="border-top:2px solid #000;line-height:40px;">
        <td colspan="3">合计人民币（大写）：</td>
        <td colspan="6">{{$reimbursements['costCn']}} 整</td>
        <td colspan="1" style="border-left:1px dotted #666;" class="text-right">{{count(array_flatten(array_pluck($reimbursements['expenses'],'bills.pic_path')))}}张</td>
        <td colspan="2" style="border-left:1px dotted #666;" class="text-right">￥{{$reimbursements['cost']}}</td>
    </tr>
    <!-- 收款人信息 -->
    <tr class="row" style="border-top:2px solid #000;line-height:36px;">
        <td colspan="9">收款人：{{$reimbursements['payee_name']}} {{$reimbursements['payee_bank_other']}} {{$reimbursements['payee_bank_account']}}</td>
        <td colspan="3">付款日期：</td>
    </tr>
    <!-- 人员 -->   
    <tr class="row" style="border-top:2px solid #000;font-weight:700;line-height:50px;">
        <td colspan="4">部门负责人 ：@if($reimbursements['userid'] != $reimbursements['approverid'] && $reimbursements['approverid'] != 'is_null'){{$reimbursements['approver_name']}}@else{{$reimbursements['user_name']}}@endif</td>
        <td colspan="4">会 计 ：</td>
        <td colspan="4">审核人 ：</td>
        
    </tr>
    <tr class="row" style="font-weight:700;line-height:50px;">
        <td colspan="4">审 计 ：</td>
        <td colspan="4">审批人 ：</td>
        <td colspan="4" class="text-right" style="color:#999;font-size:12px;font-weight:400;">打印时间：{{date('Y-m-d H:i:s',time())}} @if($reimbursements['print_count'] > 1)({{$reimbursements['print_count']}})@endif</td>
    </tr>

</table>

<script>
    window.print();
    setTimeout("window.close();", 500);
</script>