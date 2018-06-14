<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DepartmentCollection extends ResourceCollection
{
    public static $wrap = null;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($department) {
            return [
                'id' => $department->id,
                'name' => $department->name,
                'full_name' => $department->full_name,
                'is_locked' => $department->is_locked,
                'is_public' => $department->is_public,
                'manager_sn' => $department->manager_sn,
                'manager_name' => $department->manager_name,
            ];
        })->toArray();
    }
}
