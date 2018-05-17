<?php

namespace App\Http\Controllers\Finance\Reimburse;

use App\Models\Reimburse\Reimbursement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PayReimburseController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showView()
    {
        return view('finance.reimburse.pay_reimburse');
    }

    /**
     * ajax获取所有未付款报销单
     * @param Request $request
     */
    public function getNotPaidList(Request $request)
    {
        $result = app('Plugin')->dataTables($request, Reimbursement::where('status_id', 6));
        return $result;
    }

    /**
     * ajax获取所有已付款报销单
     * @param Request $request
     */
    public function getPaidList(Request $request)
    {
        $result = app('Plugin')->dataTables($request, Reimbursement::where('status_id', 7));
        return $result;
    }

    public function pay(Request $request)
    {
        $reimIds = is_array($request->reim_id) ? $request->reim_id : [$request->reim_id];
        DB::beginTransaction();
        foreach ($reimIds as $reimId) {
            $reimbursement = Reimbursement::find($reimId);
            if ($reimbursement && $reimbursement->status_id == 6) {
                $reimbursement->status_id = 7;
                $reimbursement->payer_sn = app('CurrentUser')->staff_sn;
                $reimbursement->payer_name = app('CurrentUser')->realname;
                $reimbursement->paid_at = date('Y-m-d');
                $reimbursement->save();
            } else {
                DB::rollBack();
                return 'error';
            }
        }
        DB::commit();
        return 'success';
    }
}
