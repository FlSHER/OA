<?php

namespace App\Http\Controllers\Api\HR;

use Cache;
use Encypt;
use Validator;
use App\Models\Brand;
use App\Models\HR\Staff;
use App\Models\I\District;
use App\Models\HR\Position;
use App\Models\HR\CostBrand;
use Illuminate\Http\Request;
use App\Models\HR\Department;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Resources\HR\StaffResource;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\CurrentUserResource;
use App\Http\Resources\HR\StaffCollection;
use App\Services\StaffService;

class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
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
        ->with('relative', 'position', 'department', 'brand', 'shop', 'cost_brands')
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

        $curd = $this->staffService->create($data);

        if ($curd['status'] == 1) {
            $staff = Staff::query()
                ->with(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands'])
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

        $curd = $this->staffService->update($data);

        if ($curd['status'] == 1) {
            $result = $staff->where('staff_sn', $staff->staff_sn)->first();
            $result->load(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands']);

            return response()->json(new StaffResource($result), 201);
        } elseif ($curd['status'] == -1) {
            $result = $staff->where('staff_sn', $staff->staff_sn)->first();
            $result->load(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands']);

            return response()->json(new StaffResource($result), 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        $staff->load(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands']);

        return new StaffResource($staff);
    }


    /**
     * 员工变动日志。
     * 
     * @param  Staff  $staff
     * @return mixed
     */
    public function logs(Staff $staff)
    {
        $staff->load(['change_log', 'change_log.admin', 'change_log.staff']);

        return response()->json($staff->change_log, 200);
    }

    /**
     * 员工预约任务。
     * 
     * @param  Staff  $staff
     * @return mixed
     */
    public function reserve(Staff $staff)
    {
        $staff->load('tmp');

        return response()->json($staff->tmp, 200);
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

    /**
     * 解锁用户.
     * 
     * @param  \App\Models\HR\Staff $staff
     * @return mixed
     */
    public function unlock(Staff $staff)
    {
        $staff->is_active = 1;
        $staff->save();

        return response()->json([
            'message' => '激活成功',
            'changes' => ['is_active' => 1],
        ], 201);
    }

    /**
     * 转正操作.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function process(Request $request)
    {
        $data = $request->all();
        $this->processValidator($data);
        $this->staffService->update($data);

        return response()->json([
            'message' => '转正成功',
            'changes' => $data,
        ], 201);
    }

    /**
     * 人事变动操作。
     * 
     * @param  Request $request
     * @return mixed
     */
    public function transfer(Request $request)
    {
        $data = $request->all();
        $this->processValidator($data);
        $this->staffService->update($data);

        $data['cost_brands'] = CostBrand::whereIn('id', $data['cost_brands'])->get();

        return response()->json([
            'message' => '操作成功',
            'changes' => $data,
        ], 201);
    }

    /**
     * 离职操作。
     * 
     * @param  Request $request
     * @return mixed
     */
    public function leave(Request $request)
    {
        $data = $request->all();
        $this->processValidator($data);
        $this->staffService->update($data);

        return response()->json([
            'message' => '离职成功',
            'changes' => $data,
        ], 201);
    }

    /**
     * 批量导出.
     *
     * @param Request $request
     * @return void
     */
    public function export(Request $request)
    {
        $data = [];
        $hasAuth = app('Authority')->checkAuthority(190); 
        $staff = Staff::query()
            ->with('status', 'brand', 'department', 'position', 'shop', 'cost_brands')
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();

        $staff->map(function ($item, $key) use (&$data, $hasAuth) {

            // 基础数据
            $exportData = $this->makeExportBaseData($item);

            // 筛选掉无权限查看的员工
            $checkBrand = app('Authority')->checkBrand($item->brand_id);
            $checkDepart = app('Authority')->checkDepartment($item->department_id);

            // push 高级权限数据
            if ($hasAuth && ($checkBrand || $checkDepart) && $item->status_id < 0) {

                $exportData = array_merge($exportData, $this->makeExportHighData($item));
            }
            
            $data[$key] = $exportData;
        });
        
        return response()->json($data, 201);
    }

    /**
     * 组装导出基础数据。
     * 
     * @param  [type] $item
     * @return array
     */
    protected function makeExportBaseData($item)
    {
        return [
            'staff_sn' => $item->staff_sn,
            'realname' => $item->realname,
            'gender' => $item->gender,
            'brand' => $item->brand->name,
            'cost_brand' => $item->cost_brands->implode('name', '/'),
            'shop_sn' => $item->shop_sn,
            'shop_name' => $item->shop->name ?? '',
            'department' => $item->department->name,
            'position' => $item->position->name,
            'status' => $item->status->name,
            'hired_at' => $item->hired_at,
        ];
    }

    /**
     * 组装导出高级权限用户数据。
     * 
     * @param  $item
     * @return array
     */
    protected function makeExportHighData($item)
    {
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
        $district->map(function ($city) use (&$temp) {
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

        return [
            'mobile' => $item->mobile,
            'id_card_number' => $item->id_card_number,
            'account_number' => $item->account_number,
            'account_name' => $item->account_name,
            'account_bank' => $item->account_bank,
            'national' => $item->national,
            'wechat_number' => $item->wechat_number,
            'education' => $item->education,
            'politics' => $item->politics,
            'marital_status' => $item->marital_status,
            'height' => $item->height,
            'weight' => $item->weight,
            'household_city' => implode(' ', $makeHouseholdCity).' '.$item->household_address,
            'living_city' => implode(' ', $makeLivingCity).' '.$item->living_address,
            'native_place' => $item->native_place,
            'concat_name' => $item->concat_name,
            'concat_tel' => $item->concat_tel,
            'concat_type' => $item->concat_type,
            'remark' => $item->remark,
        ];
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
            $this->staffService->update($makeVal);
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
            $curd = $this->staffService->create($makeVal);
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
     * 将表单数据转为数据库需要的数据.
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
     * 组装导入数据为(key=>value)形式。
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
        $message = [
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

        return Validator::make($value->toArray(), $rules, $message);
    }

    /**
     * 入转调离操作验证.
     * 
     * @param  array $value
     * @return mixed
     */
    protected function processValidator($value)
    {
        $rules = [
            'staff_sn' => 'required|exists:staff,staff_sn',
            'operate_at' => 'required|date_format:Y-m-d',
            'operation_type' => 'required|in:entry,employ,transfer,leave,reinstate,active,leaving',
            'operation_remark' => 'max:100',
        ];
        $message = [
            'required' => ':attribute 为必填项，不能为空。',
            'in' => ':attribute 必须在【:values】中选择。',
            'max' => ':attribute 不能大于 :max 个字。',
            'exists' => ':attribute 填写错误。',
            'date_format' => '时间格式错误',
        ];

        $type = $value['operation_type'];

        if ($type == 'employ') { //转正
            $rules = array_merge($rules, [
                'status_id' => 'required|in:2',
            ]);
        } elseif ($type == 'transfer') { //变动
            $rules = array_merge($rules, [
                'status_id' => 'required|in:-1,2,3',
                'department_id' => 'required|exists:departments,id',
                'brand_id' => 'required|exists:brands,id',
                'shop_sn' => 'max:10|exists:shops,shop_sn',
                'cost_brands' => 'required|array',
                'position_id' => 'required|exists:positions,id',

            ]);
        } elseif ($type == 'leave') { // 离职
            $rules = array_merge($rules, [
                'status_id' => 'required|in:-1,-2,-3,-4',
                'skip_leaving' => 'in:0,1',
            ]);
        } elseif ($type == 'reinstate') { // 再入职

        }

        return Validator::make($value, $rules, $message)->validate();
    }
}
