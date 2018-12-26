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
                'cate_id' => $department->cate_id,
                'brand_id' => $department->brand_id,
                'full_name' => $department->full_name,
                'parent_id' => $department->parent_id,
                'is_locked' => $department->is_locked,
                'is_public' => $department->is_public,
                'manager_sn' => $department->manager_sn,
                'manager_name' => $department->manager_name,
                'category' => $department->category,
                'area_manager_sn' => $department->area_manager_sn,
                'area_manager_name' => $department->area_manager_name,
                'personnel_manager_sn' => $department->personnel_manager_sn,
                'personnel_manager_name' => $department->personnel_manager_name,
                'regional_manager_sn' => $department->regional_manager_sn,
                'regional_manager_name' => $department->regional_manager_name,
                'minister_sn' => $department->minister_sn,
                'minister_name' => $department->minister_name,
                'province_id' => $department->province_id,
            ];
        })->toArray();
    }
}
