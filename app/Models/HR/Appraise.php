<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Appraise extends Model
{
    protected  $table='staff_appraise';
    protected  $fillable =['staff_sn','position','department','shop','remark','entry_staff_sn','entry_name'];

    public function staff(){
        return $this->hasOne('App\Models\HR\Staff','staff_sn','staff_sn')->select(['staff_sn','realname']);
    }
}
