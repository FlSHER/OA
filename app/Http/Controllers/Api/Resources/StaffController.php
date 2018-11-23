<?php

namespace App\Http\Controllers\Api\Resources;

use Cache;
use Encypt;
use Validator;
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
use App\Http\Requests\StoreStaffRequest;
use App\Http\Resources\HR\StaffResource;
use App\Http\Requests\ImportStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
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
            ->with('relative', 'info', 'gender', 'position', 'department', 'brand', 'shop')
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
    public function store(StoreStaffRequest $request, Staff $staff)
    {
        $data = $request->all();
        $staff->fill($data);
        $staff->hired_at = $data['hired_at'] ?? now();

        $info = new StaffInfo();
        $info->fill($data);

        return $staff->getConnection()->transaction(function () use ($staff, $info, $data) {
            $staff->save();
            $staff->info()->save($info);
            $staff->relative()->attach($data['relatives']);

            $staff->load(['relative', 'info', 'gender', 'position', 'department', 'brand', 'shop']);

            return response()->json(new StaffResource($staff), 201);
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $data = $request->all();
        $staff->fill($data);

        $info = $staff->info;
        $info->fill($data);

        return $staff->getConnection()->transaction(function () use ($staff, $info, $data) {
            $staff->save();
            $info->save();
            if (isset($data['relatives'])) {
                $staff->relative()->detach();
                $staff->relative()->attach($data['relatives']);
            }

            $staff->load(['relative', 'info', 'gender', 'position', 'department', 'brand', 'shop']);

            return response()->json(new StaffResource($staff), 201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        $staff->load(['relative', 'info', 'gender', 'position', 'department', 'brand', 'shop']);

        return new StaffResource($staff);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        \DB::beginTransaction();

        try {

            $staff->delete();
            $staff->info()->delete();

            \DB::commit();

        } catch (\Exception $e) {

            \DB::rollBack();

            return response()->json(['message' => '服务器错误，删除失败'], 500);
        }
        return response()->json(null, 204);
    }

    /**
     * 重置密码 (默认：123456)
     *
     * @param \App\Models\HR\Staff $staff
     * @return mixed
     */
    public function resetPass(Staff $staff)
    {
        $salt = mt_rand(100000, 999999);
        $newPass = Encypt::password('123456', $salt);

        $staff->password = $newPass;
        $staff->salt = $salt;
        $staff->save();

        return response()->json(['message' => '重置成功'], 201);
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
    public function export(Request $request)
    {
        $hasAuth = app('Authority')->checkAuthority(190);
        if (!$hasAuth) {
            return $this->exportStaffInfo($request);
        }
        $data = [$request->input('maxCols')];
        $staff = Staff::query()
            ->with('gender', 'brand', 'info', 'department', 'position', 'shop')
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();
        $staff->map(function ($item, $key) use (&$data) {
            // 查询地区名称
            $temp = [];
            $district = District::whereIn('id', [
                $item->info->household_province_id,
                $item->info->household_city_id,
                $item->info->household_county_id,
                $item->info->living_province_id,
                $item->info->living_city_id,
                $item->info->living_county_id,
            ])->get();
            $district->map(function ($city, $ck) use (&$temp) {
                $temp[$city->id] = $city;
            });
            $makeHouseholdCity = [
                $district->contains($item->info->household_province_id) ? $temp[$item->info->household_province_id]['name'] : '',
                $district->contains($item->info->household_city_id) ? $temp[$item->info->household_city_id]['name'] : '',
                $district->contains($item->info->household_county_id) ? $temp[$item->info->household_county_id]['name'] : '',
            ];
            $makeLivingCity = [
                $district->contains($item->info->living_province_id) ? $temp[$item->info->living_province_id]['name'] : '',
                $district->contains($item->info->living_city_id) ? $temp[$item->info->living_city_id]['name'] : '',
                $district->contains($item->info->living_county_id) ? $temp[$item->info->living_county_id]['name'] : '',
            ];
            // 筛选掉无权限查看的员工
            $checkBrand = app('Authority')->checkBrand($item->brand_id);
            $checkDepart = app('Authority')->checkDepartment($item->department_id);
            if (($checkBrand || $checkDepart) && $item->status_id < 0) {
                $data[$key + 1] = [
                    $item->staff_sn,
                    $item->realname,
                    $item->gender->name,
                    $item->brand->name,
                    $item->cost_brands->implode('name', '/'),
                    $item->department->full_name,
                    $item->shop_sn,
                    $item->shop ? $item->shop->name : '',
                    $item->position->name,
                    $item->status->name,
                    $item->birthday,
                    $item->hired_at,
                    $item->info->remark,
                ];
            } else {
                $data[$key + 1] = [
                    $item->staff_sn,
                    $item->realname,
                    $item->gender->name,
                    $item->brand->name,
                    $item->cost_brands->implode('name', '/'),
                    $item->department->full_name,
                    $item->shop_sn,
                    $item->shop ? $item->shop->name : '',
                    $item->position->name,
                    $item->status->name,
                    $item->birthday,
                    $item->hired_at,
                    $item->mobile,
                    $item->info->id_card_number,
                    $item->info->account_number,
                    $item->info->account_name,
                    $item->info->account_bank,
                    $item->info->national,
                    $item->info->qq_number,
                    $item->wechat_number,
                    $item->info->email,
                    $item->info->education,
                    $item->info->politics,
                    $item->info->marital_status,
                    $item->info->height,
                    $item->info->weight,
                    implode('-', $makeHouseholdCity) . ' ' . $item->info->household_address,
                    implode('-', $makeLivingCity) . ' ' . $item->info->living_address,
                    $item->info->native_place,
                    $item->info->concat_name,
                    $item->info->concat_tel,
                    $item->info->concat_type,
                ];
            }
        });

        return response()->json($data, 201);
    }

    /**
     * 普通权限导出用户信息.
     *
     * @param Request $request
     * @return void
     */
    protected function exportStaffInfo(Request $request)
    {
        $data = [$request->input('minCols')];
        $staff = Staff::query()
            ->with('gender', 'brand', 'department', 'position')
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();
        $staff->map(function ($item, $key) use (&$data) {
            $data[$key + 1] = [
                $item->staff_sn,
                $item->realname,
                $item->gender->name,
                $item->brand->name,
                $item->cost_brands->implode('name', '/'),
                $item->department->full_name,
                $item->shop_sn,
                $item->shop->name,
                $item->position->name,
                $item->status->name,
                $item->birthday,
                $item->hired_at,
                $item->info->remark,
            ];
        });

        return response()->json($data, 201);
    }

    /**
     * 批量导入||批量更新.
     *
     * @param Request $request
     * @return void
     */
    public function import(Request $request)
    {
        if (count($request->input('cols')) > 22) {
            return $this->createStaffs($request);
        }
        $data = $this->combineImportData($request);

        foreach ($data as $key => $value) {
            $validator = $this->makeValidator($value);
            if ($validator->fails()) {
                return response()->json([
                    'message' => "导入失败第 {$key} 条数据错误",
                    'errors' => $validator->errors(),
                ], 422);
            }
            $staff = $this->makeFillStaff($value);
            $staff->save();

            // 员工信息更新
            $staffInfo = $this->makeFillStaffInfo($value);
            $staffInfo->save();
        }

        return response()->json(['message' => '更新成功'], 201);
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

        foreach ($data as $key => $value) {
            $validator = $this->makeValidator($value);
            if ($validator->fails()) {
                return response()->json([
                    'message' => "导入失败第 {$key} 条数据错误",
                    'errors' => $validator->errors(),
                ], 422);
            }
            // 转化数据字段
            $staff = $this->makeFillStaff($value);
            $staff->save();

            // 关联数据表操作
            $staffInfo = $this->makeFillStaffInfo($value);
            $staffInfo->staff_sn = $staff->staff_sn;
            $staffInfo->save();

            // 费用品牌
            if (isset($value['cost_brand'])) {
                $cost = explode('/', $value['cost_brand']);
                $costIds = CostBrand::whereIn('name', $cost)->pluck('id')->toArray();
                $staff->cost_brands()->attach($costIds);
            }
        }

        return response()->json(['message' => '导入成功'], 201);
    }

    /**
     * 批量创建填充员工模型信息.
     *
     * @param array $value
     * @return void
     */
    protected function makeFillStaff($value)
    {
        if (isset($value['staff_sn'])) {
            $staff = Staff::where('staff_sn', $value['staff_sn'])->first();
        } else {
            $staff = new Staff();
            $staff->hired_at = $value['hired_at'] ?? now();
        }
        foreach ($value as $k => $v) {
            if (in_array($k, ['realname', 'mobile', 'shop_sn', 'birthday', 'dingding', 'wechat_number'])) {
                $staff->{$k} = $v;
            }
            if ($v && $k === 'brand') {
                $staff->brand_id = $this->getBrand()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'department') {
                $name = $v;
                if (strpos($v, '-') !== false) {
                    $department = explode('-', $v);
                    $name = end($department);
                }
                $staff->department_id = $this->getDepartment()->where('name', $name)->pluck('id')->last();
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
                $staff->status_id = $marital[$v];
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
    protected function makeFillStaffInfo($value)
    {
        if (isset($value['staff_sn'])) {
            $staffInfo = StaffInfo::where('staff_sn', $value['staff_sn'])->first();
        } else {
            $staffInfo = new StaffInfo();
        }
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

    /**
     * 导入信息合法性验证.
     *
     * @param array $value
     * @return void
     */
    protected function makeValidator($value)
    {
        $rules = [
            'realname' => 'bail|required|string|max:10',
            'mobile' => 'bail|required|unique:staff,mobile|regex:/^1[3456789][0-9]{9}$/',
            'id_card_number' => 'bail|required|max:18',
            'gender' => 'bail|in:男,女,未知',
            'brand' => 'bail|exists:brands,name',
            'position' => 'bail|exists:positions,name',
            'status' => 'bail|exists:staff_status,name',
            'birthday' => 'bail|date',
            'national' => 'bail|exists:i_national,name',
            'education' => 'bail|exists:i_education,name',
            'politics' => 'bail|exists:i_politics,name',
            'marital_status' => 'bail|exists:i_marital_status,name',
            'household_province' => 'bail|exists:i_district,name',
            'household_city' => 'bail|exists:i_district,name',
            'household_county' => 'bail|exists:i_district,name',
            'living_province' => 'bail|exists:i_district,name',
            'living_city' => 'bail|exists:i_district,name',
            'living_county' => 'bail|exists:i_district,name',
            'dingding' => 'bail|required|max:50',
            'department' => [
                'bail',
                function ($attribute, $value, $fail) {
                    $name = $value;
                    if (strpos($value, '-') !== false) {
                        $department = explode('-', $value);
                        $name = end($department);
                    }
                    if (!Department::where('name', $name)->count()) {
                        $fail('没有部门名称为 “' . $value . '” 的部门！');
                    }
                }
            ],
        ];
        $messages = [
            'realname.max' => '姓名长度不能超过 :max 个字',
            'realname.required' => '姓名不能为空',
            'mobile.required' => '手机号码不能为空',
            'mobile.unique' => '手机号码已经存在',
            'mobile.regex' => '手机号码不是一个有效的手机号',
            'id_card_number.required' => '身份证不能为空',
            'id_card_number.max' => '身份证号码无效',
            'gender.in' => '性别填写错误只能有男、女、未知、三种',
            'brand.exists' => '品牌信息错误',
            'position.exists' => '职位信息错误',
            'status.exists' => '员工状态不正确',
            'birthday.date' => '生日不是一个有效的日期',
            'national.exists' => '民族填写错误',
            'education.exists' => '学历填写错误',
            'politics.exists' => '政治面貌填写错误',
            'marital_status.exists' => '婚姻状态填写错误',
            'household_province.exists' => '户口所在地（省）不存在',
            'household_city.exists' => '户口所在地（市）不存在',
            'household_county.exists' => '户口所在地（区）不存在',
            'living_province.exists' => '现居地址（省）不存在',
            'living_city.exists' => '现居地址（市）不存在',
            'living_county.exists' => '现居地址（区）不存在',
            'dingding.required' => '钉钉编号不能为空',
        ];
        if (isset($value->staff_sn)) {
            $rules = array_merge($rules, [
                'staff_sn' => 'bail|required|exists:staff,staff_sn',
                'realname' => 'bail|string|max:10',
                'dingding' => 'bail|max:50',
                'mobile' => 'bail|unique:staff,mobile|regex:/^1[3456789][0-9]{9}$/',
                'id_card_number' => 'bail|max:18',
            ]);
            $messages = array_merge($messages, [
                'staff_sn.required' => '员工编号不能为空',
                'staff_sn.exists' => '员工编号不正确',

            ]);
        }

        return Validator::make($value->toArray(), $rules, $messages);
    }
}
