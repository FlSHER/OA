<?php

namespace App\Models\Reimburse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $connection="reimburse_mysql";

    public function getPicPathAttribute($value){
        return config('reimburse.url') . $value;
    }
}
