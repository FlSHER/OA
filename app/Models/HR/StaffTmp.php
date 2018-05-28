<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class StaffTmp extends Model {

    protected $table = 'staff_tmp';
    protected $fillable = ['staff_sn', 'department_id', 'position_id', 'brand_id', 'status_id', 'shop_sn', 'employed_at', 'left_at', 'operate_at', 'is_active'];
    protected $primaryKey = 'staff_sn';

}
