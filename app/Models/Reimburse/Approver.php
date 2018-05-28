<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    public $timestamps =false;
    protected $connection='reimburse_mysql';
    protected $fillable = [
        'staff_sn',
        'realname',
        'priority',
        'department_id',
    ];
}
