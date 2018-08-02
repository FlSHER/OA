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
        try {
            DB::connection('reimburse_mysql')->beginTransaction();
            if ((int)$approverSn != (int)$managerSn) {
                $formData = $this->makeFormData($reimbursement);
                $initiatorSn = $reimbursement->staff_sn;//报销单创建人编号
                $processInstanceId = app('Dingtalk')->startApprovalAndRecord($this->appId, $this->processCode, $managerSn, $formData, $callback, $initiatorSn);
                $reimbursement->process_instance_id = $processInstanceId;
            } else {
                $reimbursement->process_instance_id = 'skip_' . $reimbursement->reim_sn;
            }

            $reimbursement->manager_sn = $managerSn;
            $reimbursement->manager_name = $managerName;
            $reimbursement->save();
            DB::connection('reimburse_mysql')->commit();
        } catch (\Exception $e) {
            DB::connection('reimburse_mysql')->rollBack();
            $this->reimbursementRollback($reimbursement);//审核失败数据回滚到待审状态
            abort(400, $e->getMessage());
        }
        if ((int)$approverSn == (int)$managerSn) {
            //审批人等于资金归属管理人
            $message = [
                'processInstanceId' => $reimbursement->process_instance_id,
                'EventType' => 'bpms_instance_change',
                'type' => 'finish',
                'result' => 'agree',
            ];
            $data = app('Curl')->setUrl($callback)->sendMessageByPost($message);
            if ($data == 0) {
                $this->reimbursementRollback($reimbursement);//审核失败数据回滚到待审状态
                abort(400, '审核后转交管理人审批失败，可能是报销服务器无响应');
            }
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
            $formData = ['报销清单' => array_merge($value['data'])];
            $formData['备注'] = Auth::user()->realname . '提交已审核的报销单';
            try {
                $processInstanceId = app('Dingtalk')->startApprovalAndRecord($this->appId, $this->processCode, $managerSn, $formData, $callback);
                DB::connection('reimburse_mysql')->transaction(function () use ($value, $processInstanceId) {
                    $ids = array_keys($value['data']);//审核要审批的ID
                    $saveData = [
                        'process_instance_id' => $processInstanceId,
                        'manager_sn'=>$value['manager']['manager_sn'],
                        'manager_name'=>$value['manager']['manager_name']
                    ];
                    Reimbursement::whereIn('id', $ids)->update($saveData);
                });
            } catch (\Exception $e) {
                abort(400, $e->getMessage());
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
            $period = $this->computeExpensesMaxAndMinDate($reim->expenses);//报销期间
            $item = [];
            $item['姓名'] = $reim->realname;
            $item['描述'] = $reim->description;
            $item['金额'] = $reim->audited_cost;
            $item['报销期间'] = implode(' 至 ', $period);
            $item['备注'] = $reim->remark;
            $data[$managerSn]['data'][$reim->id] = $item;
            $manager = [];
            $manager['manager_sn'] = $managerSn;
            $manager['manager_name'] = $managerName;
            $data[$managerSn]['manager'] = $manager;
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