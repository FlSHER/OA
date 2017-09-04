<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reim_department extends Model {

    use SoftDeletes;
    public $timestamps = false;
    protected $connection = 'reimburse_mysql';
    protected $fillable = ['name'];

    public function auditor() {
        return $this->hasMany('App\Models\Reimburse\Auditor', 'reim_department_id');
    }

}
