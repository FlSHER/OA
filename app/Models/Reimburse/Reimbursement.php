<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{

    protected $connection = 'reimburse_mysql';
//    protected $dates = ['send_time','approve_time','audit_time','reject_time','create_time'];


    /**
     * 资金归属
     */
    public function reim_department() {
        return $this->belongsTo('App\Models\Reimburse\Reim_department')->withTrashed();
    }

    public function expenses() {//消费明细
        return $this->hasMany('App\Models\Reimburse\Expense', 'reim_id');
    }

    public function status() {//报销单状态
        return $this->belongsTo('App\Models\Reimburse\Reimbursement_status', 'status_id');
    }
}
