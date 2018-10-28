<?php

namespace App\Models;

use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use ListScopes;

    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    protected $fillable = ['name', 'tag_category_id', 'weight'];


    public function category()
    {
        return $this->hasOne(TagCategory::class, 'id', 'tag_category_id');
    }

    public function taggable()
    {
        return $this->hasMany(Taggable::class, 'tag_id', 'id');
    }
}
