<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {

    protected $connection = 'attendance';
    protected $table = 'attendance';

    /* ----- 定义关联 Start ----- */

    public function details() {
        return $this->hasMany('App\Models\Attendance\AttendanceStaff', 'parent')->orderBy('staff_sn', 'asc');
    }

    /* ----- 定义关联 End ----- */
}
