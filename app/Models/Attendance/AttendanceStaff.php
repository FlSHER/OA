<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;

class AttendanceStaff extends Model {

    protected $connection = 'attendance';
    protected $table = 'attendance_staff';

}
