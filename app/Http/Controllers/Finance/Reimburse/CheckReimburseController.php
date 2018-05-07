<?php

namespace App\Http\Controllers\Finance\Reimburse;

use App\Models\Reimburse\Reimbursement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckReimburseController extends Controller
{
    /**
     * 列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkAllAuditedList()
    {
        return view('finance.reimburse.check_reimburse');
    }

    /**
     * 获取全部已审核列表数据
     * @param Request $request
     */
    public function getAllAuditedList(Request $request)
    {
        $result = app('Plugin')->dataTables($request, Reimbursement::whereIn('status_id', [4, 5]));
        return $result;
    }

    /**
     * 获取查看报销单的消费明细
     * @param Request $request
     */
    public function getCheckReimburseExpenses(Request $request)
    {
        //模型条件（查询审批通过的单 is_approved =1的）
        $expensesWHere = [
            'expenses' => function ($query) {
                $query->where('is_audited', '=', 1);
                $query->orderBy('date', 'asc');
            }
        ];
        $data = Reimbursement::with('expenses.type', 'expenses.bills')->with($expensesWHere)->find($request->reim_id);
        return $data;
    }

    /**
     * 打印 (查看报销单)
     * @param Request $request
     */
    public function checkReimbursePrint(Request $request)
    {
        $reim = app('AuditRepository')->getCheckReimbursePrint($request->reim_id);
        return view('finance.reimburse.reimburse_print', ['data' => $reim]);
    }

    /**
     * 撤回
     * @param Request $request
     */
    public function restore(Request $request)
    {
        $reim_id = $request->reim_id;
        return app('AuditRepository')->checkReimburseRestore($reim_id);
    }

    public function pay(Request $request)
    {
        $reim_id = $request->reim_id;
        $reimbursement = Reimbursement::find($reim_id);
        if ($reimbursement && $reimbursement->status_id == 4) {
            $reimbursement->status_id = 5;
            $reimbursement->payer_sn = app('CurrentUser')->staff_sn;
            $reimbursement->payer_name = app('CurrentUser')->realname;
            $reimbursement->paid_at = date('Y-m-d');
            $reimbursement->save();
            return 'success';
        } else {
            return 'error';
        }
    }
}
