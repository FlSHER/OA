<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20/020
 * Time: 9:23
 */

namespace App\Repositories\Reimburse;


use App\Contracts\ExcelExport;
use App\Models\Reimburse\Reimbursement;
use Illuminate\Support\Facades\Auth;

class ExportRepository extends ExcelExport
{
    public function exportReimburse($request)
    {
        $type = $request->query('type');
        switch ($type) {
            case 'audited':
                $fileName = Auth::user()->realname . '已审核单';
                break;
            case 'check':
                $fileName = Auth::user()->realname . '导出的已审核的报销单';
                break;
            case 'pay':
                $fileName = Auth::user()->realname . '导出未转账的报销单';
                break;
            default:
                $fileName = '报销单';
        }
        $reimburse = $this->getExportData($request);//获取导出的数据
        $data = $this->getExportDetailData($reimburse);//获取导出的详细数据
        $this->fileName = $fileName;
        $this->filePath = 'finance/reimburse/export/';
        $this->trans('fields.reimburse');
        return $this->export($data);
    }

    /**
     * 获取报销导出数据
     * @param $request
     * @return mixed
     */
    protected function getExportData($request)
    {
        $type = $request->query('type');
        $auditRepository = new AuditRepository();
        $reimDepartmentIds = $auditRepository->getReimDepartmentId();//资金归属ID
        switch ($type) {
            case 'audited'://已审核的
                $where = [
                    ['status_id', '>=', 4]
                ];
                break;
            case 'check'://查看已审核的
                $where = [
                    ['status_id', '>=', 4]
                ];
                break;
            case 'pay'://未转账的
                $where = [
                    ['status_id', '=', 6]
                ];
                break;
            default:
                $where = false;
        }
        $expense_where = ['expenses' => function ($query) {
            $query->where('is_audited', 1)
                ->orderBy('date', 'asc');
        }];
        $reimburse = Reimbursement::with('expenses.type')
            ->with($expense_where)
            ->where($where)
            ->when($type == 'audited', function ($query) use ($reimDepartmentIds) {
                return $query->whereIn('reim_department_id', $reimDepartmentIds);
            })
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();
        return $reimburse;
    }

    /**
     * 获取导出的详细数据
     * @param $reimburse
     */
    protected function getExportDetailData($reimburse)
    {
        return [
            '报销单' => $this->getReimbursementData($reimburse),
            '消费明细' => $this->getExpensesData($reimburse),
            '收款人信息' => $this->getPayeeData($reimburse),
        ];
    }

    /**
     * 报销数据
     * @param $reimburse
     */
    protected function getReimbursementData($reimburse)
    {
        $data = [];
        $reimburse->each(function ($item) use (&$data) {
            $reim['reim_sn'] = $item->reim_sn;
            $reim['description'] = $item->description;
            $reim['staff_sn'] = $item->staff_sn;
            $reim['realname'] = $item->realname;
            $reim['department_name'] = $item->department_name;
            $reim['reim_department.name'] = $item->reim_department->name;
            $reim['approver_name'] = $item->approver_name;
            $reim['approved_cost'] = $item->approved_cost ? $item->approved_cost : $item->send_cost;
            $reim['audited_cost'] = $item->audited_cost;
            $reim['accountant_name'] = $item->accountant_name;
            $reim['send_time'] = $item->send_time;
            $reim['approve_time'] = $item->approve_time;
            $reim['audit_time'] = $item->audit_time;
            $reim['remark'] = $item->remark;
            $reim['payee_name'] = $item->payee_name;
            $reim['payee_bank_other'] = $item->payee_bank_other;
            $reim['payee_bank_account'] = $item->payee_bank_account;
            $reim['payee_phone'] = $item->payee_phone;
            $data[] = $reim;
        });
        return $data;
    }

    /**
     * 明细数据
     * @param $reimburse
     * @return array
     */
    protected function getExpensesData($reimburse)
    {
        $data = [];
        $reimburse->each(function ($reimburse) use (&$data) {
            $reimburse->expenses->each(function ($expense) use (&$data, $reimburse) {
                $arr['reim_sn'] = $reimburse->reim_sn;
                $arr['realname'] = $reimburse->realname;
                $arr['staff_sn'] = $reimburse->staff_sn;
                $arr['expenses.*.type.name'] = $expense->type->name;
                $arr['expenses.*.date'] = $expense->date;
                $arr['expenses.*.description'] = $expense->description;
                $arr['expenses.*.send_cost'] = $expense->send_cost;
                $arr['expenses.*.audited_cost'] = $expense->audited_cost;
                $arr['department_name'] = $reimburse->department_name;
                $arr['reim_department.name'] = $reimburse->reim_department->name;
                $arr['send_time'] = $reimburse->send_time;
                $arr['approve_time'] = $reimburse->approve_time;
                $arr['audit_time'] = $reimburse->audit_time;
                $arr['accountant_name'] = $reimburse->accountant_name;
                $data [] = $arr;
            });
        });
        return $data;
    }

    /**
     * 收款人数据
     * @param $reimburse
     * @return array
     */
    protected function getPayeeData($reimburse)
    {
        $data = [];
        $reimburse->each(function($item)use(&$data){
            $key = $item->payee_name . $item->payee_bank_other . $item->payee_bank_account .'-'.$item->reim_department_id;
            if(array_has($data,$key)){
                $data[$key]['金额'] = number_format(($data[$key]['金额'] + $item->audited_cost),'2','.','');
                $data[$key]['reim_sn'] .= '-'. $item->reim_sn;
            }else{
                $data[$key] = [
                    'payee_bank_other' => $item->payee_bank_other,
                    'payee_bank_account' => $item->payee_bank_account,
                    'payee_name' => $item->payee_name,
                    'reim_department.name' => $item->reim_department->name,
                    'payee_phone' => $item->payee_phone,
                    'payee_province' => $item->payee_province,
                    'payee_city' => $item->payee_city,
                    'payee_bank_dot' => $item->payee_bank_dot,
                    '金额' => number_format($item->audited_cost, 2, '.', ''),
                    'realname' => $item->realname,
                    'department_name' => $item->department_name,
                    'reim_sn' => $item->reim_sn,
                ];
            }
        });
        return $data;
    }
}