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
        $relativeTypes = DB::table('staff_relative_type')
            ->select(['id', 'name'])->get()->mapWithKeys(function ($item) {
                return [$item->id => ['id' => $item->id, 'name' => $item->name]];
            });
        return $this->collection->map(function ($relative) use ($relativeTypes) {
            return [
                'staff_sn' => $relative->staff_sn,
                'realname' => $relative->realname,
                'relative_type' => $relativeTypes[$relative->pivot->relative_type],
            ];
        })->toArray();
    }
}
