<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class AttendanceStaff extends Model {

    protected $connection = 'attendance';
    protected $table = 'attendance_staff';

}
