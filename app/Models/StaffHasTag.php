<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffHasTag extends Model
{
	use ListScopes;
	
	public $timestamps = false;

	protected $fillable = ['staff_sn', 'tag_id'];

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'id', 'tag_id');
    }
}
