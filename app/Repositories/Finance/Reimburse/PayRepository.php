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
use App\Models\Reimburse\ReimDepartment;
use App\Models\Reimburse\Reimbursement;
use App\Services\EncyptionService;
use DB;

class PayRepository
{

    /*--------导出start------------*/
    /**
     * 导出excel
     * @param $request
     */
    public function exportExcel($request)
    {
        $path = 'finance/reimburse/export/';
        $trans = 'fields.reimburse';
        $fileName = app('CurrentUser')->realname . '导出的报销转账单';
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
        $where = ['status_id' => 6];
        $reim_deparment_arr = app('AuditService')->getReimDepartmentId();//当前的资金归属id（array）
        $expense_where = ['expenses' => function ($query) {
            $query->where('is_audited', '=', 1);
            $query->orderBy('date', 'asc');
        }];

        $data = app('Plugin')->dataTables(
            $request,
            Reimbursement::with('expenses.type')
                ->with($expense_where)
                ->where($where)
                ->when(
                    !$request->type,
                    function ($query) use ($reim_deparment_arr) {
                        return $query->whereIn('reim_department_id', $reim_deparment_arr);
                    })
        );
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
            $payeeKey = $v['payee_name'] . $v['payee_bank_other'] . $v['payee_bank_account'] . $v['reim_department_id'];
            if (array_has($payeeSheetData, $payeeKey)) {
                $payeeSheetData[$payeeKey]['金额'] = number_format(($payeeSheetData[$payeeKey]['金额'] + $v['audited_cost']), 2, '.', '');
                $payeeSheetData[$payeeKey]['reim_sn'] .= '-' . $v['reim_sn'];
            } else {
                $payeeSheetData[$payeeKey] = [
                    'payee_bank_other' => $v['payee_bank_other'],
                    'payee_bank_account' => $v['payee_bank_account'],
                    'payee_name' => $v['payee_name'],
                    'reim_department.name' => $v['reim_department']['name'],
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
            $data->process_instance_id = '';
            $data->manager_sn = '';
            $data->manager_name = '';
            $data->save();
            foreach ($data->expenses as $v) {
                $v->is_audited = 0;
                $v->audited_cost = null;
                $v->save();
            }
        });
    }


}