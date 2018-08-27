<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/26/026
 * Time: 13:59
 */

namespace App\Services\Reimburse;


use App\Models\Reimburse\Reimbursement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayService
{
    /**
     * 驳回
     * @param $request
     * @return Reimbursement|Reimbursement[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function reject($request)
    {
        $reimburse = Reimbursement::with('expenses.bills')->find($request->input('id'));
        $reimburse->second_rejecter_name = Auth::user()->realname;
        $reimburse->second_rejecter_staff_sn = Auth::id();
        $reimburse->second_rejected_at = date('Y-m-d H:i:s');
        $reimburse->second_reject_remarks = $request->input('remarks');
        $reimburse->audit_time = null;
        $reimburse->status_id = 3;
        $reimburse->accountant_staff_sn = '';
        $reimburse->accountant_name = '';
        $reimburse->manager_sn = '';
        $reimburse->manager_name = '';
        $reimburse->manager_approved_at = null;
        $reimburse->process_instance_id = '';
        $reimburse->save();
        return $reimburse;
    }

    /**
     * 转账处理
     * @param $request
     * @return Reimbursement|Reimbursement[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function pay($request)
    {
        $ids = is_array($request->input('id')) ? $request->input('id') : [$request->input('id')];
        $column = [
            'status_id' => 7,
            'payer_sn' => Auth::id(),
            'payer_name' => Auth::user()->realname,
            'paid_at' => date('Y-m-d')
        ];

        DB::connection('reimburse_mysql')->beginTransaction();
        try {
            Reimbursement::whereIn('id', $ids)->update($column);
            $data = Reimbursement::with(['expenses.bills'])->find($ids);
            DB::connection('reimburse_mysql')->commit();
        } catch (\Exception $e) {
            DB::connection('reimburse_mysql')->rollBack();
            abort(400, '转账通过失败');
        }
        return $data;
    }
}