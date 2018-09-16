<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/18/018
 * Time: 15:03
 */

namespace App\Services\Reimburse;


use App\Models\Reimburse\Reimbursement;
use App\Models\Reimburse\VicePresident;
use App\Repositories\Reimburse\AuditRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditService
{
    /**
     * 通过
     * @param $request
     * @return array
     */
    public function agree($request)
    {
        $reimburse = [];
        DB::connection('reimburse_mysql')->transaction(function () use ($request, &$reimburse) {
            $reimburse = Reimbursement::find($request->id);
            $auditedCost = $this->saveAuditExpenses($request, $reimburse);//明细通过处理
            $this->saveReimburse($reimburse, $auditedCost);//报销单通过处理
        });
        $deliver = new DeliverService();
        $vicePresidentSn = VicePresident::pluck('staff_sn')->all();
        //发起人是副总 提交喜哥审批
        if (in_array($reimburse->staff_sn, $vicePresidentSn)) {
            $deliver->bossApprove($reimburse);
        }else{
            //资金归属 （7 成都分公司，8 电商版块，10女装，11 濮院总公司）
            if (in_array($reimburse->reim_department_id, [7, 8, 10, 11])) {
                //转交到钉钉审批
                $deliver->afterApprove($reimburse);//转交到钉钉审批
            }
        }
        $reimburse->load('expenses.bills');
        return $reimburse;
    }

    /**
     * 明细修改
     * @param $request
     * @param $reimburse
     */
    protected function saveAuditExpenses($request, $reimburse)
    {
        $auditedExpenses = array_pluck($request->expenses, [], 'id');
        $reimCost = 0;
        $reimburse->expenses
            ->where('is_approved', 1)
            ->whereIn('id', array_pluck($auditedExpenses, 'id'))
            ->each(function ($expense) use ($auditedExpenses, &$reimCost) {
                $auditedCost = $auditedExpenses[$expense->id]['audited_cost'];
                $reimCost += (float)$auditedCost;
                $expense->is_audited = 1;
                $expense->audited_cost = $auditedCost;
                $expense->save();
            });
        return $reimCost;
    }

    /**
     * 报销单修改为通过
     * @param $reimburse
     * @param $auditedCost
     */
    protected function saveReimburse($reimburse, $auditedCost)
    {
        $reimburse->status_id = 4;
        $reimburse->accountant_staff_sn = Auth::id();
        $reimburse->accountant_name = Auth::user()->realname;
        $reimburse->audited_cost = $auditedCost;
        $reimburse->audit_time = date('Y-m-d H:i:s');
        $reimburse->second_rejecter_staff_sn = '';
        $reimburse->second_rejecter_name = '';
        $reimburse->second_rejected_at = null;
        $reimburse->second_reject_remarks = null;
        $reimburse->save();
    }

    /**
     * 驳回
     * @param $request
     */
    public function reject($request)
    {
        $reimburse = Reimbursement::find($request->input('id'));
        $reimburse->reject_name = Auth::user()->realname;
        $reimburse->reject_staff_sn = Auth::id();
        $reimburse->reject_time = date('Y-m-d H:i:s');
        $reimburse->reject_remarks = $request->input('remarks');
        $reimburse->status_id = -1;
        $reimburse->save();
        $reimburse->load('expenses.bills');
        return $reimburse;
    }

    /**
     * 删除驳回的单
     * @param $request
     */
    public function destroy($request)
    {
        $auditRepository = new AuditRepository();
        $reimDepartmentIds = $auditRepository->getReimDepartmentId();//资金归属ID
        $reimburse = Reimbursement::where('reject_staff_sn', Auth::id())
            ->where('status_id', -1)
            ->whereIn('reim_department_id', $reimDepartmentIds)
            ->where('approve_time', '!=', null)
            ->find($request->id);
        if (!$reimburse) {
            abort(400, '你不能进行删除！该报销单不是你审核的');
        }
        $reimburse->accountant_delete = 1;
        $reimburse->save();
    }
}