<?php

namespace App\Models\HR;

use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrRole extends Model
{
    use SoftDeletes;
    use ListScopes;

    protected $guarded = ['id'];


    public function staff()
    {
        return $this->belongsToMany('App\Models\HR\Staff', 'hr_staff_has_roles', 'hr_role_id', 'staff_sn')
            ->select('staff.staff_sn', 'staff.realname');
    }

    public function department()
    {
        return $this->belongsToMany('App\Models\Department', 'hr_role_has_departments')
            ->select('departments.id', 'departments.name', 'departments.full_name')
            ->orderBy('parent_id', 'asc')
            ->withTrashed();
    }

    public function brand()
    {
        return $this->belongsToMany('App\Models\Brand', 'hr_role_has_brands')
            ->orderBy('sort', 'asc');
    }
}
