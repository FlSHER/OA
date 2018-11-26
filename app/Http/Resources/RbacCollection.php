<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RbacCollection extends ResourceCollection
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
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'auth_name' => $item->auth_name,
                'access_url' => $item->access_url,
                'parent_id' => $item->parent_id,
                'menu_name' => $item->menu_name,
                'menu_logo' => $item->menu_logo,
                'is_menu' => $item->is_menu,
                'is_lock' => $item->is_lock,
                'is_public' => $item->is_public,
                'full_url_tmp' => $item->full_url_tmp,
            ];
        })->toArray();
    }
}
