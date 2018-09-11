<?php

namespace App\Models\HR;

use Authority;
use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{

    use SoftDeletes, ListScopes;

    protected $connection = 'mysql';

    protected $fillable = [
        'shop_sn',
        'name',
        'manager_sn',
        'manager_name',
        'department_id',
        'brand_id',
        'province_id',
        'city_id',
        'county_id',
        'address',
        'lng',
        'lat',
        'clock_in',
        'clock_out',
    ];

    /* ----- 定义关联Start ----- */

    public function staff()
    { //店员
        return $this->hasMany('App\Models\HR\Staff', 'shop_sn', 'shop_sn')->where([['status_id', '>=', 0]]);
    }

    public function manager()
    { //店长
        return $this->hasOne('App\Models\HR\Staff', 'staff_sn', 'manager_sn');
    }

    public function department()
    { //所属部门
        return $this->belongsTo('App\Models\Department', 'department_id')->withTrashed();
    }

    public function brand()
    { //所属品牌
        return $this->belongsTo('App\Models\Brand', 'brand_id')->withTrashed();
    }

    public function province()
    { //省级区划
        return $this->belongsTo('App\Models\I\District', 'province_id');
    }

    public function city()
    { //市级区划
        return $this->belongsTo('App\Models\I\District', 'city_id');
    }

    public function county()
    { //县级区划
        return $this->belongsTo('App\Models\I\District', 'county_id');
    }

    /* ----- 定义关联End ----- */

    /* ----- 查询器 Start ----- */

    public function getClockInAttribute($value)
    {
        return preg_replace('/^(\d{1,2}:\d{2})(:\d{2})?$/', '$1', $value);
    }

    public function getClockOutAttribute($value)
    {
        return preg_replace('/^(\d{1,2}:\d{2})(:\d{2})?$/', '$1', $value);
    }

    /* ----- 查询器 End ----- */

    /* ----- 修改器Start ----- */

    public function setShopSnAttribute($value)
    {
        $this->attributes['shop_sn'] = trim(strtolower($value));
    }

    /* ----- 修改器End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeVisible($query, $staffSn = '')
    {
        $brands = Authority::getAvailableBrands($staffSn);
        $departments = Authority::getAvailableDepartments($staffSn);
        $query->whereIn('brand_id', $brands);
        if (!in_array('0', $departments))
            $query->whereIn('department_id', $departments);
    }

    public function scopeApi($query)
    {
        $query->with(['staff', 'department', 'brand', 'province', 'city', 'county', 'manager']);
    }

    /* ----- 本地作用域 End ----- */

    /**
     * 记录店铺变更日志。
     *
     * @return void
     */
    public function createShopLog()
    {
        $isDirty = $this->isDirty();
        if ($isDirty === true) {
            $dirty = $this->getDirty();
            $staff = request()->user();
            $logModel = new ShopLog();
            $logModel->fill([
                'target_id' => $this->id,
                'admin_sn' => $staff['staff_sn'],
                'changes' => $dirty,
            ]);
            $logModel->save();
        }
    }
}
