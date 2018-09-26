<?php

namespace App\Http\Controllers\Api\Reimburse;

use App\Models\Reimburse\Reimbursement;
use App\Repositories\Reimburse\PublicRepository;
use App\Services\Reimburse\PayService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicController extends Controller
{
    protected $response;
    protected $publicRepository;
    protected $payService;

    public function __construct(ResponseService $responseService,
                                PublicRepository $publicRepository,
                                PayService $payService)
    {
        $this->response = $responseService;
        $this->publicRepository = $publicRepository;
        $this->payService = $payService;
    }

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $data = $this->publicRepository->getPayList($request);
        return $this->response->get($data);
    }

    public function toPrivate(Reimbursement $reimbursement)
    {
        if ($reimbursement->payee_is_public == 0) {
            abort(404, '未找到可转换的报销单');
        }
        $reimbursement->payee_is_public = 0;
        $reimbursement->save();
        return $this->response->patch($reimbursement);
    }

}
