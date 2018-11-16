<?php

namespace App\Models\HR;

use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBrand extends Model
{
	use ListScopes;
    use SoftDeletes;

    protected $appends = ['brand_ids'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'pivot'];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_has_cost_brands', 'cost_brand_id');
    }

    public function getBrandIdsAttribute()
    {
    	return $this->brands()->pluck('id');
    }
}
