<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model
{
    protected $fillable = ['name', 'type', 'color', 'weight'];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Has tags of the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany(Tag::class, 'tag_category_id', 'id')
            ->orderBy('weight', 'desc');
    }
}
