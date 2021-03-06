<?php

namespace App\Models\HR\Attendance;

use App\Models\App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clock extends Model
{
    use SoftDeletes;
    protected $connection = 'attendance';
    protected $table = 'clock_';
    protected $guarded = ['ym'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $ym = array_has($attributes, 'ym') ? $attributes['ym'] : date('Ym');
        $this->setMonth($ym);
    }

    /* 定义关联 Start */

    public function staff()
    {
        return $this->belongsTo('App\Models\HR\Staff', 'staff_sn');
    }

    public function shop()
    {
        return $this->belongsTo('App\Models\HR\Shop', 'shop_sn', 'shop_sn');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\HR\Staff', 'operator_sn');
    }

    /* 定义关联 End */

    /* 访问器 Start */

    public function getPhotoAttribute($value)
    {
        if (!empty($value)) {
            $url = App::find(5)->url;
            return $url . $value;
        } else {
            return '';
        }
    }

    public function getThumbAttribute($value)
    {
        if (!empty($value)) {
            $url = App::find(5)->url;
            return $url . $value;
        } else {
            return '';
        }
    }

    /* 访问器 End */

    /* 自定义方法 Start */

    public function setMonth($month)
    {
        if (!preg_match('/^\d{6}$/', $month)) {
            $month = date('Ym', strtotime($month));
        }
        $this->setTable('clock_' . $month);
        return $this;
    }

    /* 自定义方法 End */

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('clockOnly', function (Builder $builder) {
            $builder->where('type', '<', 3);
        });
    }
}
