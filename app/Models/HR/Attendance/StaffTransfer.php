<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\HR\Staff;
use App\Models\HR\Shop;

class StaffTransfer extends Model
{

    use SoftDeletes;

    protected $connection = 'attendance';
    protected $table = 'transfer';
    protected $fillable = [
        'staff_sn',
        'staff_name',
        'staff_gender',
        'staff_department',
        'current_shop_sn',
        'leaving_date',
        'leaving_shop_sn',
        'leaving_shop_name',
        'left_at',
        'arriving_shop_sn',
        'arriving_shop_name',
        'arriving_shop_duty_id',
        'arrived_at',
        'status',
        'maker_sn',
        'maker_name',
        'remark',
    ];

    /* ----- 定义关联 Start ----- */

    //员工
    public function staff()
    {
        return $this->belongsTo('App\Models\HR\Staff', 'staff_sn');
    }

    //当前店铺
    public function current_shop()
    {
        return $this->belongsTo('App\Models\HR\Shop', 'current_shop_sn', 'shop_sn');
    }

    //离开店铺
    public function leaving_shop()
    {
        return $this->belongsTo('App\Models\HR\Shop', 'leaving_shop_sn', 'shop_sn');
    }

    //到达店铺
    public function arriving_shop()
    {
        return $this->belongsTo('App\Models\HR\Shop', 'arriving_shop_sn', 'shop_sn');
    }

    //标签
    public function tag()
    {
        return $this->belongsToMany('App\Models\HR\Attendance\StaffTransferTag', 'transfer_has_tags', 'transfer_id', 'tag_id');
    }

    //到店职务
    public function arriving_shop_duty()
    {
        return $this->belongsTo('App\Models\HR\Attendance\ShopDuty', 'arriving_shop_duty_id');
    }

    /* ----- 定义关联 End ----- */

    /* ----- 修改器Start ----- */

    public function setLeavingShopSnAttribute($value)
    {
        $this->attributes['leaving_shop_sn'] = strtolower($value);
    }

    public function setArrivingShopSnAttribute($value)
    {
        $this->attributes['arriving_shop_sn'] = strtolower($value);
    }

    /* ----- 修改器End ----- */

    /* ----- 事件回调 Start ----- */

    public function onCreating()
    {
        $this->setAttribute('staff_name', $this->staff->realname);
        $this->setAttribute('staff_gender', $this->staff->gender);
        $this->setAttribute('staff_department_id', $this->staff->department_id);
        $this->setAttribute('staff_department_name', $this->staff->department->name);
        $this->setAttribute('current_shop_sn', $this->staff->shop_sn);
    }

    public function onSaving()
    {
        if ($this->leaving_shop_sn) {
            $this->setAttribute('leaving_shop_name', $this->leaving_shop->name);
        }
        if ($this->arriving_shop_sn) {
            $this->setAttribute('arriving_shop_name', $this->arriving_shop->name);
        }
    }

    /* ----- 事件回调 End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeVisible($query)
    {
        $departments = app('Authority')->getAvailableDepartments();
        $query->whereIn('staff_department_id', $departments);
    }

    /* ----- 本地作用域 End ----- */
}
