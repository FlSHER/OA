<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class LeaveLog extends Model
{
    protected $connection = 'attendance';
    protected $table = 'leave_log';
    protected $guarded = [];
}
