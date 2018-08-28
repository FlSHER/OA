<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReimDepartment extends Model
{

    use SoftDeletes;
    public $timestamps = false;
    protected $connection = 'reimburse_mysql';
    protected $fillable = [
        'name',
        'manager_sn',
        'manager_name',
        'cashier_sn',
        'cashier_name'
    ];

    public function auditor()
    {
        return $this->hasMany('App\Models\Reimburse\Auditor', 'reim_department_id');
    }

}
