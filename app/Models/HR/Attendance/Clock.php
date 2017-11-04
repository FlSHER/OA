<?php

namespace App\Models\HR\Attendance;

use App\Models\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clock extends Model
{
    use SoftDeletes;
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
        if (!empty($value)) {
            $url = App::find(5)->url;
            return $url . $value;
        } else {
            return '';
        }
    }

    public function getThumbAttribute($value)
    {
        if (!empty($value)) {
            $url = App::find(5)->url;
            return $url . $value;
        } else {
            return '';
        }
    }

    /* 访问器 End */
}
