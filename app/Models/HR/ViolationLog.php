<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViolationLog extends Model {

    protected $casts = ['operation' => 'array'];
    protected $fillable = [
        'operation',
        'operated_at',
        'violation_id',
        'operator_sn',
        'operator_name',
    ];
    protected $table="violation_log";
    public $timestamps = false;
 public function getChangesAttribute($value) {
        $value = json_decode($value, true);
        $data = [];
        foreach ($value as $k => $v) {
            $key = isset($this->columnLocalization[$k]) ? $this->columnLocalization[$k] : $k;
            $data[$key] = $v;
        }
        return $data;
    }
}
