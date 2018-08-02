<?php

namespace App\Http\Controllers\Api\Reimburse;

use App\Repositories\Reimburse\ExportRepository;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{
    protected $response;
    protected $exportRepository;

    public function __construct(ResponseService $responseService,ExportRepository $exportRepository)
    {
        $this->response = $responseService;
        $this->exportRepository = $exportRepository;
    }

    /**
     * 导出
     * @param Request $request
     */
    public function export(Request $request)
    {
        $filePath = $this->exportRepository->exportReimburse($request);
        return $this->response->get($filePath);
    }
}
