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
            'cate_id' => $this->cate_id,
            'brand_id' => $this->brand_id,
            'full_name' => $this->full_name,
            'parent_id' => $this->parent_id,
            'is_locked' => $this->is_locked,
            'is_public' => $this->is_public,
            'manager_sn' => $this->manager_sn,
            'manager_name' => $this->manager_name,
            'category' => $this->category,
            'area_manager_sn' => $this->area_manager_sn,
            'area_manager_name' => $this->area_manager_name,
            'personnel_manager_sn' => $this->personnel_manager_sn,
            'personnel_manager_name' => $this->personnel_manager_name,
            'regional_manager_sn' => $this->regional_manager_sn,
            'regional_manager_name' => $this->regional_manager_name,
            'minister_sn' => $this->minister_sn,
            'minister_name' => $this->minister_name,
            'province_id' => $this->province_id,
            'brand' => $this->brand,
            'parents' => $this->parents,
            'children' => $this->children,
        ];
    }
}
