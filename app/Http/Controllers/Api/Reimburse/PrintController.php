<?php

/**
 * 打印类
 */
namespace App\Http\Controllers\Api\Reimburse;

use App\Repositories\Reimburse\AuditRepository;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrintController extends Controller
{
    protected $response;
    protected $auditRepository;

    public function __construct(ResponseService $responseService, AuditRepository $auditRepository)
    {
        $this->response = $responseService;
        $this->auditRepository = $auditRepository;
    }

    /**
     * 获取打印数据
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request){
        $data = $this->auditRepository->getPrintData($request);
        return $this->response->get($data);
    }
}
