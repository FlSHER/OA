<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Appraise extends Model
{
    protected  $table='staff_appraise';
    protected  $fillable =['staff_sn','remark','entry_staff_sn','entry_name'];
}
