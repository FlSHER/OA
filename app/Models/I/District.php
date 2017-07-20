<?php

namespace App\Models\I;

use Illuminate\Database\Eloquent\Model;

class District extends Model {

    protected $table = 'i_district';

    /* ----- 定义关联Start ----- */

    public function _parent() { //上级区划
        return $this->belongsTo('App\Models\I\District', 'parent_id');
    }

    public function _children() { //下级区划
        return $this->hasMany('App\Models\I\District', 'parent_id')->orderBy('id', 'asc');
    }

    /* ----- 定义关联End ----- */

    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */
}
