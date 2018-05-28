<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use DB;

class StaffRelative extends Model
{

    protected $fillable = ['staff_sn', 'relative_sn', 'relative_name', 'relative_type'];

    /* ----- 定义关联 Start ----- */

    public function staff()
    { //员工
        return $this->belongsTo('App\Models\HR\Staff', 'staff_sn', 'staff_sn');
    }

    /* ----- 定义关联 End ----- */

}
