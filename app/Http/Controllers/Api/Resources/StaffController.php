<?php

namespace App\Http\Controllers\Api\Resources;

use Cache;
use App\Models\Brand;
use App\Models\HR\Staff;
use App\Models\I\District;
use App\Models\I\National;
use App\Models\I\Politics;
use App\Models\HR\Position;
use App\Models\HR\CostBrand;
use App\Models\HR\StaffInfo;
use Illuminate\Http\Request;
use App\Models\HR\Department;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\StaffResource;
use App\Http\Resources\HR\StaffCollection;
use App\Http\Resources\CurrentUserResource;

class StaffController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        preg_match('/role\.id=(.*?)(;|$)/', $request->filters, $match);
        $roleId = false;
        if ($match) {
            $roleId = is_numeric($match[1]) ? $match[1] : json_decode($match[1], true);
            $newFilters = preg_replace('/role\.id=.*?(;|$)/', '$3', $request->filters);
            $request->offsetSet('filters', $newFilters);
        }
        $list = Staff::when($roleId, function ($query) use ($roleId) {
            $query->whereHas('role', function ($query) use ($roleId) {
                if (is_array($roleId)) {
                    $query->whereIn('id', $roleId);
                } else {
                    $query->where('id', $roleId);
                }
            });
        })
        ->with('relative')
        ->filterByQueryString()
        ->sortByQueryString()
        ->withPagination();
        if (isset($list['data'])) {
            $list['data'] = new StaffCollection(collect($list['data']));
            return $list;
        } else {
            return new StaffCollection($list);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        return new StaffResource($staff);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        //
    }


    public function getCurrentUser()
    {
        $staffSn = app('CurrentUser')->staff_sn;
        if ($staffSn == 999999) {
            $currentUser = config('auth.developer');
            $currentUser['authorities'] = [
                'oa' => app('Authority')->getAuthoritiesByStaffSn($staffSn),
                'available_brands' => app('Authority')->getAvailableBrandsByStaffSn($staffSn),
                'available_departments' => app('Authority')->getAvailableDepartmentsByStaffSn($staffSn),
                'available_shops' => app('Authority')->getAvailableShopsByStaffSn($staffSn),
            ];
            return $currentUser;
        } else {
            $currentUser = Staff::find($staffSn);
            return new CurrentUserResource($currentUser);
        }
    }

    /**
     * 批量导出.
     *
     * @param Request $request
     * @return void
     */
    public function export(Request $request, Staff $staff)
    {
        $auth = app('Authority')->checkAuthority(84);
        if ($auth) {

        } else {

        }
    }

    /**
     * 批量导入||批量更新.
     *
     * @param Request $request
     * @return void
     */
    public function import(Request $request)
    {
        if (count($request->input('cols')) > 10) {
            return $this->createStaffs($request);
        }
        $data = $this->combineImportData($request);
        try {
            \DB::beginTransaction();

            foreach ($data as $key => $value) {
                $staff = $this->makeFillUpdateStaff($value);
                $staff->save();
                // 费用品牌更新
                if (isset($value['cost_brand'])) {
                    $cost = explode('/', $value['cost_brand']);
                    $costIds = CostBrand::whereIn('name', $cost)->pluck('id')->toArray();
                    $staff->cost_brands()->detach();
                    $staff->cost_brands()->attach($costIds);
                }
                // 员工备注信息更新
                if (isset($value['remark'])) {
                    $staff->info()->update(['remark' => $value['remark']]);
                }
            }
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();

            return response()->json(['message' => '数据错误，导入失败'], 500);
        }

        return response()->json(['message' => '导入成功'], 201);
    }

    /**
     * 批量创建员工.
     *
     * @param Request $request
     * @return void
     */
    public function createStaffs(Request $request)
    {
        $data = $this->combineImportData($request);
        try {
            \DB::beginTransaction();

            foreach ($data as $key => $value) {
                // 转化数据字段
                $staff = $this->makeFillCreateStaff($value);
                $staff->save();

                // 关联数据表操作
                $staffInfo = $this->makeFillCreateStaffInfo($value);
                $staffInfo->staff_sn = $staff->staff_sn;
                $staffInfo->save();

                // 费用品牌
                if (isset($value['cost_brand'])) {
                    $cost = explode('/', $value['cost_brand']);
                    $costIds = CostBrand::whereIn('name', $cost)->pluck('id')->toArray();
                    $staff->cost_brands()->attach($costIds);
                }
            }

            \DB::commit();
        } catch(Exception $e) {
            \DB::rollback();
        }
    }

    /**
     * 批量创建填充员工模型信息.
     *
     * @param array $value
     * @return void
     */
    protected function makeFillCreateStaff($value)
    {
        $staff = new Staff();
        $staff->username = $value['realname'];
        foreach ($value as $k => $v) {
            if (in_array($k, ['realname', 'mobile', 'shop_sn', 'birthday', 'dingding', 'wechat_number'])) {
                $staff->{$k} = $v;
            }
            if ($v && $k === 'brand') {
                $staff->brand_id = $this->getBrand()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'department') {
                $name = explode('-', $v);
                $staff->department_id = $this->getDepartment()->where('name', end($name))->pluck('id')->last();
            }
            if ($v && $k === 'position') {
                $staff->position_id = $this->getPosition()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'status') {
                $status = ['试用期' => 1, '在职' => 2, '停薪留职' => 3, '离职' => -1, '自动离职' => -2, '开除' => -3, '劝退' => -4];
                $staff->status_id = $status[$v];
            }
            if ($v && $k === 'national') {
                $staff->national_id = $this->getNational()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'politics') {
                $staff->politics_id = $this->getPolitics()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'gender') {
                $gender = ['未知' => 0, '男' => 1, '女' => 2];
                $staff->gender_id = $gender[$v];
            }
            if ($v && $k === 'marital_status') {
                $marital = ['未知' => 0, '未婚' => 1, '已婚' => 2, '离异' => 3, '再婚' => 4, '丧偶' => 5];
                $staff->marital_status_id = $marital[$v];
            }
        }

        return $staff;
    }

    /**
     * 批量创建填充员工信息模型.
     *
     * @param array $value
     * @return void
     */
    protected function makeFillCreateStaffInfo($value)
    {
        $staffInfo = new StaffInfo();
        foreach ($value as $k => $v) {
            if (in_array($k, ['id_card_number', 'account_number', 'account_bank', 'account_name', 'email', 'qq_number', 'national', 'marital_status', 'politics', 'height', 'weight', 'household_address', 'living_address', 'native_place', 'education', 'remark', 'concat_name', 'concat_tel', 'concat_type'])) {
                $staffInfo->{$k} = $v;
            }
            if ($v && $k === 'household_province') {
                $staffInfo->household_province_id = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'household_city') {
                $staffInfo->household_city_id = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'household_county') {
                $staffInfo->household_county_id = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'living_province') {
                $staffInfo->living_province_id = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'living_city') {
                $staffInfo->living_city_id = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'living_county') {
                $staffInfo->living_county_id = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
        }

        return $staffInfo;
    }

    /**
     * 批量更新填充员工模型信息.
     *
     * @param array $value
     * @return void
     */
    protected function makeFillUpdateStaff($value)
    {
        $staff= Staff::where('staff_sn', 110103)->first();
        if (empty($staff)) {
            return false;
        }
        foreach ($value as $k => $v) {
            if (in_array($k, ['realname', 'shop_sn'])) {
                $staff->{$k} = $v;
            }
            if ($v && $k === 'brand') {
                $staff->brand_id = $this->getBrand()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'department') {
                $name = explode('-', $v);
                $staff->department_id = $this->getDepartment()->where('name', end($name))->pluck('id')->last();
            }
            if ($v && $k === 'position') {
                $staff->position_id = $this->getPosition()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'status') {
                $status = ['试用期' => 1, '在职' => 2, '停薪留职' => 3, '离职' => -1, '自动离职' => -2, '开除' => -3, '劝退' => -4];
                $staff->status_id = $status[$v];
            }
        }

        return $staff;
    }
    

     // 缓存职位
     protected function getBrand()
     {
         $key = "brand_list";
         if (Cache::has($key)) return Cache::get($key);
 
         $brand = Brand::select('id', 'name')->get();
         Cache::put($key, $brand, now()->addMinutes(10));
 
         return $brand;   
     }
 
     // 缓存部门
     protected function getDepartment()
     {
         $key = "department_list";
         if (Cache::has($key)) return Cache::get($key);
 
         $department = Department::select('id', 'name')->get();
         Cache::put($key, $department, now()->addMinutes(10));
 
         return $department;   
     }
     
     // 缓存职位
     protected function getPosition()
     {
         $key = "position_list";
         if (Cache::has($key)) return Cache::get($key);
         
         $position = Position::select('id', 'name')->get();
         Cache::put($key, $position, now()->addMinutes(10));
 
         return $position;   
     }

     // 缓存民族
     protected function getNational()
     {
        $key = "national_list";
        if (Cache::has($key)) return Cache::get($key);
        
        $national = National::select('id', 'name')->get();
        Cache::put($key, $national, now()->addMinutes(10));

        return $national; 
     }

     // 缓存政治面貌
     protected function getPolitics()
     {
        $key = "politics_list";
        if (Cache::has($key)) return Cache::get($key);
        
        $politics = Politics::select('id', 'name')->get();
        Cache::put($key, $politics, now()->addMinutes(10));

        return $politics;
     }

     // 缓存地区
     protected function getDistrict()
     {
         $key = "district_list";
         if (Cache::has($key)) return Cache::get($key);
        
         $district = District::select('id', 'name')->get();
         Cache::put($key, $district, now()->addMinutes(10));
 
         return $district;
     }

    /**
     * 组装导入数据。
     *
     * @param Request $request
     * @return void
     */
    protected function combineImportData(Request $request)
    {
        $temp = [];
        $data = array_filter($request->input('data', []));
        $cols = $request->input('cols', []);
        if (count($data) <= 1) return false;
        foreach ($data as $key => $value) {
            if ($key >= 1) {
                $temp[$key] = collect($cols)->combine($value)->filter();
            }
        }
        return $temp;
    }
}
