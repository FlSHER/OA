<?php

namespace App\Http\Controllers\Finance\Reimburse;

use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\Reimbursement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EncyptionService;

class ReimburseController extends Controller
{

    /**
     * 进入报销审核界面
     * @return view
     */
    public function showReimbursePage()
    {
        return view('finance.reimburse.reimburse');
    }

    /**
     * 获取待审核报销单列表数据
     * @return json
     */
    public function getHandleList(Request $request)
    {
        return $this->getHandleData($request);
    }

    private function getHandleData($request)
    {
        $reim_deparment_arr = app('AuditService')->getReimDepartmentId();
        if (!$reim_deparment_arr) {
            $result['data'] = [];
//            $result['draw'] = 2;
            $result['recordsFiltered'] = 0;
            $result['recordsTotal'] = 0;
        } else {
            $result = app('Plugin')->dataTables($request, Reimbursement::where('status_id', 3)->whereIn('reim_department_id', $reim_deparment_arr));
        }
        return $result;
    }

    /**
     * 获取审核消费详情数据
     * @param Request $request
     * @return json
     */
    public function getReimburseExpenses(Request $request)
    {
        //模型条件（查询审批通过的单 is_approved =1的）
        $expensesWHere = [
            'expenses' => function ($query) {
                $query->where('is_approved', '=', 1);
            }
        ];
        $data = Reimbursement::with('expenses.type', 'expenses.bills')->with($expensesWHere)->find($request->reim_id);
        return $data;

    }


    /**
     * 审核通过处理
     * @param Request $request
     * @return string
     */
    public function agree(Request $request)
    {
        $auditAuth = app('AuditService')->getAuditAuthority($request->reim_id);
        if ($auditAuth['msg'] != 'success') {
            return $auditAuth;
        }
        $this->validate($request, [
            'reim_id' => 'required',
            'expenses' => 'required',
            'expenses.*.id' => 'exists:reimburse_mysql.expenses,id,reim_id,' . $request->reim_id,
            'expenses.*.audited_cost' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        ], [], trans('fields.reimburse.audit')
        );
        return app('AuditRepository')->saveAudit($request);
    }

    /**
     * 驳回（审核）
     * @param Request $request
     * @return string
     */
    public function reject(Request $request)
    {
        $auditAuth = app('AuditService')->getAuditAuthority($request->id);
        if ($auditAuth['msg'] != 'success') {
            return $auditAuth;
        }

        $this->validate($request, [
            'id' => 'required',
            'remarks' => 'required|string',
        ], [], trans('fields.reimburse.reject'));

        return app('AuditRepository')->reject($request);
    }


    /**
     * 打印
     * @param Request $request
     * @param EncyptionService $encypt
     * @return type
     */
    public function printReimbursement(Request $request)
    {
        $data = app('AuditRepository')->printReimburse($request);
        if ($data['msg'] != 1) {
            return $data['result'];
        }
        $reim = $data['result'];
        return view('finance.reimburse.reimburse_print', ['data' => $reim]);
    }


    /**
     * 已审核列表数据
     * @param Request $request
     * @return mixed
     */
    public function getAuditedList(Request $request)
    {
        $reim_deparment_arr = app('AuditService')->getReimDepartmentId();
        if (!$reim_deparment_arr) {
            $result['data'] = [];
//            $result['draw'] = 2;
            $result['recordsFiltered'] = 0;
            $result['recordsTotal'] = 0;
        } else {
            $where = ['status_id' => 4];
            $result = app('Plugin')->dataTables($request, Reimbursement::where($where)->whereIn('reim_department_id', $reim_deparment_arr));
        }

        return $result;
    }


    /**
     * 已驳回列表数据
     * @param Request $request
     * @return mixed
     */
    public function getRejectedList(Request $request)
    {
        $staff_sn = app('CurrentUser')->staff_sn;
        $reim_deparment_arr = app('AuditService')->getReimDepartmentId();
        if (!$reim_deparment_arr) {
            $result['data'] = [];
//            $result['draw'] = 2;
            $result['recordsFiltered'] = 0;
            $result['recordsTotal'] = 0;
        } else {
            $where = [
                ['accountant_delete', '=', 0],
                ['approve_time', '<>', ''],
                ['reject_staff_sn', '<>', '']
            ];
            $result = app('Plugin')->dataTables($request, Reimbursement::where($where)->whereIn('reim_department_id', $reim_deparment_arr));
        }

        return $result;
    }


    /**
     * 删除驳回后的报销单
     * @param Request $request
     * @return mixed
     */
    public function deleteReject(Request $request)
    {
        $staff_sn = app('CurrentUser')->staff_sn;
        $reimburse = Reimbursement::where('reject_staff_sn', $staff_sn)->where('approve_time', '!=', null)->find($request->id);
        if (count($reimburse) < 1) {
            return ['msg' => 'error', 'result' => '你不能进行删除！该报销单不是你审核的'];
        }
        $reimburse->accountant_delete = 1;
        $reimburse->save();
        return ['msg' => 'success'];

    }

    /**
     * 导出excel
     * @param Request $request
     */
    public function exportAsExcel(Request $request)
    {
        return $data = app('AuditRepository')->exportExcel($request);
    }


}
