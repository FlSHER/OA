<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $connection = 'reimburse_mysql';
    public $timestamps =false;


    public function type(){
        return $this->belongsTo('App\Models\Reimburse\Expense_type');
    }

    public function bills(){
        return $this->hasMany('App\Models\Reimburse\Bill','expense_id');
    }
}
