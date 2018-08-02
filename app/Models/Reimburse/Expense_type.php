<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;

class Expense_type extends Model
{
    protected $connection='reimburse_mysql';

    public function getPicPathAttribute($value){
        return config('reimburse.url') . $value;
    }
}
