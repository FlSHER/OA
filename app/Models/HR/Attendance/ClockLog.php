<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class ClockLog extends Model
{
    protected $connection = 'attendance';
    protected $table = 'clock_change_log';
    protected $guarded = [];
}
