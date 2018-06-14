<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class StaffRelativeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($relative) {
            return [
                'staff_sn' => $relative->staff_sn,
                'realname' => $relative->realname,
                'relative_type' => DB::table('staff_relative_type')
                    ->select(['id', 'name'])
                    ->where('id', $relative->pivot->relative_type)->first(),
            ];
        })->toArray();
    }
}
