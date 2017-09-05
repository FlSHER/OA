<?php

namespace App\Services\Finance\Reimburse;

/**
 * 审核类
 * Description of AuditService
 *
 * @author admin
 */
use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\Expense;
use App\Models\Reimburse\Reim_department;
use App\Models\Reimburse\Reimbursement;
use App\Services\EncyptionService;
use DB;
use Excel;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * 获取待审核列表数据
     * @param type $request
     */
    public function getAuditListData($request)
    {
        $staff_sn = session()->get('admin')['staff_sn'];
        $result['data'] = [];
        $result['draw'] = 2;
        $result['recordsFiltered'] = 0;
        $result['recordsTotal'] = 0;
        $reim_department_id_array = Auditor::where('auditor_staff_sn', $staff_sn)->pluck('reim_department_id');//当前审核人的审核权限资金归属id
        if (count($reim_department_id_array) > 0) {//当前用户在审核人中 
            $reim_department_id_str = implode(',', $reim_department_id_array->toArray());
            $where = 'status_id = 3 and reim_department_id in (' . $reim_department_id_str . ')';
            $result = app('Plugin')->dataTables($request, Reimbursement::class, $where);
        }
        return $result;
    }

    /**
     * 获取当前报销消费明细数据（详情）
     * @param $reim_id
     */
    public function getReimIdExpenses($reim_id, $expensesWHere)
    {

        $data = Reimbursement::with('expenses.type', 'expenses.bills')->with($expensesWHere)->find($reim_id);
        return $data;
    }

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
        $data['accountant_staff_sn'] = session()->get('admin')['staff_sn'];
        $data['accountant_name'] = session()->get('admin')['realname'];
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
            'reject_name' => session()->get('admin')['realname'],
            'reject_staff_sn' => session()->get('admin')['staff_sn'],
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
        $auth = new AuditAuthority();
        $reim_department_id_array = $auth->getReimDepartmentId();

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
        $staff_sn = session()->get('admin')['staff_sn'];
        $reimData = Reimbursement::with('status', 'expenses.type', 'expenses.bills')->find($reim_id);
        if ($reimData->status_id == 3) {//待审核单打印 
            $reimData->cost = $reimData->approved_cost ? $reimData->approved_cost : $reimData->send_cost;
        } else {//已审核单打印 
            if ($staff_sn != $reimData->accountant_staff_sn) {
                return ['msg' => 2, 'result' => '当前报销单不是你审核的，亲你不能进行打印哦'];
            }
            $reimData->cost = $reimData->audited_cost;
        }
        $encypt = new EncyptionService;
        $reimData->costCn = $encypt->numberToCNY($reimData->cost);//金额转为大写 
        Reimbursement::find($reim_id)->increment('print_count');//打印次数自增 
        return ['msg' => 1, 'result' => $reimData];
    }


    /**
     * 查看报销单时打印获取数据
     * @param $reim_id
     */
    public function getCheckReimbursePrint($reim_id)
    {
        $reimData = Reimbursement::with('status', 'expenses.type', 'expenses.bills')->find($reim_id);
        $reimData->cost = $reimData->audited_cost;
        $encypt = new EncyptionService;
        $reimData->costCn = $encypt->numberToCNY($reimData->cost);//金额转为大写
        Reimbursement::find($reim_id)->increment('print_count');//打印次数自增
        return $reimData;
    }

    /*--------------------------已审核报销单start----------------------------------*/

    /**
     * （列表）获取已审核报销单列表数据
     * @param $request
     */
    public function getAuditedReimburseList($request, $where = '')
    {
        $where_str = $where . 'status_id = 4';
        $result = app('Plugin')->dataTables($request, Reimbursement::class, $where_str);
        return $result;
    }


    /*--------导出start------------*/
    /**
     * 导出excel
     * @param $request
     */
    public function exportExcel($request)
    {
        $file_name = session()->get('admin')['realname'] . '已审核单';
        if($request->type=='all'){
            $file_name ='报销单';
        }
        $data = $this->getReimburseData($request);//获取报销数据
        $sheetData = $this->get_reimburse_expense_payee_data($data);//获取报销单数据、明细数据、收款数据

        $file = app('App\Contracts\ExcelExport')->setPath('finance/reimburse/export/')->setBaseName($file_name)->trans('fields.reimburse')->export($sheetData);

        return ['state' => 1, 'file_name' => $file];
    }

    /**
     * 导出获取报销数据
     * @param $request
     */
    private function getReimburseData($request)
    {
        $staff_sn = session()->get('admin')['staff_sn'];
        $where = 'accountant_staff_sn = ' . $staff_sn . ' and status_id = 4 ';
        if (isset($request->type) && $request->type == 'all') {//查看报销单导出数据获取
            $where = 'status_id = 4';
        }
        $expense_where = ['expenses' => function ($query) {
            $query->where('is_audited', '=', 1);
            $query->orderBy('date','asc');
        }];
        $result = app('Plugin')->dataTables($request, Reimbursement::with('expenses.type')->with($expense_where), $where);
        return $result['data'];
    }

    /**
     * 获取报销单、明细、收款人数据
     * @param $data
     */
    private function get_reimburse_expense_payee_data($data)
    {
        return [
            '报销单' => $this->get_reim_data($data),
            '消费明细' => $this->getExpenseData($data),//明细数据
            '收款人信息' => $this->get_payee_data($data),
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

    /*------------------------------已驳回报销单start-----------------*/
    /**
     * 获取审核时的驳回单列表数据
     * @param $request
     */
    public function getAuditRejectList($request)
    {
        $staff_sn = session()->get('admin')['staff_sn'];
        $where = 'reject_staff_sn = ' . $staff_sn . ' and accountant_delete =0 and approve_time is not null';
        $result = app('Plugin')->dataTables($request, Reimbursement::class, $where);
        return $result;
    }

    /**
     * 删除审核时已驳回的单
     * @param $request
     */
    public function deleteRejectAudit($request)
    {
        $staff_sn = session()->get('admin')['staff_sn'];
        $reimburse = Reimbursement::where('reject_staff_sn', $staff_sn)->where('approve_time', '!=', null)->find($request->id);
        if (count($reimburse) < 1) {
            return ['msg' => 'error', 'result' => '你不能进行删除！该报销单不是你审核的'];
        }
        $reimburse->accountant_delete = 1;
        $reimburse->save();
        return ['msg' => 'success'];
    }

    /*------------------------------已驳回报销单end-----------------*/


    /**
     * 筛选（获取已审核单的资金归属列表）
     */
    public function getReimDepartmentName()
    {
        return Reim_department::withTrashed()->get();
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
