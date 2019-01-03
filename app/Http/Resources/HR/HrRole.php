<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\Resource;

class HrRole extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        Resource::withoutWrapping();

        return parent::toArray($request);
    }
}
