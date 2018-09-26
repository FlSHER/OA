<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/21/021
 * Time: 9:02
 *
 * 转账仓库
 */

namespace App\Repositories\Reimburse;


use App\Models\Reimburse\Reimbursement;
use App\Models\Reimburse\ReimDepartment;
use Illuminate\Support\Facades\Auth;

class PublicRepository
{
    /**
     * 获取转账列表
     * @param $request
     * @return mixed
     */
    public function getPayList($request)
    {
        $type = $request->query('type');
        if (!($request->has('type') && in_array($type, ['paid', 'not_paid']))) {
            abort(404, '请正确输入type类型');
        }
        if ($type == 'paid') {//已转账
            $where = [
                ['status_id', '=', 7]
            ];
        } else if ($type == 'not_paid') {//未转账
            $where = [
                ['status_id', '=', 6]
            ];
        }
        $with = [
            'expenses' => function ($query) {
                $query->where('is_audited', '=', 1);
                $query->orderBy('date', 'asc');
            },
            'expenses.bills'
        ];
        $data = Reimbursement::with($with)
            ->where($where)
            ->where('payee_is_public', 1)
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
        return $data;
    }

    /**
     * 获取导出列表
     * @param $request
     * @return mixed
     */
    public function getExportList($request)
    {
        $type = $request->query('type');
        if (!($request->has('type') && in_array($type, ['paid', 'not_paid']))) {
            abort(404, '请正确输入type类型');
        }
        if ($type == 'paid') {//已转账
            $where = [
                ['status_id', '=', 7]
            ];
        }
        $with = [
            'expenses' => function ($query) {
                $query->where('is_audited', '=', 1);
                $query->orderBy('date', 'asc');
            },
        ];
        $data = Reimbursement::with($with)
            ->where($where)
            ->where('payee_is_public', 1)
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();
        return $data;
    }
}