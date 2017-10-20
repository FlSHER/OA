<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class WorkingSchedule extends Model
{
    protected $connection = 'attendance';
    protected $table = 'working_schedule_';
    protected $fillable = [
        'shop_sn',
        'staff_sn',
        'staff_name',
        'clock_in',
        'clock_out',
        'shop_duty_id'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $ymd = array_has($attributes, 'ymd') ? $attributes['ymd'] : date('Ymd');
        $this->table .= $ymd;
    }

    /* ----- 定义关联 Start ----- */

    public function shop()
    {
        return $this->belongsTo('App\Models\HR\Shop', 'shop_sn', 'shop_sn');
    }

    public function shop_duty()
    {
        return $this->belongsTo('App\Models\HR\Attendance\ShopDuty', 'shop_duty_id');
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
