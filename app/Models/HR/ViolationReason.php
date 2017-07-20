<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Models\HR;
use Illuminate\Database\Eloquent\Model;
class ViolationReason extends Model{
    
    protected $table="violation_reason";
     protected $fillable = [
        'type_id',
         'content',
		 'prices',
    ];
     public function type(){
        return $this->belongsTo('App\Models\HR\ViolationType','type_id');
    }
   
}
