<?php

namespace App\Models\App\WorkMission;

use Illuminate\Database\Eloquent\Model;

class StatisticUser extends Model
{
    protected $connection = "work_mission";

    public $fillable = [
      'staff_sn',
      'realname',
      'department_id',
      'department_name',
    ];
    /**
     * 关联统计的部门
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statisticDepartment(){
        return $this->hasMany('App\Models\App\WorkMission\StatisticDepartment');
    }
}
