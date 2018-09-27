<?php

namespace App\Http\Controllers\Api\Reimburse;

use App\Models\Reimburse\Reimbursement;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CheckAuditedController extends Controller
{
    protected $response;

    public function __construct(ResponseService $responseService)
    {
        $this->response = $responseService;
    }

    /**
     * 获取全部已审核列表数据
     * @param Request $request
     */
    public function index()
    {
        $with = [
            'expenses' => function ($query) {
                $query->where('is_audited', '=', 1);
                $query->orderBy('date', 'asc');
            },
            'expenses.bills'
        ];
        $data = Reimbursement::with($with)
            ->where('status_id', '>=', 4)
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
        return $this->response->get($data);
    }

    /**
     * 全部已审核详情
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        //模型条件（查询审批通过的单 is_approved =1的）
        $expensesWhere = [
            'expenses' => function ($query) {
                $query->where('is_audited', '=', 1);
                $query->orderBy('date', 'asc');
            }
        ];
        $data = Reimbursement::with('expenses.type', 'expenses.bills')->with($expensesWhere)->find($id);
        return $this->response->get($data);
    }

    /**
     * 撤回已审核单
     * @param Request $request
     */
    public function withdraw(Reimbursement $reimbursement)
    {
        if ($reimbursement->status_id !== 4) {
            abort(400, '该报销单无法撤回');
        }
        DB::connection('reimburse_mysql')->transaction(function () use (&$reimbursement) {
            $reimbursement->status_id = 3;
            $reimbursement->accountant_staff_sn = '';
            $reimbursement->accountant_name = '';
            $reimbursement->audited_cost = null;
            $reimbursement->audit_time = null;
            $reimbursement->process_instance_id = '';
            $reimbursement->manager_sn = '';
            $reimbursement->manager_name = '';
            $reimbursement->save();
            $reimbursement->expenses->each(function ($expense) {
                $expense->is_audited = 0;
                $expense->audited_cost = null;
                $expense->save();
            });
        });
        $reimbursement->load('expenses.bills');
        return $this->response->patch($reimbursement);
    }
}
