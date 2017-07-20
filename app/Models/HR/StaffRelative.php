<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use DB;

class StaffRelative extends Model {

    protected $fillable = ['id', 'staff_sn', 'relative_name', 'relative_sn', 'relative_type'];

    /* ----- 定义关联 Start ----- */

    public function staff() { //员工
        return $this->belongsTo('App\Models\HR\Staff', 'staff_sn', 'staff_sn');
    }

    /* ----- 定义关联 End ----- */

    /* ----- 本地作用域 Start ----- */

    public function scopeApi($query) {
        
    }

    /* ----- 本地作用域 End ----- */

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    /* ---- 绑定事件 Start ---- */

    public function onSaving() {
        $this->setAttribute('id', $this->staff_sn . $this->relative_sn);
    }

    public function onSaved() {
        $data = [
            'id' => $this->relative_sn . $this->staff_sn,
            'staff_sn' => $this->relative_sn,
            'relative_sn' => $this->staff_sn,
            'relative_name' => $this->staff->realname,
            'relative_type' => $this->getOppositeType()->id,
        ];
        StaffRelative::insert($data);
    }

    public function onDeleted() {
        StaffRelative::where('relative_sn', $this->staff_sn)->delete();
    }

    /* ---- 绑定事件 End ---- */

    public function getOppositeType() {
        $type = DB::table('staff_relative_type')->where('id', $this->relative_type)->first();
        $oppositeType = DB::table('staff_relative_type')
                        ->where([['group_id', '=', $type->opposite_group_id]])
                        ->where(function($query) {
                            $query->where('gender_id', $this->staff->gender_id)
                            ->orWhere('gender_id', 0);
                        })->first();
        return $oppositeType;
    }

}
