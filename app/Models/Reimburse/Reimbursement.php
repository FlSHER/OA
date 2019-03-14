<?php

namespace App\Models\Reimburse;

use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reimbursement extends Model
{
    use ListScopes,SoftDeletes;

    protected $connection = 'reimburse_mysql';
//    protected $dates = ['send_time','approve_time','audit_time','reject_time','create_time'];


    /**
     * 资金归属
     */
    public function reim_department()
    {
        return $this->belongsTo('App\Models\Reimburse\ReimDepartment')->withTrashed();
    }

    public function expenses()
    {//消费明细
        return $this->hasMany('App\Models\Reimburse\Expense', 'reim_id')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');
    }

    public function status()
    {//报销单状态
        return $this->belongsTo('App\Models\Reimburse\Reimbursement_status', 'status_id');
    }
}
