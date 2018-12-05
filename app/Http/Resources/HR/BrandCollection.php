<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BrandCollection extends ResourceCollection
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
        return $this->collection->map(function ($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'is_public' => $brand->is_public,
                'cost_brands' => $brand->cost_brands,
            ];
        })->toArray();
    }
}
