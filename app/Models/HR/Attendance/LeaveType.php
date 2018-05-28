<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $connection = 'attendance';
    protected $table = 'leave_type';
}
