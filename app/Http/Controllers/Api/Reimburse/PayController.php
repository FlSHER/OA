<?php

namespace App\Http\Controllers\Api\Reimburse;

use App\Http\Requests\Reimburse\PayRequest;
use App\Models\Reimburse\Reimbursement;
use App\Repositories\Reimburse\PayRepository;
use App\Services\Reimburse\PayService;
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
    protected $payService;

    public function __construct(ResponseService $responseService,
                                PayRepository $payRepository,
                                PayService $payService)
    {
        $this->response = $responseService;
        $this->payRepository = $payRepository;
        $this->payService = $payService;
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
     * 获取导出列表
     * @param Request $request
     * @return mixed
     */
    public function exportIndex(Request $request)
    {
        $data = $this->payRepository->getExportList($request);
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
                    ->whereIn('reim_department_id', $this->payRepository->getCashierReimDepartmentAuthority())
                    ->whereNull('deleted_at')
            ],
            'remarks' => [
                'required',
                'string'
            ]
        ], [], $messages);
        $data = $this->payService->reject($request);
        return $this->response->patch($data);
    }

    /**
     * 转账
     * @param Request $request
     */
    public function pay(PayRequest $request)
    {
        $data = $this->payService->pay($request);
        return $this->response->patch($data);
    }
}
