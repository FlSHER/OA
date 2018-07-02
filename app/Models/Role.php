<?php

namespace App\Models;

use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{

    use SoftDeletes;
    use ListScopes;

    protected $guarded = ['id'];

    /* ----- 定义关联Start ----- */

    public function authority()
    { //权限
        return $this->belongsToMany('App\Models\Authority', 'role_has_authorities')->orderBy('parent_id', 'asc');
    }

    public function staff()
    { //员工
        return $this->belongsToMany('App\Models\HR\Staff', 'staff_has_roles', 'role_id', 'staff_sn');
    }

    public function department()
    { //部门分配
        return $this->belongsToMany('App\Models\Department', 'role_has_departments')->withTrashed()->orderBy('parent_id', 'asc');
    }

    public function brand()
    { //品牌
        return $this->belongsToMany('App\Models\Brand', 'role_has_brands')->orderBy('sort', 'asc');
    }

    /* ----- 定义关联End ----- */
}
