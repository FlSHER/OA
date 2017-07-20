<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\HR\Staff;
use App\Models\I\District;

class StaffInfo extends Model {

    use SoftDeletes;

    protected $primaryKey = 'staff_sn';
    protected $table = 'staff_info';
    protected $fillable = [
        'staff_sn',
        'id_card_number',
        'account_number',
        'account_bank',
        'account_name',
        'account_active',
        'email',
        'qq_number',
        'recruiter_sn',
        'recruiter_name',
        'height',
        'weight',
        'household_province',
        'household_province_id',
        'household_city',
        'household_city_id',
        'household_county',
        'household_county_id',
        'household_address',
        'living_province',
        'living_province_id',
        'living_city',
        'living_city_id',
        'living_county',
        'living_county_id',
        'living_address',
        'native_place',
        'education',
        'mini_shop_sn',
        'remark',
        'relative_sn',
        'relative_name',
        'relative_type',
        'concat_name',
        'concat_tel',
        'concat_type',
        'origin_staff_sn'
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $staff = new Staff;
        $this->guarded = $staff->getFillable();
    }

    /* ----- 定义关联Start ----- */

    public function household_province() { //省级区划
        return $this->belongsTo('App\Models\I\District', 'household_province_id');
    }

    public function household_city() { //市级区划
        return $this->belongsTo('App\Models\I\District', 'household_city_id');
    }

    public function household_county() { //县级区划
        return $this->belongsTo('App\Models\I\District', 'household_county_id');
    }

    public function living_province() { //省级区划
        return $this->belongsTo('App\Models\I\District', 'living_province_id');
    }

    public function living_city() { //市级区划
        return $this->belongsTo('App\Models\I\District', 'living_city_id');
    }

    public function living_county() { //县级区划
        return $this->belongsTo('App\Models\I\District', 'living_county_id');
    }

    /* ----- 定义关联End ----- */

    /* ----- 访问器Start ----- */



    /* ----- 访问器End ----- */

    /* ----- 修改器Start ----- */

    public function setHouseholdProvinceAttribute($value) {
        if (is_string($value)) {
            $this->attributes['household_province_id'] = District::where('name', $value)->value('id');
        }
    }

    public function setHouseholdCityAttribute($value) {
        if (is_string($value)) {
            $this->attributes['household_city_id'] = District::where([['name', '=', $value], ['parent_id', '=', $this->household_province_id]])->value('id');
        }
    }

    public function setHouseholdCountyAttribute($value) {
        if (is_string($value)) {
            $this->attributes['household_county_id'] = District::where([['name', '=', $value], ['parent_id', '=', $this->household_city_id]])->value('id');
        }
    }

    public function setLivingProvinceAttribute($value) {
        if (is_string($value)) {
            $this->attributes['living_province_id'] = District::where('name', $value)->value('id');
        }
    }

    public function setLivingCityAttribute($value) {
        if (is_string($value)) {
            $this->attributes['living_city_id'] = District::where([['name', '=', $value], ['parent_id', '=', $this->living_province_id]])->value('id');
        }
    }

    public function setLivingCountyAttribute($value) {
        if (is_string($value)) {
            $this->attributes['living_county_id'] = District::where([['name', '=', $value], ['parent_id', '=', $this->living_city_id]])->value('id');
        }
    }

    /* ----- 修改器End ----- */
}
