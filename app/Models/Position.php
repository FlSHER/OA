<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model {

    use SoftDeletes;

    protected $guarded = ['id', 'brand'];

    /* ----- 定义关联Start ----- */

    public function department() { //所属部门  ps：职位调整为与品牌关联 by Fisher 2017-03-03
        return $this->belongsToMany('App\Models\Department', 'department_has_positions')->orderBy('parent_id', 'asc')->orderBy('sort', 'asc');
    }

    public function brand() { // 所属品牌
        return $this->belongsToMany('App\Models\Brand', 'brand_has_positions')->orderBy('sort', 'asc')->withTrashed();
    }

    public function authority() { //权限
        return $this->belongsToMany('App\Models\Authority', 'position_has_authorities')->orderBy('parent_id', 'asc')->orderBy('sort', 'asc');
    }

    /* ----- 定义关联End ----- */

    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeApi($query) {
        $query->with('brand');
    }

    /* ----- 本地作用域 End ----- */
}
