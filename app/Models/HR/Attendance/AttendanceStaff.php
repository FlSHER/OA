<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class AttendanceStaff extends Model
{

    protected $connection = 'attendance';
    protected $table = 'attendance_staff_';
    protected $guarded = ['ym'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $ym = array_has($attributes, 'ym') ? $attributes['ym'] : date('Ym');
        $this->table .= $ym;
    }

    /* 定义关联 Start */

    public function attendance_shop()
    {
        return $this->belongsTo('App\Models\HR\Attendance\Attendance', 'attendance_shop_id');
    }

    public function shop_duty()
    {
        return $this->belongsTo('App\Models\HR\Attendance\ShopDuty', 'shop_duty_id');
    }

    /* 定义关联 End */

    /* 访问器 Start */

    public function getClockLogAttribute($value)
    {
        $response = [];
        while (preg_match('/\d{4}\w\d{4}/', $value, $clockLog)) {
            $start = substr($clockLog[0], 0, 2) . ':' . substr($clockLog[0], 2, 2);
            $end = substr($clockLog[0], -4, 2) . ':' . substr($clockLog[0], -2, 2);
            $response[] = [
                'start' => $start,
                'end' => $end,
                'duration' => strtotime($end) - strtotime($start),
                'type' => substr($clockLog[0], 4, 1)
            ];
            $value = preg_replace('/^.*?\d{4}\w(\d{4}.*)$/', '$1', $value);
        }
        return $response;
    }

    /* 访问器 End */

}
