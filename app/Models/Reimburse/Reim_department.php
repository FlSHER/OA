<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;

class Reim_department extends Model {

    public $timestamps = false;
    protected $connection = 'reimburse_mysql';
    protected $fillable = ['name'];

    public function auditor() {
        return $this->hasMany('App\Models\Reimburse\Auditor', 'reim_department_id');
    }

}
