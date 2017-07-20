<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Violation extends Model {
use SoftDeletes;
    protected $fillable = [
        'staff_name',
        'staff_sn',
        'brand',
		'brand_id',
        'department',
		'department_id',
        'position',
		'position_id',
        'type_id',
        'reason',
        'price',
		'fruit',
		'times',
        'supervisor_sn',
        'supervisor_name',
        'maker_sn',
        'maker_name',
        'committed_at',
        'submitted_at',
        'paid_at',
    ];
 protected $dates = ['deleted_at'];
  public function type(){
        return $this->belongsTo('App\Models\HR\ViolationType');
    }
}
