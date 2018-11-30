<?php

namespace App\Models;

use App\Models\HR;
use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use ListScopes;

    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    protected $fillable = ['name', 'tag_category_id', 'description', 'weight'];


    public function category()
    {
        return $this->hasOne(TagCategory::class, 'id', 'tag_category_id');
    }

    public function staff()
    {
        return $this->belongsToMany(HR\Staff::class, 'staff_has_tags', 'tag_id', 'staff_sn');
    }

    public function shop()
    {
        return $this->belongsToMany(HR\Shop::class, 'shop_has_tags', 'tag_id', 'shop_sn');
    }
}
