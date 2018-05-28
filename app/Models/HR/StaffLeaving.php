<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffLeaving extends Model {

    use SoftDeletes;

    protected $table = 'staff_leaving';
    protected $casts = ['attendance' => 'array', 'goods' => 'array', 'punishment' => 'array', 'inventory' => 'array', 'software' => 'array', 'finance' => 'array'];
//    protected $fillable = ['staff_sn',
//        'attendance', 'attendance_operator_sn', 'attendance_operator_name', 'attendance_operate_at',
//        'goods', 'goods_operator_sn', 'goods_operator_name', 'goods_operate_at',
//        'punishment', 'punishment_operator_sn', 'punishment_operator_name', 'punishment_operate_at',
//        'inventory', 'inventory_operator_sn', 'inventory_operator_name', 'inventory_operate_at',
//        'software', 'software_operator_sn', 'software_operator_name', 'software_operate_at',
//        'finance', 'finance_operator_sn', 'finance_operator_name', 'finance_operate_at',
//    ];
    protected $guarded = [];

}
