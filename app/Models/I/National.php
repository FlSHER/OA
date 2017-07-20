<?php

namespace App\Models\I;

use Illuminate\Database\Eloquent\Model;

class National extends Model {

    protected $table = 'i_national';
    
    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */

}
