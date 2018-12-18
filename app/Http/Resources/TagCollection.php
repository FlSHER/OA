<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TagCollection extends ResourceCollection
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
        return $this->collection->map(function ($tag) {
            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'category' => $tag->category,
                'description' => $tag->description,
                'category_id' => $tag->tag_category_id,
            ];
        })->toArray();
    }
}
