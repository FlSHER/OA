<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model {
    /* ----- 定义关联Start ----- */

    public function position() { // 职位
        return $this->belongsToMany('App\Models\Position', 'brand_has_positions')->orderBy('level', 'asc')->orderBy('sort', 'asc');
    }

    /* ----- 定义关联End ----- */

    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeVisible($query) {
        $brands = app('Authority')->getAvailableBrands();
        $query->whereIn('id', $brands);
    }

    public function scopeApi($query) {
        
    }

    /* ----- 本地作用域 End ----- */
}
