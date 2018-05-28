<?php

namespace App\Models\I;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model {

    protected $table = 'i_marital_status';

    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */
}
