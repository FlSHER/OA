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
        return app('AuditService')->getAuditedReimburseList($request);
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
                $query->orderBy('date','asc');
            }
        ];
        return app('AuditService')->getReimIdExpenses($request->reim_id, $expensesWHere);
    }

    /**
     * 打印 (查看报销单)
     * @param Request $request
     */
    public function checkReimbursePrint(Request $request){
        $reim = app('AuditService')->getCheckReimbursePrint($request->reim_id);
        return view('finance.reimburse.reimburse_print', ['data' => $reim]);
    }

    /**
     * 撤回
     * @param Request $request
     */
    public function reply(Request $request){
        $reim_id = $request->reim_id;
        return app('AuditService')->checkReimburseReply($reim_id);
    }
}
