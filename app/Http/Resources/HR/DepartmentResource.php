<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\Resource;

class DepartmentResource extends Resource
{

    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'full_name' => $this->full_name,
            'is_locked' => $this->is_locked,
            'is_public' => $this->is_public,
            'manager_sn' => $this->manager_sn,
            'manager_name' => $this->manager_name,
        ];
    }
}
