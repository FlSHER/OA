<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Models\HR;
use Illuminate\Database\Eloquent\Model;
class ViolationType extends Model{
     protected $fillable = [
        'name',
    ];
    protected $table="violation_type";
    public function reason(){
        return $this->hasMany('App\Models\HR\ViolationReason','type_id');
    }
}
