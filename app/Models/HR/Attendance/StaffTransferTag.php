<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class StaffTransferTag extends Model
{

    protected $connection = 'attendance';
    protected $table = 'transfer_tags';
    protected $guarded = [];

}
