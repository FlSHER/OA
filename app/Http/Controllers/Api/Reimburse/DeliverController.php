<?php

namespace App\Http\Controllers\Api\Reimburse;

use App\Http\Requests\Reimburse\DeliverRequest;
use App\Repositories\Reimburse\DeliverRepository;
use App\Services\Reimburse\DeliverService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliverController extends Controller
{
    protected $response;
    protected $deliverRepository;
    protected $deliverService;


    public function __construct(ResponseService $responseService, DeliverRepository $deliverRepository, DeliverService $deliverService)
    {
        $this->response = $responseService;
        $this->deliverRepository = $deliverRepository;
        $this->deliverService = $deliverService;
    }

    /**
     * 已审核待转交列表
     */
    public function index(){
        $data = $this->deliverRepository->getDeliverList();
        return $this->response->get($data);
    }

    /**
     * 转交到钉钉审批
     * @param Request $request
     */
    public function store(DeliverRequest $request)
    {
        $data = $this->deliverService->managerApprove($request);
        return response($data,201);
    }
}
