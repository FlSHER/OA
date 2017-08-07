<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Authority;

class Shop extends Model {

    use SoftDeletes;

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
    ];

    /* ----- 定义关联Start ----- */

    public function staff() { //店员
        return $this->hasMany('App\Models\HR\Staff', 'shop_sn', 'shop_sn')->where([['status_id', '>=', 0]]);
    }

    public function manager() { //店长
        return $this->hasOne('App\Models\HR\Staff', 'staff_sn', 'manager_sn');
    }

    public function department() { //所属部门
        return $this->belongsTo('App\Models\Department', 'department_id')->withTrashed();
    }

    public function brand() { //所属品牌
        return $this->belongsTo('App\Models\Brand', 'brand_id');
    }

    public function province() { //省级区划
        return $this->belongsTo('App\Models\I\District', 'province_id');
    }

    public function city() { //市级区划
        return $this->belongsTo('App\Models\I\District', 'city_id');
    }

    public function county() { //县级区划
        return $this->belongsTo('App\Models\I\District', 'county_id');
    }

    /* ----- 定义关联End ----- */

    /* ----- 修改器Start ----- */

    public function setShopSnAttribute($value) {
        $this->attributes['shop_sn'] = strtolower($value);
    }

    /* ----- 修改器End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeVisible($query) {
        $brands = Authority::getAvailableBrands();
        $departments = Authority::getAvailableDepartments();
        $query->whereHas('brand', function($q)use($brands) {
            $q->whereIn('id', $brands);
        })->whereHas('department', function($q)use($departments) {
            $q->whereIn('id', $departments)->withTrashed();
        });
    }

    public function scopeApi($query) {
        $query->with(['staff', 'department', 'brand', 'province', 'city', 'county']);
    }

    /* ----- 本地作用域 End ----- */
}
