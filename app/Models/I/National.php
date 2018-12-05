<?php

namespace App\Models\I;

use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class National extends Model
{
	use ListScopes;

    protected $table = 'i_national';
    
    /* ----- 访问器Start ----- */

    public function getOptionAttribute()
    {
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */

}
