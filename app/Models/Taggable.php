<?php

namespace App\Models;

use App\Models\HR\Staff;
use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    protected $table = 'taggables';

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'taggable_id', 'staff_sn');
    }
}
