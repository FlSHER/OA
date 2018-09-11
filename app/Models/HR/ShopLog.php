<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class ShopLog extends Model
{
    protected $table = 'shop_log';

    public $fillable = [
        'target_id',
        'admin_sn',
        'changes',
    ];
    
    protected $casts = [
        'changes' => 'array',
    ];
}