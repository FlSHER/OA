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

class PayRepository
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
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
        return $data;
    }
}