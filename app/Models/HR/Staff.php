<?php

namespace App\Models\HR;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User;

use Illuminate\Database\Eloquent\SoftDeletes;
use Authority;
use App\Models\Department;
use App\Models\Position;
use App\Models\Brand;
use App\Models\I\Gender;
use App\Models\I\National;
use App\Models\I\MaritalStatus;
use App\Models\I\Politics;

class Staff extends User
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $connection = 'mysql';
    protected $primaryKey = 'staff_sn';
    protected $fillable = [
        'username',
        'realname',
        'mobile',
        'wechat_number',
        'gender_id',
        'birthday',
        'brand_id',
        'department_id',
        'shop_sn',
        'position_id',
        'dingding',
        'status_id',
        'hired_at',
        'employed_at',
        'left_at',
        'is_active',
        'property_id',
    ];
    protected $hidden = ['password', 'salt', 'created_at', 'updated_at', 'deleted_at'];

    public function getAuthIdentifierName()
    {
        return 'staff_sn';
    }

    /* ----- 定义关联 Start ----- */

    public function role()
    { //角色
        return $this->belongsToMany('App\Models\Role', 'staff_has_roles', 'staff_sn');
    }

    public function status()
    { //员工状态
        return $this->belongsTo('App\Models\HR\StaffStatus');
    }

    public function department()
    { //所属部门
        return $this->belongsTo('App\Models\Department')->withTrashed();
    }

    public function position()
    { //职位
        return $this->belongsTo('App\Models\Position');
    }

    public function brand()
    { //所属品牌
        return $this->belongsTo('App\Models\Brand');
    }

    public function shop()
    { //所属店铺
        return $this->belongsTo('App\Models\HR\Shop', 'shop_sn', 'shop_sn')->withTrashed();
    }

    public function shopMiddle()
    { //店铺中间表
        return $this->belongsToMany('App\Models\HR\Shop', 'shop_has_staff', 'staff_sn');
    }

    public function info()
    { //员工信息
        return $this->hasOne('App\Models\HR\StaffInfo', 'staff_sn', 'staff_sn');
    }

    public function gender()
    { //性别
        return $this->belongsTo('App\Models\I\Gender');
    }

    public function change_log()
    { //员工信息变动日志
        return $this->hasMany('App\Models\HR\StaffLog', 'staff_sn')->orderBy('created_at', 'desc');
    }

    public function leaving()
    { //员工离职流程
        return $this->hasOne('App\Models\HR\StaffLeaving', 'staff_sn')->orderBy('created_at', 'desc');
    }

    public function relative()
    { //公司内关系人
        return $this->belongsToMany('App\Models\HR\Staff', 'staff_relatives', 'staff_sn', 'relative_sn')->withPivot('relative_name', 'relative_type', 'relative_sn AS relative_sn');
    }

    public function anti_relative()
    { //公司内关系人-反向
        return $this->belongsToMany('App\Models\HR\Staff', 'staff_relatives', 'relative_sn', 'staff_sn');
    }

    public function tmp()
    { //预约调动
        return $this->hasOne('App\Models\HR\StaffTmp', 'staff_sn');
    }

    public function appraise()
    {//员工评价
        return $this->hasMany('App\Models\HR\Appraise', 'staff_sn', 'staff_sn')->orderBy('create_time', 'desc');
    }
    /* ----- 定义关联 End ----- */

    /* ----- 访问器 Start ----- */

    public function getLatestLoginTimeAttribute($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : '';
    }

    /* ----- 访问器 End ----- */

    /* ----- 修改器 Start ----- */

    public function setBrandAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['brand_id'] = Brand::where('name', $value)->value('id');
        }
    }

    public function setDepartmentAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['department_id'] = Department::where('full_name', $value)->value('id');
        }
    }

    public function setShopSnAttribute($value)
    {
        $this->attributes['shop_sn'] = strtolower($value);
    }

    public function setPositionAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['position_id'] = Position::where('name', $value)->value('id');
        }
    }

    public function setStatusAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['status_id'] = StaffStatus::where('name', $value)->value('id');
        }
    }

    public function setGenderAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['gender_id'] = Gender::where('name', $value)->value('id');
        }
    }

    public function setNationalAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['national_id'] = National::where('name', $value)->value('id');
        }
    }

    public function setMaritalStatusAttribute($value)
    {
        $this->attributes['marital_status_id'] = MaritalStatus::where('name', $value)->value('id');
    }

    public function setPoliticsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['politics_id'] = Politics::where('name', $value)->value('id');
        }
    }

    public function setHiredAtAttribute($value)
    {
        $this->attributes['hired_at'] = empty($value) ? null : $value;
    }

    public function setEmployedAtAttribute($value)
    {
        $this->attributes['employed_at'] = empty($value) ? null : $value;
    }

    public function setLeftAtAttribute($value)
    {
        $this->attributes['left_at'] = empty($value) ? null : $value;
    }

    public function setBirthdayAttribute($value)
    {
        $this->attributes['birthday'] = empty($value) ? null : $value;
    }

    /* ----- 修改器 End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeVisible($query, $staffSn = '')
    {
        $brands = Authority::getAvailableBrands($staffSn);
        $departments = Authority::getAvailableDepartments($staffSn);
        $query->whereIn('brand_id', $brands);
        if (!in_array('0', $departments))
            $query->whereIn('department_id', $departments);
        $query->orWhere('status_id', '<', 0);
    }

    public function scopeApi($query)
    {
        $query->with('brand', 'department', 'position', 'shop', 'status', 'info', 'role');
    }

    /* ----- 本地作用域 End ----- */


    protected function findForPassport($username)
    {
        return $this->where('mobile', $username)->first();
    }
}
