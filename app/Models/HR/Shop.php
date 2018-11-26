<?php

namespace App\Models\HR;

use Authority;
use App\Models\Tag;
use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Shop extends Model
{

    use SoftDeletes, ListScopes;

    protected $connection = 'mysql';

    protected $hidden = ['password', 'salt', 'deleted_at'];

    protected $primaryKey = 'shop_sn';

    protected $keyType = 'string';

    protected $fillable = [
        'shop_sn',
        'name',
        'department_id',
        'brand_id',
        'province_id',
        'city_id',
        'county_id',
        'address',
        'lng',
        'lat',
        'real_address',
        'clock_in',
        'clock_out',
        'opening_at',
        'end_at',
        'status_id',
        'manager_sn',
        'manager_name',
        'assistant_sn',
        'assistant_name',
        'total_area',
        'shop_type',
        'work_type',
        'work_schedule_id',
    ];

    protected $dirtyAttributes = [];

    public static function boot()
    {
        parent::boot();

        // 获取 dirty
        static::saving(function($shop){
            $dirty = $shop->getDirty();
            $shop->dirtyAttributes = $dirty;
            $shop->setOpeningAt();
        });

        // 保存 dirty
        static::saved(function($shop){
            $shop->recordLog();
        });
    }

    /* ----- 定义关联Start ----- */

    public function staff()
    { //店员
        return $this->hasMany('App\Models\HR\Staff', 'shop_sn', 'shop_sn')->where([['status_id', '>=', 0]]);
    }

    public function manager()
    { //店长
        return $this->hasOne('App\Models\HR\Staff', 'staff_sn', 'manager_sn');
    }

    /**
     * assistant.
     * 
     * @return \Illuminate\Database\Eloquent\Concerns\hasOne
     */
    public function assistant()
    {
        return $this->hasOne(Staff::class, 'staff_sn', 'assistant_sn');
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
        $query->with(['staff', 'department', 'brand', 'province', 'city', 'county', 'manager', 'assistant', 'tags']);
    }

    /**
     * Has tags of the staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables')
            ->withTimestamps();
    }

    /* ----- 本地作用域 End ----- */

    /**
     * 记录店铺变更日志。
     *
     * @return void
     */
    public function recordLog()
    {
        if (!empty($this->dirtyAttributes)) {
            $staff = request()->user();
            $logModel = new ShopLog();
            $logModel->fill([
                'target_id' => $this->id,
                'admin_sn' => $staff['staff_sn'],
                'changes' => $this->dirtyAttributes,
            ]);
            $logModel->save();
        }
    }

    /**
     * 根据店铺状态设置开闭店时间.
     */
    public function setOpeningAt()
    {
        $dirty = $this->dirtyAttributes;
        if (!empty($dirty) && array_has($dirty, 'status_id')) {
            if ($dirty['status_id'] == 2) {

                $this->attributes['opening_at'] = date('Y-m-d');
            } elseif ($dirty['status_id'] == 4) {

                $this->attributes['end_at'] = date('Y-m-d');
            }
        }
    }
}
