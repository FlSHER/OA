<?php

namespace App\Http\Resources\HR;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffCollection extends ResourceCollection
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
        $types = \DB::table('staff_relative_type')
            ->select(['id', 'name'])->get()->mapWithKeys(function ($item) {
                return [$item->id => ['id' => $item->id, 'name' => $item->name]];
            });
        return $this->collection->map(function ($staff) use ($types) {
            $makeRelative = function ($item) use ($types) {
                return [
                    'staff_sn' => $item->staff_sn,
                    'realname' => $item->realname,
                    'relative_type' => $types[$item->pivot->relative_type],
                ];
            };
            return [
                'staff_sn' => $staff->staff_sn,
                'realname' => $staff->realname,
                'username' => $staff->username,
                'mobile' => $staff->mobile,
                'gender' => $staff->gender,
                'brand_id' => $staff->brand_id,
                'department_id' => $staff->department_id,
                'position_id' => $staff->position_id,
                'shop_sn' => $staff->shop_sn,
                'status_id' => $staff->status_id,
                'is_active' => $staff->is_active,
                'hired_at' => $staff->hired_at,
                'employed_at' => $staff->employed_at,
                'left_at' => $staff->left_at,
                'property' => $staff->property,
                'dingtalk_number' => $staff->dingtalk_number,
                'wechat_number' => $staff->wechat_number,

                'shop' => $staff->shop ? $this->makeShop($staff->shop) : null,
                'cost_brands' => $staff->cost_brands,
                'department' => $staff->department->only(['id', 'full_name', 'manager_sn', 'manager_name']),
                'relatives' => $staff->relative ? $staff->relative->map($makeRelative) : [],
                'position' => $staff->position->only(['id', 'name', 'level']),
                'status' => $staff->status->only(['id', 'name']),
                'brand' => $staff->brand->only(['id', 'name']),
                'tags' => $staff->tags,

                'height' => $staff->height,
                'weight' => $staff->weight,
                'recruiter_sn' => $staff->recruiter_sn,
                'recruiter_name' => $staff->recruiter_name,
                'id_card_number' => $staff->id_card_number,
                'account_number' => $staff->account_number,
                'account_bank' => $staff->account_bank,
                'account_name' => $staff->account_name,
                'account_active' => $staff->account_active,
                'household_province_id' => $staff->household_province_id,
                'household_city_id' => $staff->household_city_id,
                'household_county_id' => $staff->household_county_id,
                'household_address' => $staff->household_address,
                'living_province_id' => $staff->living_province_id,
                'living_city_id' => $staff->living_city_id,
                'living_county_id' => $staff->living_county_id,
                'living_address' => $staff->living_address,
                'native_place' => $staff->native_place,
                'education' => $staff->education,
                'national' => $staff->national,
                'marital_status' => $staff->marital_status,
                'politics' => $staff->politics,
                'remark' => $staff->remark,
                'concat_name' => $staff->concat_name,
                'concat_tel' => $staff->concat_tel,
                'concat_type' => $staff->concat_type,
            ];

        })->toArray();
    }

    protected function makeShop($shop)
    {
        $base = $shop->only(['shop_sn', 'name', 'manager_sn', 'manager_name']);
        $address = \DB::table('i_district')
            ->whereIn('id', [
                $shop->province_id,
                $shop->city_id,
                $shop->county_id,
            ])
            ->pluck('name')->implode(' ');

        return array_merge($base, ['address' => $address]);
    }
}
