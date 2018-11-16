<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StaffTmp extends Model
{
    protected $table = 'staff_tmp';

    protected $casts = [
    	'changes' => 'array'
    ];

    protected $fillable = [
    	'staff_sn', 
    	'admin_sn', 
    	'changes', 
    	'status', 
    	'operate_at', 
    ];
    
    /**
     * has staff.
     * 
     * @return Illuminate\Database\Eloquent\Concerns\belongsTo || null
     */
    public function staff()
    {
    	return $this->belongsTo(Staff::class, 'staff_sn', 'staff_sn')
    		->select('staff_sn', 'realname')
    		->withTrashed();
    }

    /**
     * has admin.
     * 
     * @return Illuminate\Database\Eloquent\Concerns\belongsTo || null
     */
    public function admin()
    {
    	return $this->belongsTo(Staff::class, 'admin_sn', 'staff_sn')
    		->select('staff_sn', 'realname')
    		->withTrashed();
    }

    /**
     * scope func to status.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLock(Builder $query)
    {
        return $query->where('status', 0);
    }
}
