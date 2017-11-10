<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{

    use SoftDeletes;

    protected $connection = 'attendance';
    protected $table = 'attendance_shop';

    /* ----- 定义关联 Start ----- */

    public function details()
    {
        $ym = date('Ym', strtotime($this->attendance_date));
        return $this->hasMany(new \App\Models\HR\Attendance\AttendanceStaff(['ym' => $ym]), 'attendance_shop_id')
            ->with('shop_duty')->orderBy('shop_duty_id', 'asc');
    }

    public function shop()
    {
        return $this->belongsTo('App\Models\HR\Shop', 'shop_sn', 'shop_sn');
    }

    /* ----- 定义关联 End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeVisible($query, $staffSn = '')
    {
        $shops = app('Authority')->getAvailableShops($staffSn);
        $query->whereIn('shop_sn', $shops);
    }

    /* ----- 本地作用域 End ----- */
}
