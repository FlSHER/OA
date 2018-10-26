<?php

namespace App\Models\HR;

use App\Scopes\SortScope;
use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class StaffStatus extends Model
{
    use ListScopes;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SortScope);
    }

    protected $table = 'staff_status';

    /* ----- 访问器Start ----- */

    public function getOptionAttribute() { //获取option
        return '<option value="' . $this->id . '">' . $this->name . '</option>';
    }

    /* ----- 访问器End ----- */
}
