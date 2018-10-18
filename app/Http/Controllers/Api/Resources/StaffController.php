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
use App\Contracts\OperationLog;
use App\Contracts\CURD;

class StaffController extends Controller
{
    protected $logService;
    protected $curdService;

    public function __construct(OperationLog $logService, CURD $curd)
    {
        $this->logService = $logService;
        $this->curdService = $curd->log($this->logService);
    }

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
        ->with('relative', 'position', 'department', 'brand', 'shop')
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
    public function store(StoreStaffRequest $request)
    {
        $data = $request->all();

        $curd = $this->curdService->create($data);

        if ($curd['status'] == 1) {
            $staff = Staff::query()
                ->with(['relative', 'position', 'department', 'brand', 'shop'])
                ->orderBy('staff_sn', 'desc')
                ->first();

            return response()->json(new StaffResource($staff), 201);
        }

        return response()->json($curd, 422);
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

        $curd = $this->curdService->update($data);

        if ($curd['status'] == 1) {
            $result = $staff->where('staff_sn', $staff->staff_sn)->first();
            $result->load(['relative', 'position', 'department', 'brand', 'shop']);

            return response()->json(new StaffResource($result), 201);
        }

        return response()->json($curd, 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        $staff->load(['relative', 'position', 'department', 'brand', 'shop']);

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
        $staff->delete();

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
            ->with('status', 'brand', 'department', 'position')
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();
        $staff->map(function ($item, $key) use (&$data) {
            // 查询地区名称
            $temp = [];
            $district = District::whereIn('id', [
                $item->household_province_id,
                $item->household_city_id,
                $item->household_county_id,
                $item->living_province_id,
                $item->living_city_id,
                $item->living_county_id,
            ])->get();
            $district->map(function ($city, $ck) use (&$temp) {
                $temp[$city->id] = $city;
            });
            $makeHouseholdCity = [
                $district->contains($item->household_province_id) ? $temp[$item->household_province_id]['name'] : '',
                $district->contains($item->household_city_id) ? $temp[$item->household_city_id]['name'] : '',
                $district->contains($item->household_county_id) ? $temp[$item->household_county_id]['name'] : '',
            ];
            $makeLivingCity = [
                $district->contains($item->living_province_id) ? $temp[$item->living_province_id]['name'] : '',
                $district->contains($item->living_city_id) ? $temp[$item->living_city_id]['name'] : '',
                $district->contains($item->living_county_id) ? $temp[$item->living_county_id]['name'] : '',
            ];
            // 筛选掉无权限查看的员工
            $checkBrand = app('Authority')->checkBrand($item->brand_id);
            $checkDepart = app('Authority')->checkDepartment($item->department_id);
            if ((!$checkBrand || !$checkDepart) && $item->status_id > 0) {
                $data[$key+1] = [
                    $item->staff_sn,
                    $item->realname,
                    $item->gender,
                    $item->brand->name,
                    $item->cost_brands->implode('name', '/'),
                    $item->department->name,
                    $item->shop_sn,
                    $item->position->name,
                    $item->status->name,
                    $item->hired_at,
                ]; 
            } else {
                $data[$key+1] = [
                    $item->staff_sn,
                    $item->realname,
                    $item->gender,
                    $item->brand->name,
                    $item->cost_brands->implode('name', '/'),
                    $item->department->full_name,
                    $item->shop_sn,
                    $item->position->name,
                    $item->status->name,
                    $item->hired_at,
                    $item->mobile,
                    $item->id_card_number,
                    $item->account_number,
                    $item->account_name,
                    $item->account_bank,
                    $item->national,
                    $item->wechat_number,
                    $item->education,
                    $item->politics,
                    $item->marital_status,
                    $item->height,
                    $item->weight,
                    implode(' ', $makeHouseholdCity).' '.$item->household_address,
                    implode(' ', $makeLivingCity).' '.$item->living_address,
                    $item->native_place,
                    $item->concat_name,
                    $item->concat_tel,
                    $item->concat_type,
                    $item->remark,
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
            ->with('status', 'brand', 'department', 'position')
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();
        $staff->map(function ($item, $key) use (&$data) {
            $data[$key+1] = [
                $item->staff_sn,
                $item->realname,
                $item->gender,
                $item->brand->name,
                $item->cost_brands->implode('name', '/'),
                $item->shop_sn,
                $item->department->name,
                $item->position->name,
                $item->status->name,
                $item->hired_at,
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
        if (count($request->input('cols')) > 25) {
            return $this->createStaffs($request);
        }
        $data = $this->combineImportData($request);

        foreach ($data as $key => $value) {
            $validator = $this->makeValidator($value);
            if ($validator->fails()) {
                return response()->json([
                    'message' =>  "更新失败第 {$key} 条数据错误", 
                    'errors' => $validator->errors(),
                ], 422);
            }
            $makeVal = $this->makeFillStaff($value);
            $makeVal['operation_type'] = 'import_transfer';
            $this->curdService->update($makeVal);
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
                    'message' =>  "导入失败第 {$key} 条数据错误", 
                    'errors' => $validator->errors(),
                ], 422);
            }

            $makeVal = $this->makeFillStaff($value);
            $makeVal['operation_type'] = 'import_entry';
            $curd = $this->curdService->create($makeVal);
            // 费用品牌
            if ($curd['status'] == 1 && isset($value['cost_brand'])) {
                $staff = Staff::query()->orderBy('staff_sn', 'desc')->first();
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
        $data = ['operate_at' => now()->toDateString(), 'operation_remark' => ''];
        if (isset($value['staff_sn'])) {
            $data['realname'] = Staff::where('staff_sn', $value['staff_sn'])->value('realname');
        }
        foreach ($value as $k => $v) {
            if (in_array($k, [
                'realname', 'mobile', 'shop_sn', 'dingtalk_number', 'wechat_number', 'national', 'politics', 'gender',
                'marital_status', 'id_card_number', 'account_number', 'account_bank', 'account_name', 'height',
                'weight', 'household_address', 'living_address', 'native_place', 'education', 'remark',
                'concat_name', 'concat_tel', 'concat_type'
            ])) {
                $data[$k] = $v;
            }
            if ($v && $k === 'brand') {
                $data['brand_id'] = $this->getBrand()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'department') {
                $name = $v;
                if (strpos($v, '-') !== false) {
                    $department = explode('-', $v);
                    $name = end($department);
                }
                $data['department_id'] = $this->getDepartment()->where('name', $name)->pluck('id')->last();
            }
            if ($v && $k === 'position') {
                $data['position_id'] = $this->getPosition()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'status') {
                $status = ['试用期' => 1, '在职' => 2, '停薪留职' => 3, '离职' => -1, '自动离职' => -2, '开除' => -3, '劝退' => -4];
                $data['status_id'] = $status[$v];
            }
            if ($v && $k === 'household_province') {
                $data['household_province_id'] = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'household_city') {
                $data['household_city_id'] = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'household_county') {
                $data['household_county_id'] = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'living_province') {
                $data['living_province_id'] = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'living_city') {
                $data['living_city_id'] = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
            if ($v && $k === 'living_county') {
                $data['living_county_id'] = $this->getDistrict()->where('name', $v)->pluck('id')->last();
            }
        }

        return $data;
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
            'brand' => 'bail|exists:brands,name',
            'position' => 'bail|exists:positions,name',
            'mobile' => 'bail|required|unique:staff,mobile|cn_phone',
            'id_card_number' => 'bail|required|ck_identity',
            'gender' => 'bail|in:未知,男,女',
            'property' => 'bail|in:0,1,2,3,4',
            'status' => 'bail|exists:staff_status,name',
            'national' => 'bail|exists:i_national,name',
            'education' => 'bail|exists:i_education,name',
            'politics' => 'bail|exists:i_politics,name',
            'shop_sn' => 'bail|exists:shops,shop_sn|max:10',
            'marital_status' => 'bail|exists:i_marital_status,name',
            'household_province' => 'bail|exists:i_district,name',
            'household_city' => 'bail|exists:i_district,name',
            'household_county' => 'bail|exists:i_district,name',
            'living_province' => 'bail|exists:i_district,name',
            'living_city' => 'bail|exists:i_district,name',
            'living_county' => 'bail|exists:i_district,name',
            'household_address' => 'bail|string|max:30',
            'living_address' => 'bail|string|max:30',
            'concat_name' => 'bail|max:10',
            'concat_tel' => 'bail|cn_phone',
            'concat_type' => 'bail|max:5',
            'account_bank' => 'bail|max:20',
            'account_name' => 'bail|max:10',
            'account_number' => 'bail|between:16,19',
            'height' => 'bail|integer|between:140,220',
            'weight' => 'bail|integer|between:30,150',
            'dingtalk_number' => 'bail|max:50',
            'remark' => 'bail|max:100',
            'department' => [
                'bail',
                function ($attribute, $value, $fail) {
                    $name = $value;
                    if (strpos($value, '-') !== false) {
                        $department = explode('-', $value);
                        $name = end($department);
                    }
                    if (!Department::where('name', $name)->count()) {
                        $fail('没有部门名称为 “'.$value.'” 的部门！');
                    }
                }
            ],
            'cost_brand' => [
                'bail',
                function ($attribute, $value, $fail) {
                    $cost = CostBrand::pluck('name');
                    if (strpos($value, '/') !== false) {
                        $values = explode('/', $value);
                        foreach ($values as $key => $val) {
                            if (!$cost->contains($val)) {
                                $fail('没有名称为 “'.$val.'” 的费用品牌！');
                            }
                        }
                    } else {
                        if (!$cost->contains($value)) {
                            $fail('没有名称为 “'.$value.'” 的费用品牌！');
                        }
                    }
                }
            ],
        ];
        $messages = [
            'in' => ':attribute 必须在【:values】中选择。',
            'max' => ':attribute 不能大于 :max 个字。',
            'exists' => ':attribute 填写错误。',
            'unique' => ':attribute 已经存在，请重新填写。',
            'required' => ' :attribute 为必填项，不能为空。',
        ];

        if (isset($value['staff_sn'])) {
            $rules = array_merge($rules, [
                'staff_sn' => 'bail|required|exists:staff,staff_sn',
                'mobile' => 'bail|cn_phone',
                'id_card_number' => 'bail|ck_identity',
                'dingtalk_number' => 'bail|max:50',
                'realname' => 'bail|string|max:10',
            ]);
        }

        return Validator::make($value->toArray(), $rules, $messages);
    }
}
