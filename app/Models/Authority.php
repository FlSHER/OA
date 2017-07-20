<?php

/**
 * create by Fisher 2016/8/28 <fisher9389@sina.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\AuthorityService;
use DB;

class Authority extends Model {

    protected $guarded = ['id'];
    protected $appends = ['full_url_tmp'];

    /* ----- 定义关联Start ----- */

    public function _parent() { //上级权限
        return $this->belongsTo('App\Models\Authority', 'parent_id');
    }

    public function _children() { //子权限
        return $this->hasMany('App\Models\Authority', 'parent_id')->orderBy('sort', 'asc');
    }

    /* ----- 定义关联End ----- */

    /* ----- 访问器Start ----- */

    public function getFullNameTmpAttribute() { //权限名称拼接父级
        if ($this->parent_id > 0) {
            return $this->_parent->full_name_tmp . '-' . $this->auth_name;
        } else {
            return $this->auth_name;
        }
    }

    public function getFullUrlTmpAttribute() { //权限地址拼接父级
        $curUrl = empty($this->access_url) ? '' : '/' . $this->access_url;
        if ($this->parent_id > 0) {
            return $this->_parent->full_url_tmp . $curUrl;
        } else {
            return $curUrl;
        }
    }

    /* ----- 访问器End ----- */

    public static function reOrder($authorities, $parentId = 0) {
        foreach ($authorities as $k => $v) {
            $data = ['parent_id' => $parentId, 'sort' => $k + 1];
            self::find($v['id'])->update($data);
            if (isset($v['children'])) {
                self::reOrder($v['children'], $v['id']);
            }
        }
    }

}
