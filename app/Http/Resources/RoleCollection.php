<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
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
            $staff = $item->staff->map(function ($staff) {
                return $staff->only(['staff_sn', 'realname']);
            });
            return [
                'id' => $item->id,
                'role_name' => $item->role_name,
                'staff' => $staff
            ];
        })->toArray();
    }
}
