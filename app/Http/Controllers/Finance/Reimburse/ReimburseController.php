<?php

namespace App\Http\Controllers\Finance\Reimburse;

use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\Reimbursement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EncyptionService;
use App\Services\Finance\Reimburse\AuditAuthority;

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
        return app('AuditService')->getAuditListData($request);
    }

    /**
     * 获取审核消费详情数据
     * @param Request $request
     * @return json
     */
    public function getExpensesByReimId(Request $request)
    {
        //模型条件（查询审批通过的单 is_approved =1的）
        $expensesWHere = [
            'expenses' => function ($query) {
                $query->where('is_approved', '=', 1);
            }
        ];
        return app('AuditService')->getReimIdExpenses($request->reim_id, $expensesWHere);

    }


    /**
     * 审核通过处理
     * @param Request $request
     * @return string
     */
    public function agree(Request $request, AuditAuthority $auth)
    {
        $auditAuth = $auth->getAuditAuthority($request->reim_id);
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
        return app('AuditService')->saveAudit($request);
    }

    /**
     * 驳回（审核）
     * @param Request $request
     * @return string
     */
    public function reject(Request $request, AuditAuthority $auth)
    {
        $auditAuth = $auth->getAuditAuthority($request->id);
        if ($auditAuth['msg'] != 'success') {
            return $auditAuth;
        }

        $this->validate($request, [
            'id' => 'required',
            'remarks' => 'required|string',
        ], [], trans('fields.reimburse.reject'));

        return app('AuditService')->reject($request);
    }


    /**
     * 打印
     * @param Request $request
     * @param EncyptionService $encypt
     * @return type
     */
    public function printReimbursement(Request $request)
    {
        $data = app('AuditService')->printReimburse($request);
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
        $staff_sn = session()->get('admin')['staff_sn'];
        $where = 'accountant_staff_sn = ' . $staff_sn . ' and ';
        return app('AuditService')->getAuditedReimburseList($request, $where);
    }


    /**
     * 已驳回列表数据
     * @param Request $request
     * @return mixed
     */
    public function getRejectedList(Request $request)
    {
        return app('AuditService')->getAuditRejectList($request);
    }


    /**
     * 删除驳回后的报销单
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        return app('AuditService')->deleteRejectAudit($request);

    }

    /**
     * 导出excel
     * @param Request $request
     */
    public function exportAsExcel(Request $request)
    {
        return $data = app('AuditService')->exportExcel($request);
    }


}
