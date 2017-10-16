<?php

namespace App\Models\HR\Attendance;

use App\Models\App;
use Illuminate\Database\Eloquent\Model;

class Clock extends Model
{
    protected $connection = 'attendance';
    protected $table = 'clock_';
    protected $guarded = ['ym'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $ym = array_has($attributes, 'ym') ? $attributes['ym'] : date('Ym');
        $this->table .= $ym;
    }

    /* 访问器 Start */

    public function getPhotoAttribute($value)
    {
        $url = App::find(5)->url;
        return $url . $value;
    }

    public function getThumbAttribute($value)
    {
        $url = App::find(5)->url;
        return $url . $value;
    }

    /* 访问器 End */
}
