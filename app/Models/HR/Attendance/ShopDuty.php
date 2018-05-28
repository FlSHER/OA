<?php

namespace App\Models\HR\Attendance;

use Illuminate\Database\Eloquent\Model;

class ShopDuty extends Model
{
    protected $connection = 'attendance';
    protected $table = 'shop_duty';
}
