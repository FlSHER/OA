<?php

namespace App\Models\I;

use Illuminate\Database\Eloquent\Model;

class Education extends Model {

    protected $table = 'i_education';

    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->name . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */
}
