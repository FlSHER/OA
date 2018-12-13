<?php

namespace App\Models\Sign;

use Illuminate\Database\Eloquent\Model;

class SignUser extends Model
{
    protected $table = 'sign_user';
    protected $fillable = [
        'user_id',
        'name',
        'department',
        'avatar',
    ];
}
