<?php

namespace App\Http\Controllers\Api\Reimburse;

use App\Http\Requests\Reimburse\PayRequest;
use App\Models\Reimburse\Reimbursement;
use App\Repositories\Reimburse\PayRepository;
use App\Services\Reimburse\AuditService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PayController extends Controller
{
    protected $response;
    protected $payRepository;
    protected $auditService;

    public function __construct(ResponseService $responseService,
                                PayRepository $payRepository,
                                AuditService $auditService)
    {
        $this->response = $responseService;
        $this->payRepository = $payRepository;
        $this->auditService = $auditService;
    }

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $data = $this->payRepository->getPayList($request);
        return $this->response->get($data);
    }

    /**
     * 驳回
     * @param Request $request
     */
    public function reject(Request $request)
    {
        $messages = [
            'id' => '报销单ID',
            'remarks' => '驳回备注',
        ];
        $this->validate($request, [
            'id' => [
                'required',
                Rule::exists('reimburse_mysql.reimbursements')
                    ->where('status_id', 6)
                    ->whereNull('deleted_at')
            ],
            'remarks' => [
                'required',
                'string'
            ]
        ], [], $messages);
        $data = $this->auditService->reject($request);
        return $this->response->patch($data);
    }

    /**
     * 转账
     * @param Request $request
     */
    public function pay(PayRequest $request)
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
            $data = Reimbursement::find($ids);
            DB::connection('reimburse_mysql')->commit();
            return $this->response->patch($data);
        } catch (\Exception $e) {
            DB::connection('reimburse_mysql')->rollBack();
            abort(400,'转账通过失败');
        }

    }
}
