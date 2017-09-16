<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/5
 * Time: 11:40
 */

namespace App\Repositories\Finance\Reimburse;

use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\Expense;
use App\Models\Reimburse\Reim_department;
use App\Models\Reimburse\Reimbursement;
use App\Services\EncyptionService;
use DB;

class AuditRepository
{

    /*---------------------------------审核通过处理start--------------------------------------------------------*/
    /**
     * 处理审核通过报销单
     * @param $request
     */
    public function saveAudit($request)
    {
        DB::transaction(function () use ($request) {
            $audited_cost = $this->saveAuditExpenses($request);//审核修改明细表数据
            $this->saveReimburse($request, $audited_cost);//审核修改报销数据
        });
        return ['msg' => 'success'];
    }

    /*---------------明细处理start-----------*/
    private function saveAuditExpenses($request)
    {
        if ($request->expenses == 'all') {//审核全部明细未修改金额
            $reim_cost = $this->allExpensesSave($request);
        } else {//明细修改进行处理（选择部分明细单）
            $reim_cost = $this->updateExpensesSave($request);
        }
        return $reim_cost;//审核金额
    }

    private function allExpensesSave($request)
    {
        $expenses = Expense::where(['reim_id' => $request->reim_id, 'is_approved' => 1])->get();
        $reim_cost = 0;
        foreach ($expenses as $k => $v) {
            $reim_cost += (float)$v['send_cost'];
            $v->is_audited = 1;
            $v->audited_cost = $v['send_cost'];
            $v->save();
        }
        return $reim_cost;
    }

    private function updateExpensesSave($request)
    {
        $expense = $request->expenses;
        $reim_cost = 0;
        if (!empty($expense)) {
            foreach ($expense as $k => $v) {
                $reim_cost += (float)$v['audited_cost'];
                $data = [
                    'is_audited' => 1,
                    'audited_cost' => $v['audited_cost']
                ];
                Expense::where(['id' => $v['id'], 'reim_id' => $request->reim_id])->update($data);
            }
        }
        return $reim_cost;
    }
    /*---------------------明细处理end-------------------------*/
    /**
     * 审核报销单修改处理
     * @param $request
     * @param $audited_cost
     */
    private function saveReimburse($request, $audited_cost)
    {
        $data['status_id'] = 4;
        $data['accountant_staff_sn'] = app('CurrentUser')->staff_sn;
        $data['accountant_name'] = app('CurrentUser')->realname;
        $data['audited_cost'] = $audited_cost;
        $data['audit_time'] = date('Y-m-d H:i:s', time());
        Reimbursement::where('id', $request->reim_id)->update($data);
    }

    /*---------------------------------审核通过处理end-------------------------------------------------------*/

    /*----------------------------------驳回start-------------------------------------------*/
    /**
     * 驳回处理
     * @param $request
     */
    public function reject($request)
    {
        $id = $request->id;
        $remarks = $request->remarks;
        $data = [
            'reject_name' => app('CurrentUser')->realname,
            'reject_staff_sn' => app('CurrentUser')->staff_sn,
            'reject_time' => date('Y-m-d H:i:s', time()),
            'reject_remarks' => $remarks,
            'status_id' => -1,
        ];
        Reimbursement::where('id', $id)->update($data);
        return ['msg' => 'success'];
    }
    /*----------------------------------驳回end-------------------------------------------*/

    /**
     * 审核打印
     * @param $request
     */
    public function printReimburse($request)
    {
        $reim_department_id_array = app('AuditService')->getReimDepartmentId();
        if (!$reim_department_id_array) {
            return ['msg' => -1, 'result' => '你没有审核权限'];
        }
        $reim_id_arr = Reimbursement::whereIn('reim_department_id', $reim_department_id_array)->pluck('id')->toArray();//获取当前审核人的审核单id
        if (!in_array($request->reim_id, $reim_id_arr)) {
            return ['msg' => 0, 'result' => '当前报销单不属于你审核范围哦'];
        }
        return $this->getPrintData($request->reim_id);
    }

    private function getPrintData($reim_id)
    {
        $staff_sn = app('CurrentUser')->staff_sn;
        $reimData = Reimbursement::with('status', 'expenses.type', 'expenses.bills')->find($reim_id);
        if ($reimData->status_id == 3) {//待审核单打印
            $reimData->cost = $reimData->approved_cost ? $reimData->approved_cost : $reimData->send_cost;
        } else {//已审核单打印
            if ($staff_sn != $reimData->accountant_staff_sn) {
                return ['msg' => 2, 'result' => '当前报销单不是你审核的，亲你不能进行打印哦'];
            }
            $reimData->cost = $reimData->audited_cost;
        }
        $reimData->costCn = app('Encyption')->numberToCNY($reimData->cost);//金额转为大写
        Reimbursement::find($reim_id)->increment('print_count');//打印次数自增
        return ['msg' => 1, 'result' => $reimData];
    }



    /*--------------------------已审核报销单start----------------------------------*/


    /*--------导出start------------*/
    /**
     * 导出excel
     * @param $request
     */
    public function exportExcel($request)
    {
        $path = 'finance/reimburse/export/';
        $trans = 'fields.reimburse';
        $fileName = app('CurrentUser')->realname . '已审核单';
        if($request->type=='all'){
            $fileName =app('CurrentUser')->realname .'导出的报销单';
        }
        $data = $this->getExportData($request);
        $export = new ReimburseExport();
        $file = $export->exports($data, $path, $fileName, $trans);
        return ['state' => 1, 'file_name' => $file];
    }

    private function getExportData($request)
    {
        $reimburse = $this->getReimburse($request);
        return $this->getAllData($reimburse);
    }

    private function getReimburse($request)
    {
        $staff_sn = app('CurrentUser')->staff_sn;
        $where = ['accountant_staff_sn' => $staff_sn, 'status_id' => 4];
        if (isset($request->type) && $request->type == 'all') {
            $where = ['status_id' => 4];
        }
        $expense_where = ['expenses' => function ($query) {
            $query->where('is_audited', '=', 1);
            $query->orderBy('date', 'asc');
        }];
        $data = app('Plugin')->dataTables($request, Reimbursement::with('expenses.type')->with($expense_where)->where($where));
        return $data['data'];
    }

    private function getAllData($reimburse)
    {
        return [
            '报销单' => $this->get_reim_data($reimburse),
            '消费明细' => $this->getExpenseData($reimburse),//明细数据
            '收款人信息' => $this->get_payee_data($reimburse),
        ];
    }

    /**
     * 获取报销单导出的数据
     * @return array
     */
    private function get_reim_data($data)
    {
        $reimburse = [];
        foreach ($data as $k => $v) {
            $reim['reim_sn'] = $v['reim_sn'];
            $reim['description'] = $v['description'];
            $reim['staff_sn'] = $v['staff_sn'];
            $reim['realname'] = $v['realname'];
            $reim['department_name'] = $v['department_name'];
            $reim['reim_department.name'] = $v['reim_department']['name'];
            $reim['approver_name'] = $v['approver_name'];
            $reim['approved_cost'] = $v['approved_cost'] ? $v['approved_cost'] : $v['send_cost'];
            $reim['audited_cost'] = $v['audited_cost'];
            $reim['accountant_name'] = $v['accountant_name'];
            $reim['send_time'] = $v['send_time'];
            $reim['approve_time'] = $v['approve_time'];
            $reim['audit_time'] = $v['audit_time'];
            $reim['remark'] = $v['remark'];
            $reim['payee_name'] = $v['payee_name'];
            $reim['payee_bank_other'] = $v['payee_bank_other'];
            $reim['payee_bank_account'] = $v['payee_bank_account'];
            $reim['payee_phone'] = $v['payee_phone'];
            $reimburse[] = $reim;
        }
        return $reimburse;
    }

    /**
     * 获取明细导出数据
     * @param $data
     */
    private function getExpenseData($data)
    {
        $expenses = [];
        foreach ($data as $k => $v) {
            foreach ($v['expenses'] as $key => $val) {
                $arr['reim_sn'] = $v['reim_sn'];
                $arr['realname'] = $v['realname'];
                $arr['staff_sn'] = $v['staff_sn'];
                $arr['expenses.*.type.name'] = $val['type']['name'];
                $arr['expenses.*.date'] = $val['date'];
                $arr['expenses.*.description'] = $val['description'];
                $arr['expenses.*.send_cost'] = $val['send_cost'];
                $arr['expenses.*.audited_cost'] = $val['audited_cost'];
                $arr['department_name'] = $v['department_name'];
                $arr['reim_department.name'] = $v['reim_department']['name'];
                $arr['send_time'] = $v['send_time'];
                $arr['approve_time'] = $v['approve_time'];
                $arr['audit_time'] = $v['audit_time'];
                $arr['accountant_name'] = $v['accountant_name'];
                $expenses [] = $arr;
            }
        }
        return $expenses;
    }

    /**
     * 获取收款人导出数据
     */
    private function get_payee_data($data)
    {
        $payeeSheetData = [];
        foreach ($data as $k => $v) {
            $payeeKey = $v['payee_name'] . $v['payee_bank_other'] . $v['payee_bank_account'];
            if (array_has($payeeSheetData, $payeeKey)) {
                $payeeSheetData[$payeeKey]['金额'] += number_format($v['audited_cost'], 2, '.', '');
                $payeeSheetData[$payeeKey]['reim_sn'] .= '-' . $v['reim_sn'];
            } else {
                $payeeSheetData[$payeeKey] = [
                    'payee_name' => $v['payee_name'],
                    'payee_bank_other' => $v['payee_bank_other'],
                    'payee_bank_account' => $v['payee_bank_account'],
                    'payee_phone' => $v['payee_phone'],
                    'payee_province' => $v['payee_province'],
                    'payee_city' => $v['payee_city'],
                    'payee_bank_dot' => $v['payee_bank_dot'],
                    '金额' => number_format($v['audited_cost'], 2, '.', ''),
                    'realname' => $v['realname'],
                    'department_name' => $v['department_name'],
                    'reim_sn' => $v['reim_sn'],
                ];
            }

        }
        return $payeeSheetData;
    }
    /*--------导出end-----------*/

    /*--------------------------已审核报销单end---------------------------------*/


    /**
     * 查看报销单时打印获取数据
     * @param $reim_id
     */
    public function getCheckReimbursePrint($reim_id)
    {
        $reimData = Reimbursement::with('status', 'expenses.type', 'expenses.bills')->find($reim_id);
        $reimData->cost = $reimData->audited_cost;
        $reimData->costCn = app('Encyption')->numberToCNY($reimData->cost);//金额转为大写
        Reimbursement::find($reim_id)->increment('print_count');//打印次数自增
        return $reimData;
    }
    /**
     * 撤回 （查看报销单）
     */
    public function checkReimburseRestore($reim_id)
    {
        $data = Reimbursement::find($reim_id);
        if ($data->status_id == 4) {
            $this->restoreSave($data);
            return 'success';
        }
        return 'error';
    }

    private function restoreSave($data)
    {
        DB::transaction(function () use ($data) {
            $data->status_id = 3;
            $data->accountant_staff_sn = '';
            $data->accountant_name = '';
            $data->audited_cost = null;
            $data->audit_time = null;
            $data->save();
            foreach ($data->expenses as $v) {
                $v->is_audited = 0;
                $v->audited_cost = null;
                $v->save();
            }
        });
    }


}