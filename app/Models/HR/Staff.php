<?php

namespace App\Models\HR;

use Authority;
use App\Models;
use App\Models\Traits;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Api\Resources\StaffAvatarController;

class Staff extends User
{
    use Notifiable,
        SoftDeletes,
        HasApiTokens,
        Traits\HasAvatar,
        Traits\ListScopes;

    protected $connection = 'mysql';
    protected $primaryKey = 'staff_sn';
    protected $fillable = [
        'username',
        'realname',
        'mobile',
        'gender',
        'brand_id',
        'department_id',
        'shop_sn',
        'position_id',
        'dingtalk_number',
        'is_active',
        'status_id',
        'hired_at',
        'employed_at',
        'left_at',
        'property',
        'id_card_number',
        'account_number',
        'account_bank',
        'account_name',
        'account_active',
        'recruiter_sn',
        'recruiter_name',
        'household_province_id',
        'household_city_id',
        'household_county_id',
        'household_address',
        'living_province_id',
        'living_city_id',
        'living_county_id',
        'living_address',
        'national',
        'marital_status',
        'politics',
        'education',
        'height',
        'weight',
        'native_place',
        'remark',
        'concat_name',
        'concat_tel',
        'concat_type',
        'wechat_number',
    ];

    protected $appends = ['avatar'];

    protected $hidden = ['password', 'salt', 'created_at', 'updated_at', 'deleted_at'];

    public function getAuthIdentifierName()
    {
        return 'staff_sn';
    }

    /* ----- 定义关联 Start ----- */

    public function info()
    { //员工信息
        return $this->hasOne('App\Models\HR\StaffInfo', 'staff_sn', 'staff_sn')->withTrashed();
    }

    public function household_province()
    { //省级区划
        return $this->belongsTo('App\Models\I\District', 'household_province_id');
    }

    public function household_city()
    { //市级区划
        return $this->belongsTo('App\Models\I\District', 'household_city_id');
    }

    public function household_county()
    { //县级区划
        return $this->belongsTo('App\Models\I\District', 'household_county_id');
    }

    public function living_province()
    { //省级区划
        return $this->belongsTo('App\Models\I\District', 'living_province_id');
    }

    public function living_city()
    { //市级区划
        return $this->belongsTo('App\Models\I\District', 'living_city_id');
    }

    public function living_county()
    { //县级区划
        return $this->belongsTo('App\Models\I\District', 'living_county_id');
    }

    public function role()
    { //角色
        return $this->belongsToMany('App\Models\Role', 'staff_has_roles', 'staff_sn');
    }

    public function roles()
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
        return $this->belongsTo('App\Models\Brand')->withTrashed();
    }

    public function cost_brands()
    { //所属品牌
        return $this->belongsToMany(CostBrand::class, 'staff_has_cost_brands', 'staff_sn');
    }

    public function shop()
    { //所属店铺
        return $this->belongsTo('App\Models\HR\Shop', 'shop_sn', 'shop_sn')->withTrashed();
    }

    public function shopMiddle()
    { //店铺中间表
        return $this->belongsToMany('App\Models\HR\Shop', 'shop_has_staff', 'staff_sn');
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
        return $this->belongsToMany('App\Models\HR\Staff', 'staff_relatives', 'staff_sn', 'relative_sn')
            ->withPivot('relative_name', 'relative_type', 'relative_sn');
    }

    public function anti_relative()
    { //公司内关系人-反向
        return $this->belongsToMany('App\Models\HR\Staff', 'staff_relatives', 'relative_sn', 'staff_sn');
    }

    public function tmp()
    { //预约调动
        return $this->hasMany('App\Models\HR\StaffTmp', 'staff_sn', 'staff_sn');
    }

    public function appraise()
    {//员工评价
        return $this->hasMany('App\Models\HR\Appraise', 'staff_sn', 'staff_sn')->orderBy('create_time', 'desc');
    }

    /**
     * has gender.
     * 
     * @author 28youth
     * @return \Illuminate\Database\Eloquent\Concerns\hasOne
     */
    public function igender()
    {
        return $this->hasOne('App\Models\I\Gender', 'name', 'gender');
    }
    /* ----- 定义关联 End ----- */

    /* ----- 修改器 Start ----- */

    public function setBrandAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['brand_id'] = Models\Brand::where('name', $value)->value('id');
        }
    }

    public function setDepartmentAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['department_id'] = Models\Department::where('full_name', $value)->value('id');
        }
    }

    public function setShopSnAttribute($value)
    {
        $this->attributes['shop_sn'] = strtolower($value);
    }

    public function setPositionAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['position_id'] = Models\Position::where('name', $value)->value('id');
        }
    }

    public function setStatusAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['status_id'] = StaffStatus::where('name', $value)->value('id');
        }
    }

    public function setPoliticsAttribute($value)
    {
        $this->attributes['politics'] = !empty($value) ? $value : '未知';
    }

    public function setEducationAttribute($value)
    {
        $this->attributes['education'] = !empty($value) ? $value : '未知';;
    }

    public function setMaritalStatusAttribute($value)
    {
        $this->attributes['marital_status'] = !empty($value) ? $value : '未知';
    }

    public function setNationalAttribute($value)
    {
        $this->attributes['national'] = !empty($value) ? $value : '未知';
    }

    public function setHouseholdProvinceIdAttribute($value)
    {
        $this->attributes['household_province_id'] = $value ?: 0;
    }

    public function setHouseholdCityIdAttribute($value)
    {
        $this->attributes['household_city_id'] = $value ?: 0;
    }

    public function setHouseholdCountyIdAttribute($value)
    {
        $this->attributes['household_county_id'] = $value ?: 0;
    }

    public function setLivingProvinceIdAttribute($value)
    {
        $this->attributes['living_province_id'] = $value ?: 0;
    }

    public function setLivingCityIdAttribute($value)
    {
        $this->attributes['living_city_id'] = $value ?: 0;
    }

    public function setLivingCountyIdAttribute($value)
    {
        $this->attributes['living_county_id'] = $value ?: 0;
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
        $query->with('brand', 'department', 'position', 'shop', 'status', 'role');
    }

    public function scopeWithApi($query)
    {
        return $query->with(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands', 'tags']);
    }

    public function scopeWorking($query)
    {
        $query->where('status_id', '>=', 0)->where('is_active', 1);
    }

    /* ----- 本地作用域 End ----- */


    protected function findForPassport($username)
    {
        return $this->where('mobile', $username)->first();
    }

    public function getAvatarKey()
    {
        return $this->getKey();
    }

    /**
     * Get avatar attribute.
     *
     * @return string|null
     */
    public function getAvatarAttribute()
    {
        if (! $this->avatarPath()) {
            return null;
        }

        // return $this->avatar(50);
        return action('\\'.StaffAvatarController::class.'@show', ['staff' => $this]);
    }


    /**
     * Has tags of the staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Models\Tag::class, 'taggable', 'taggables')
            ->withTimestamps();
    }
}
