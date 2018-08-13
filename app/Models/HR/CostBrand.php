<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBrand extends Model
{
    use SoftDeletes;

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_has_cost_brands', 'cost_brand_id');
    }
}
