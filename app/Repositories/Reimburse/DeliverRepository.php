<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23/023
 * Time: 10:35
 */

namespace App\Repositories\Reimburse;


use App\Models\Reimburse\Reimbursement;

class DeliverRepository
{
    protected $auditRepository;

    public function __construct(AuditRepository $auditRepository)
    {
        $this->auditRepository = $auditRepository;
    }

    /**
     * 获取转交已审核列表
     * @param $request
     */
    public function getDeliverList()
    {
        $data = Reimbursement::where('status_id', 4)
            ->where('process_instance_id','')
            ->whereIn('reim_department_id', $this->auditRepository->getReimDepartmentId())
            ->orderBy('audit_time', 'desc')
            ->get();
        return $data;
    }

}