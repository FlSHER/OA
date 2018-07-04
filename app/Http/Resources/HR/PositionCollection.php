<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PositionCollection extends ResourceCollection
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
        return $this->collection->map(function ($position) {
            return [
                'id' => $position->id,
                'name' => $position->name,
                'level' => $position->level,
                'is_public' => $position->is_public,
                'brands' => $position->brand->toArray(),
            ];
        })->toArray();
    }
}
