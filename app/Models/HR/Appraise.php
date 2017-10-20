<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appraise extends Model
{
    use SoftDeletes;
    protected  $table='staff_appraise';
//    protected  $fillable =['staff_sn','position','department','shop','remark','entry_staff_sn','entry_name'];
    protected $dates = ['deleted_at'];

    public function staff(){
        return $this->hasOne('App\Models\HR\Staff','staff_sn','staff_sn')->select(['staff_sn','realname']);
    }
}
