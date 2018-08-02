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
    public function index(Request $request){
        $with = [
            'expenses' => function ($query) {
                $query->where('is_audited', '=', 1);
                $query->orderBy('date', 'asc');
            },
            'expenses.type',
            'expenses.bills'
        ];
        $data = Reimbursement::with('reim_department')
            ->with($with)
            ->where('status_id', '>=', 4)
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
        $this->response->get($data);
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
    public function withdraw(Request $request)
    {
        $reimburse = Reimbursement::with(['expenses'=>function($query){
            return $query->where('is_audited',1);
        }])
            ->where('status_id',4)
            ->find($request->id);
        if(!$reimburse)
            abort(404,'该报销单不存在');
        DB::transaction(function()use(&$reimburse){
            $reimburse->status_id = 3;
            $reimburse->accountant_staff_sn = '';
            $reimburse->accountant_name = '';
            $reimburse->audited_cost = null;
            $reimburse->audit_time = null;
            $reimburse->process_instance_id = '';
            $reimburse->manager_sn = '';
            $reimburse->manager_name = '';
            $reimburse->save();
            $reimburse->expenses->each(function($expense){
                $expense->is_audited = 0;
                $expense->audited_cost = null;
                $expense->save();
            });
        });
        return $this->response->get($reimburse);
    }
}
