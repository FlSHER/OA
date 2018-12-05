<?php

namespace App\Models;

use App\Models\Traits\ListScopes;
use Illuminate\Database\Eloquent\Model;

class ShopHasTag extends Model
{
	use ListScopes;
	
	public $timestamps = false;

	protected $fillable = ['shop_sn', 'tag_id'];

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'id', 'tag_id');
    }
}
