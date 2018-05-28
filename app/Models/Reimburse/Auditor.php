<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;

class Auditor extends Model
{
    public $timestamps =false;
    protected $connection = 'reimburse_mysql';
    protected $fillable = [
        'reim_department_id',
        'auditor_staff_sn',
        'auditor_realname',
    ];
  
}
