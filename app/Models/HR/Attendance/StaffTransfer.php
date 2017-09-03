<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffTransfer extends Model {

    use SoftDeletes;

    protected $table = 'staff_transfer';
    protected $fillable = [
        'staff_sn',
        'staff_name',
        'staff_gender',
        'staff_department',
        'current_shop_sn',
        'leaving_shop_sn',
        'left_at',
        'arriving_shop_sn',
        'arriving_shop_duty',
        'arrived_at',
        'status',
        'maker_sn',
        'maker_name',
        'remark',
    ];

    /* ----- 定义关联 Start ----- */

    public function staff() { //员工
        return $this->belongsTo('App\Models\HR\Staff', 'staff_sn');
    }

    public function current_shop() { //当前店铺
        return $this->belongsTo('App\Models\HR\Shop', 'current_shop_sn', 'shop_sn');
    }

    public function leaving_shop() { //离开店铺
        return $this->belongsTo('App\Models\HR\Shop', 'leaving_shop_sn', 'shop_sn');
    }

    public function arriving_shop() { //到达店铺
        return $this->belongsTo('App\Models\HR\Shop', 'arriving_shop_sn', 'shop_sn');
    }

    public function tag() {
        return $this->belongsToMany('App\Models\HR\Attendance\StaffTransferTag', 'staff_transfer_has_tags', 'transfer_id', 'tag_id');
    }

    /* ----- 定义关联 End ----- */

    /* ----- 修改器Start ----- */

    public function setLeavingShopSnAttribute($value) {
        $this->attributes['leaving_shop_sn'] = strtolower($value);
    }

    public function setArrivingShopSnAttribute($value) {
        $this->attributes['arriving_shop_sn'] = strtolower($value);
    }

    /* ----- 修改器End ----- */

    /* ----- 事件回调 Start ----- */

    public function onSaving() {
        $this->setAttribute('staff_gender', $this->staff->gender->name);
        $this->setAttribute('staff_department', $this->staff->department->name);
        $this->setAttribute('current_shop_sn', $this->staff->shop_sn);
    }

    public function onSaved() {
        $leftAt = strtotime($this->left_at);
        if ($leftAt <= time()) {
            $this->staff->shop_sn = $this->arriving_shop_sn;
            $this->staff->save();
        } else {
            $tmpData = ['operate_at' => date('Y-m-d', $leftAt), 'shop_sn' => $this->arriving_shop_sn];
            $this->staff->tmp()->updateOrCreate(['operate_at' => date('Y-m-d', $leftAt)], $tmpData);
        }
    }

    /* ----- 事件回调 End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeVisible($query) {
        $query->whereHas('staff', function($query) {
            $query->visible();
        });
    }

    /* ----- 本地作用域 End ----- */
}
