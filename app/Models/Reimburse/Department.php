<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;

class Department extends Model {

    public $timestamps = false;
    protected $connection = 'reimburse_mysql';
    protected $fillable = [
        'department_id',
        'reim_department_id',
    ];

    public function reim_department() {
        return $this->belongsto('App\Models\Reimburse\Reim_department');
    }

    public function approver1() {
        return $this->hasMany('App\Models\Reimburse\Approver')->where('priority', 1);
    }

    public function approver2() {
        return $this->hasMany('App\Models\Reimburse\Approver')->where('priority', 2);
    }

    public function approver3() {
        return $this->hasMany('App\Models\Reimburse\Approver')->where('priority', 3);
    }

}
