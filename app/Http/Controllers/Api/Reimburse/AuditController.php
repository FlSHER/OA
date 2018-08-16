<?php

namespace App\Http\Controllers\Api\Reimburse;

use App\Http\Requests\Reimburse\AgreeRequest;
use App\Http\Requests\Reimburse\RejectRequest;
use App\Models\Reimburse\ReimbursementStatus;
use App\Models\Reimburse\ReimDepartment;
use App\Repositories\Reimburse\AuditRepository;
use App\Services\Reimburse\AuditService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuditController extends Controller
{
    protected $auditRepository;//审核仓库
    protected $auditService;//审核服务
    protected $response;//返回

    public function __construct(AuditRepository $auditRepository, AuditService $auditService, ResponseService $responseService)
    {
        $this->auditRepository = $auditRepository;//审核仓库
        $this->auditService = $auditService;
        $this->response = $responseService;
    }

    /**
     * 审核列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $data = $this->auditRepository->getListData($request);
        return $this->response->get($data);
    }

    /**
     * 审核详情
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $data = $this->auditRepository->detail($id);
        return $this->response->get($data);
    }

    /**
     * 通过处理
     * @param AgreeRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function agree(AgreeRequest $request)
    {
        $data = $this->auditService->agree($request);//通过处理
        return $this->response->patch($data);
    }

    /**
     * 驳回
     * @param RejectRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function reject(RejectRequest $request)
    {
        $data = $this->auditService->reject($request);
        return $this->response->patch($data);
    }

    /**
     * 删除驳回的单
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request)
    {
        $this->auditService->destroy($request);
        return $this->response->delete();
    }

    /**
     * 获取资金归属数据
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getReimDepartment()
    {
        $data = ReimDepartment::get();
        return $this->response->get($data);
    }

    /**
     * 获取报销单状态
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getStatus()
    {
        return $this->response->get(ReimbursementStatus::get());
    }
}
