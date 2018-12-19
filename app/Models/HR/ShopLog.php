<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class ShopLog extends Model
{
    protected $table = 'shop_log';

    public $fillable = [
        'target_id',
        'admin_sn',
        'shop_sn',
        'changes',
    ];
    
    protected $casts = [
        'changes' => 'array',
    ];

    public function shop()
    {
    	return $this->belongsTo(Shop::class, 'shop_sn', 'shop_sn')->withTrashed();
    }
}