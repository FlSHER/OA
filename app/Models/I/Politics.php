<?php

namespace App\Models\I;

use Illuminate\Database\Eloquent\Model;

class Politics extends Model {

    protected $table = 'i_politics';

    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */
}
