<?php

namespace App\Services\Finance\Reimburse;

/**
 * 审核类
 * Description of AuditService
 *
 * @author admin
 */
use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\Reim_department;
use App\Models\Reimburse\Reimbursement;
use DB;
use Illuminate\Http\Request;

class AuditService
{

    /**
     * 获取当前报销单的权限
     * @param $id
     */
    public function getAuditAuthority($id){
        $reim_department_id_array = $this->getReimDepartmentId();
        if(!$reim_department_id_array){
            return array('msg' => 'warning', 'result' => '当前用户不是审核人!');
        }
        $reim = Reimbursement::where('status_id', 3)->whereIn('reim_department_id', $reim_department_id_array)->find($id);//获取当前审核人的审核单数据
        if(count($reim)<1){
            return array('msg'=>'error','result'=>'当前报销单不存在！或已被其他人处理了。请刷新页面再试！');
        }
        return array('msg'=>'success');
    }

    /**
     * 获取审核的权限id
     */
    public function getReimDepartmentId(){
        $staff_sn = app('CurrentUser')->staff_sn;
        $reim_department_id_array = Auditor::where('auditor_staff_sn', $staff_sn)->pluck('reim_department_id');//当前审核人的审核权限资金归属id
        if(count($reim_department_id_array)>0){
            return $reim_department_id_array;
        }
        return false;
    }

    /**
     * 筛选（获取已审核单的资金归属列表）
     */
    public function getReimDepartmentName()
    {
        return Reim_department::withTrashed()->get();
    }

} 
