<?php

namespace App\Models\Dingtalk;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'dingtalk_messages';
    protected $fillable = [
        'client_id',
        'agent_id',
        'create_staff',
        'create_realname',
        'msgtype',
        'data',
        'step_run_id',
        'errcode',
        'task_id',
        'request_id',
    ];

    protected $casts = [
        'data'=>'array',
    ];

    public function setDataAttribute($value){
        $this->attributes['data'] = json_encode($value);
    }
}
