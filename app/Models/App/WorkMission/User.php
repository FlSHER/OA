<?php

namespace App\Models\App\WorkMission;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'work_mission';

    public function department(){
        return $this->hasMany('App\Models\App\WorkMission\Department');
    }
}
