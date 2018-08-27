<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17/017
 * Time: 11:45
 * 审核仓库
 */

namespace App\Repositories\Reimburse;


use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\Reimbursement;
use Illuminate\Support\Facades\Auth;

class AuditRepository
{

    /**
     * 获取当前审核人的资金归属ID
     */
    public function getReimDepartmentId()
    {
        $staffSn = Auth::id();
        if ($staffSn == '999999') {
            $reimDepartmentIds = Auditor::pluck('reim_department_id')->all();
        } else {
            $reimDepartmentIds = Auditor::where('auditor_staff_sn', $staffSn)->pluck('reim_department_id')->all();
        }
        return $reimDepartmentIds;
    }

    /**
     * 获取审核列表
     * @param $request
     * @return mixed
     */
    public function getListData($request)
    {
        if ($request->has('type') && in_array($request->query('type'), ['processing', 'overtime', 'audited', 'rejected'])) {
            $reimDepartmentIds = $this->getReimDepartmentId();//资金归属ID
            $query = Reimbursement::with([
                'expenses' => function ($query) {
                    $query->where('is_approved', '=', 1);
                },
                'expenses.bills'
            ])->whereIn('reim_department_id', $reimDepartmentIds);
            $curDay = date('d');
            $approveDeadLine = $curDay >= 13 ?
                ($curDay >= 27 ? date('Y-m-28 12:00:00') : date('Y-m-14 12:00:00')) :
                date('Y-m-d H:i:s', strtotime(date('Y-m-28 12:00:00') . ' -1 month'));
            $type = $request->query('type');
            switch ($type) {
                case 'processing':
                    $query->where('status_id', 3)
                        ->where('approve_time', '<=', $approveDeadLine);
                    break;
                case 'overtime':
                    $query->where('status_id', 3)
                        ->where('approve_time', '>', $approveDeadLine);
                    break;
                case 'audited':
                    $query->whereNotNull('audit_time');
                    break;
                case 'rejected':
                    $query->whereNotNull('approve_time')
                        ->whereNull('audit_time')
                        ->where(['status_id' => -1, 'accountant_delete' => 0]);
                    break;
            }
            $data = $query->filterByQueryString()
                ->sortByQueryString()
                ->withPagination();
            return $data;
        }
        abort(400, '请正确输入type类型，不要乱修改哦');
    }

    /**
     * 获取审核详情
     * @param $id
     * @return Reimbursement|Reimbursement[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function detail($id)
    {
        $with = [
            'expenses' => function ($query) {
                $query->where('is_approved', '=', 1);
            },
            'expenses.type',
            'expenses.bills'
        ];
        $data = Reimbursement::with($with)
            ->whereIn('reim_department_id', $this->getReimDepartmentId())
            ->find($id);
        if (empty($data))
            abort(404, '该数据不存在');
        return $data;
    }

    /**
     * 获取打印数据
     * @param $request
     * @return Reimbursement|Reimbursement[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getPrintData($request)
    {
        switch ($request->query('type')) {
            case 'processing'://待审核
                $where = [
                    ['status_id', '=', 3]
                ];
                break;
            case 'audited'://已审核
                $where = [
                    ['status_id', '>=', 4],
                    ['accountant_staff_sn', '=', Auth::id()]
                ];
                break;
            case 'all'://查看报销单
                $where = [
                    ['status_id', '>=', 4]
                ];
                break;
            default:
                $where = [];
        }
        $reimDepartmentIds = $this->getReimDepartmentId();//资金归属ID
        $data = Reimbursement::with('status', 'expenses.type', 'expenses.bills')
            ->where($where)
            ->when((in_array($request->query('type'), ['processing', 'audited'])), function ($query) use ($reimDepartmentIds) {
                return $query->whereIn('reim_department_id', $reimDepartmentIds);
            })
            ->find($request->id);
        if (empty($data))
            abort(403, '你没有当前报销的打印权限');
        $data->increment('print_count');
        $data->send_cost_cny = $this->costToCNY($data->send_cost);//提交金额转换为大写金额
        $data->approved_cost_cny = $data->approved_cost ? $this->costToCNY($data->approved_cost) : $data->approved_cost;//审批金额转换为大写金额
        $data->audited_cost_cny = $data->audited_cost ? $this->costToCNY($data->audited_cost) : $data->audited_cost;//审核金额转换为大写金额
        return $data;
    }

    /**
     * 金额转换为大写
     * @param $cost
     * @return mixed
     */
    private function costToCNY($cost)
    {
        return app('Encyption')->numberToCNY($cost);
    }
}