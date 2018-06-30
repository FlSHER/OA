<?php

namespace App\Services\Finance\Reimburse;

/**
 * 审核类
 * Description of AuditService
 *
 * @author admin
 */
use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\ReimDepartment;
use App\Models\Reimburse\Reimbursement;
use DB;
use Illuminate\Http\Request;

class AuditService
{

    /**
     * 获取当前报销单的权限
     * @param $id
     */
    public function getAuditAuthority($id)
    {
        $reim_department_id_array = $this->getReimDepartmentId();
        if (!$reim_department_id_array) {
            return array('msg' => 'warning', 'result' => '当前用户不是审核人!');
        }
        $reim = Reimbursement::where('status_id', 3)->whereIn('reim_department_id', $reim_department_id_array)->find($id);//获取当前审核人的审核单数据
        if (empty($reim)) {
            return array('msg' => 'error', 'result' => '当前报销单不存在！或已被其他人处理了。请刷新页面再试！');
        }
        return array('msg' => 'success');
    }

    /**
     * 获取审核的权限id
     */
    public function getReimDepartmentId()
    {
        $staffSn = app('CurrentUser')->staff_sn;
        if ($staffSn == '999999') {
            $reimDepartmentIds = Auditor::pluck('reim_department_id');
        } else {
            $reimDepartmentIds = Auditor::where('auditor_staff_sn', $staffSn)->pluck('reim_department_id');
        }
        if (count($reimDepartmentIds) > 0) {
            return $reimDepartmentIds;
        }
        return false;
    }

    /**
     * 筛选（获取已审核单的资金归属列表）
     */
    public function getReimDepartmentName()
    {
        return ReimDepartment::withTrashed()->get();
    }

    /*---------- 审核之后流程 Start -----------*/

    public function afterApprove($reimbursement)
    {
        $appId = 1;
        $processCode = 'PROC-GLYJ5N2V-E11VUX0YRK67A1WOOODU2-G8JBUYGJ-1';
        $managerSn = $reimbursement->reim_department->manager_sn;
        $managerName = $reimbursement->reim_department->manager_name;
        $formData = $this->makeFormData($reimbursement);
        $initiatorSn = $reimbursement->staff_sn;
        $callback = config("api.url.reimburse.base") . 'api/callback/manager';
        $processInstanceId = app('Dingtalk')->startApprovalAndRecord($appId, $processCode, $managerSn, $formData, $callback, $initiatorSn);
        $reimbursement->process_instance_id = $processInstanceId;
        $reimbursement->manager_sn = $managerSn;
        $reimbursement->manager_name = $managerName;
        $reimbursement->save();
    }

    public function makeFormData($reimbursement)
    {
        $formData = [
            '报销单编号' => $reimbursement->reim_sn,
            '内容' => $reimbursement->description,
            '申请人' => $reimbursement->realname,
            '总金额' => sprintf('%.2f', $reimbursement->audited_cost),
            '直属领导' => $reimbursement->approver_name,
            '财务审核' => $reimbursement->accountant_name,
            '资金归属' => $reimbursement->reim_department->name,
        ];
        $formData['消费明细'] = $reimbursement->expenses->where('is_audited', 1)->map(function ($expense) {
            return [
                '金额' => $expense->audited_cost,
                '日期' => $expense->date,
                '描述' => $expense->description,
                '发票' => $expense->bills->pluck('pic_path')->map(function ($picPath) {
                    return config("api.url.reimburse.base") . $picPath;
                })->all(),
            ];
        })->all();
        return $formData;
    }

    /*---------- 审核之后流程 End -----------*/

}
