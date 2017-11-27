<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class AttendanceStaff extends Model
{

    protected $connection = 'attendance';
    protected $table = 'attendance_staff_';
    protected $fillable = [
        'attendance_shop_id',
        'staff_sn',
        'staff_name',
        'shop_duty_id',
        'sales_performance_lisha',
        'sales_performance_go',
        'sales_performance_group',
        'sales_performance_partner',
        'working_days',
        'working_hours',
        'leaving_days',
        'leaving_hours',
        'transferring_days',
        'transferring_hours',
        'is_missing',
        'late_time',
        'early_out_time',
        'over_time',
        'is_leaving',
        'is_transferring',
        'clock_log',
        'working_start_at',
        'working_end_at',
        'staff_position_id',
        'staff_position',
        'staff_department_id',
        'staff_department',
        'staff_status_id',
        'staff_status',
        'is_assistor',
        'is_shift',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $ym = array_has($attributes, 'ym') ? $attributes['ym'] : app('AttendanceService')->getAttendanceDate('Ym');
        $this->setMonth($ym);
    }

    /* 定义关联 Start */

    public function staff()
    {
        return $this->belongsTo('App\Models\HR\Staff', 'staff_sn');
    }

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

    /* 自定义方法 Start */

    public function setMonth($month)
    {
        if (!preg_match('/^\d{6}$/', $month)) {
            $month = date('Ym', strtotime($month));
        }
        $this->setTable('attendance_staff_' . $month);
        return $this;
    }

    /* 自定义方法 End */

}
