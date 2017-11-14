<?php

namespace App\Http\Controllers\app\Reimburse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Reimburse\Department;
use App\Models\Reimburse\ReimDepartment;
use Curl;

class ReimburseController extends Controller
{

    public $auditorCURD;
    public $approverCURD;

    public function __construct()
    {
        $this->auditorCURD = app('App\Contracts\CURD')->model('App\Models\Reimburse\ReimDepartment');
        $this->approverCURD = app('App\Contracts\CURD')->model('App\Models\Reimburse\Department');
    }

    /* ------------------------审批start----------------------------- */

    /**
     * 审批视图
     * @return type
     */
    public function approverList()
    {
        return view('reimburse.approver');
    }

    /**
     * 获取审批人列表信息
     * @param Request $request
     * @return type
     */
    public function approverInfo(Request $request)
    {
        $result = app('Plugin')->dataTables($request, new Department);
        return $result;
    }

    /**
     * 审批保存
     * @param Request $request
     */
    public function saveApprover(Request $request)
    {
        $this->validate($request, [
            'department_id' => 'required|numeric|unique:reimburse_mysql.departments,department_id,' . $request->id,
            'reim_department_id' => 'required|numeric',
            'approver1' => 'required',
            'approver2' => 'required_with:approver3',
        ], [], trans('fields.reimburse.approver')
        );
        $result = $this->approverCURD->create($request->all());
        $this->ApproverUserInfoToCache(); //审批人信息存入报销的缓存
        return $result;
    }

    /**
     * 删除审批人
     * @param Request $request
     */
    public function delApprover(Request $request)
    {
        $id = $request->id;
        return $this->approverCURD->delete($id, ['approver1', 'approver2', 'approver3']);
    }

    /**
     * 编辑审批
     * @param Request $request
     */
    public function editApprover(Request $request)
    {
        $data = Department::with(['approver1', 'approver2', 'approver3'])->find($request->id);
        return $data;
    }

    /**
     * 审批人信息存入报销系统缓存
     */
    private function ApproverUserInfoToCache()
    {
        $url = config('api.url.reimburse.approverCache');
        Curl::setUrl($url)->get();
    }

    /* --------------------审批end------------- -------------------------- */
    /* --------------------------------审核start---------------------------------- */

    /**
     * 审核人列表
     */
    public function auditorList()
    {
        return view('reimburse.auditor');
    }

    /**
     * 审核人数据
     * @param Request $request
     */
    public function auditorInfo(Request $request)
    {
        $result = app('Plugin')->dataTables($request, new ReimDepartment);
        return $result;
    }

    /**
     * 审核保存
     * @param Request $request
     */
    public function saveAuditor(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:20|string|unique:reimburse_mysql.reim_departments,name,' . $request->id,
            'auditor.*.auditor_realname' => 'required|string|max:10',
            'auditor.*.auditor_staff_sn' => 'required_with:auditor.*.auditor_realname|string|max:9'
        ], [], trans('fields.reimburse.auditor')
        );
        $result = $this->auditorCURD->create($request->all());
        return $result;
    }

    /**
     * 审核数据删除
     * @param Request $request
     */
    public function delAuditor(Request $request)
    {
        return $this->auditorCURD->delete($request->id, ['auditor']);
    }

    /**
     * 审核修改
     * @param Request $request
     */
    public function editAuditor(Request $request)
    {
        $id = $request->id;
        $data = ReimDepartment::with('auditor')->find($id);
        return $data;
    }

    /* --------------------------------审核end---------------------------------- */
}
