<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
class TransferMid extends Model
{
    //
    use SoftDeletes;
    protected $table = 'transfer_mid';
    protected $dates = ['deleted_at'];
    protected $fillable = ['transfer_id','go_shop_id','memo'];
}
