<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23/023
 * Time: 11:15
 * 转交审批
 */

namespace App\Services\Reimburse;


use App\Models\Reimburse\Reimbursement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliverService
{

    protected $appId;
    protected $processCode;

    protected $financeOfficerSn = 110085;
    protected $financeOfficerName = '郭娟';
//    protected $financeOfficerSn = 110103;
//    protected $financeOfficerName = '刘勇01';

    public function __construct()
    {
        $this->appId = config('reimburse.app_id');
        $this->processCode = config('reimburse.process_code');
    }

    /**
     * 单条数据
     * 审核通过的进行发起钉钉审批
     * @param $reimbursement
     */
    public function afterApprove($reimbursement)
    {
        $approverSn = empty($reimbursement->approver_staff_sn) ? $reimbursement->staff_sn : $reimbursement->approver_staff_sn;//审批人员工编号
        $managerSn = $reimbursement->reim_department->manager_sn;//资金归属管理人员工编号
        $managerName = $reimbursement->reim_department->manager_name;//资金归属管理人员工名字
        $callback = config("reimburse.manager_callback");
        DB::connection('reimburse_mysql')->beginTransaction();
        try {
            $dingApproveSn = '';
            if($reimbursement->audited_cost > 5000){
                if((int)$approverSn == (int)$managerSn){
                    $dingApproveSn = $this->financeOfficerSn;
                }else{
                    $dingApproveSn =[$managerSn,$this->financeOfficerSn];
                }
            }else{
                if((int)$approverSn != (int)$managerSn){
                    $dingApproveSn = $managerSn;
                }
            }
            if(!empty($dingApproveSn)){
                $formData = $this->makeFormData($reimbursement);
                $initiatorSn = $reimbursement->staff_sn;//报销单创建人编号
                $code = 'PROC-GLYJ5N2V-E11VUX0YRK67A1WOOODU2-G8JBUYGJ-1';
                $processInstanceId = app('Dingtalk')->startApprovalAndRecord($this->appId, $code, $dingApproveSn, $formData, $callback, $initiatorSn);
                $reimbursement->process_instance_id  = $processInstanceId;
            }else{
                $reimbursement->process_instance_id = 'skip_' . $reimbursement->reim_sn;
            }

            $reimbursement->second_rejecter_staff_sn = '';
            $reimbursement->second_rejecter_name = '';
            $reimbursement->second_rejected_at = null;
            $reimbursement->second_reject_remarks = null;
            $reimbursement->save();
            DB::connection('reimburse_mysql')->commit();
        } catch (\Exception $e) {
            DB::connection('reimburse_mysql')->rollBack();
            $this->reimbursementRollback($reimbursement);//审核失败数据回滚到待审状态
            abort(400, $e->getMessage());
        }
    }

    /**
     * 审核失败回滚到待审核状态
     * @param $reimbursement
     */
    protected function reimbursementRollback($reimbursement)
    {
        DB::connection('reimburse_mysql')->transaction(function () use ($reimbursement) {
            $reimbursement->expenses->where('is_audited', 1)
                ->each(function ($expense) {
                    $expense->is_audited = 0;
                    $expense->audited_cost = null;
                    $expense->save();
                });
            $reimbursement->status_id = 3;
            $reimbursement->accountant_staff_sn = '';
            $reimbursement->accountant_name = '';
            $reimbursement->audited_cost = null;
            $reimbursement->audit_time = null;
            $reimbursement->manager_sn = '';
            $reimbursement->manager_name = '';
            $reimbursement->process_instance_id = '';
            $reimbursement->save();
        });
    }

    protected function makeFormData($reimbursement)
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
                '发票' => $expense->bills->pluck('pic_path')->all(),
            ];
        })->all();
        return $formData;
    }

    /**
     * 批量转交审批
     * @param $request
     */
    public function managerApprove($request)
    {
        $callback = config('reimburse.manager_batch_approve_callback');
        $ids = (array)$request->input('id');
        $reimbursement = Reimbursement::orderBy('audit_time', 'desc')->find($ids);
        $data = $this->makeManagerFormData($reimbursement);//表单数据
        foreach ($data as $managerSn => $value) {
            foreach ($value['data'] as $reimDepartmentName => $reim) {
                $formData['资金归属'] = $reimDepartmentName;
                $formData['报销单'] = count(array_merge($reim));
                $formData['总金额'] = sprintf('%.2f', array_sum(array_pluck($reim, '金额')));
                $formData['备注'] = $request->input('remark') ?: '无';
                $formData ['报销清单'] = array_merge($reim);
                try {
                    if($managerSn != '110085'){
                        //品牌副总不是郭娟 添加郭娟审批
                        $approveSn = [$managerSn,110085];
                    }
                    $processInstanceId = app('Dingtalk')->startApprovalAndRecord($this->appId, $this->processCode, $approveSn, $formData, $callback);
                    DB::connection('reimburse_mysql')->transaction(function () use ($value, $reim, $processInstanceId) {
                        $ids = array_keys($reim);//审核要审批的ID
                        $saveData = [
                            'process_instance_id' => $processInstanceId,
                            'manager_sn' => $value['manager']['manager_sn'],
                            'manager_name' => $value['manager']['manager_name'],
                            'second_rejecter_staff_sn' => '',
                            'second_rejecter_name' => '',
                            'second_rejected_at' => null,
                            'second_reject_remarks' => null,
                        ];
                        Reimbursement::whereIn('id', $ids)->update($saveData);
                    });
                } catch (\Exception $e) {
                    abort(400, $e->getMessage());
                }
            }
        }
        return 1;
    }

    /**
     * 获取审核表单数据
     * @param $reimbursement
     * @return array
     */
    protected function makeManagerFormData($reimbursement)
    {
        $data = [];
        $reimbursement->map(function ($reim) use (&$data) {
            $managerSn = $reim->reim_department->manager_sn;
            $managerName = $reim->reim_department->manager_name;
            $reimDepartmentName = $reim->reim_department->name;//资金归属名
            $period = $this->computeExpensesMaxAndMinDate($reim->expenses);//报销期间
            $item = [];
            $item['姓名'] = $reim->realname;
            $item['描述'] = $reim->description;
            $item['金额'] = $reim->audited_cost;
            $item['报销期间'] = implode(' 至 ', $period);
            $item['备注'] = $reim->remark;
//            $data[$managerSn]['data'][$reim->id] = $item;
            $data[$managerSn]['data'][$reimDepartmentName][$reim->id] = $item;
            $manager = [];
            $manager['manager_sn'] = $managerSn;
            $manager['manager_name'] = $managerName;
            $data[$managerSn]['manager'] = $manager;
            //资金归属
//            $data[$managerSn]['reim_department_name'] = $reim->reim_department->name;
        });
        return $data;
    }

    /**
     * 计算消费明细最大与最小日期
     * @param $expenses
     */
    protected function computeExpensesMaxAndMinDate($expenses)
    {
        $expensesDate = $expenses->pluck('date')->all();
        $maxDate = max($expensesDate);
        $minDate = min($expensesDate);
        return [$minDate, $maxDate];
    }
}