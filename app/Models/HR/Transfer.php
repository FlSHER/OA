<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
 
class Transfer extends Model
{
    use SoftDeletes;
    protected $table = 'transfer';
    protected $dates = ['deleted_at'];

    protected $statusArr = [
   		'未启程',
   		'行程中',
   		'调动完成',
   		'调动取消'
    ];

    protected $fillable = [
    	'staff_sn',
    	'out_shop_id',
    	'budget',

    ];

    public function getStatusAttribute($value){
    	return $this->statusArr[$value];
    }

    //离开店铺
    public function shopout(){
    	return $this->hasOne('App\Models\HR\Shop','id','out_shop_id');
    }

    //到达店铺
    public function shopto(){
    	return $this->hasOne('App\Models\HR\Shop','id','go_shop_id');
    }

    public function staff(){
        return $this->hasOne('App\Models\HR\staff','staff_sn','staff_sn');
    }
}
