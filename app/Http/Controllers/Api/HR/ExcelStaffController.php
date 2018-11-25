<?php 

namespace App\Http\Controllers\Api\HR;

use Cache;
use Validator;
use App\Models\HR;
use App\Models\Brand;
use App\Models\I\District;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Services\StaffService;

class ExcelStaffController extends Controller
{
    
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
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
        $staff = HR\Staff::query()
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
            // dd($hasAuth);
            // merge 高级权限数据
            if ($hasAuth && ($checkBrand || $checkDepart || $item->status_id < 0)) {

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
        $property = ['无', '108将', '36天罡', '24金刚', '18罗汉'];
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
            'employed_at' => $item->employed_at,
            'left_at' => $item->left_at,
            'property' => $property[$item->property],
            'remark' => $item->remark,
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
        $district = District::whereIn('id', [
            $item->household_province_id,
            $item->household_city_id,
            $item->household_county_id,
            $item->living_province_id,
            $item->living_city_id,
            $item->living_county_id,
        ])->get();
        $temp = $district->mapWithKeys(function ($city) {
            return [$city->id => $city->name];
        });
        $makeHouseholdCity = implode(' ', [
            $temp[$item->household_province_id] ?? '',
            $temp[$item->household_county_id] ?? '',
            $temp[$item->household_city_id] ?? '',
        ]);
        $makeLivingCity = implode(' ', [
            $temp[$item->living_province_id] ?? '',
            $temp[$item->living_county_id] ?? '',
            $temp[$item->living_city_id] ?? '',
        ]);

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
            'household_city' => $makeHouseholdCity.' '.$item->household_address,
            'living_city' => $makeLivingCity.' '.$item->living_address,
            'native_place' => $item->native_place,
            'concat_name' => $item->concat_name,
            'concat_tel' => $item->concat_tel,
            'concat_type' => $item->concat_type,
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
            $makeVal['operation_remark'] = 'Excel批量更新';
            $this->staffService->update($makeVal);
        }

        return response()->json(['message' => '更新成功，请刷新页面更新信息'], 201);
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
            $makeVal['operation_remark'] = 'Excel批量导入';
            $curd = $this->staffService->create($makeVal);
        }

        return response()->json(['message' => '导入成功，请刷新页面更新信息'], 201);
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
     * 将表单数据转为数据库需要的数据.
     *
     * @param array $value
     * @return void
     */
    protected function makeFillStaff($value)
    {
        $data = ['operate_at' => now()->toDateString()];
        if (!empty($value['staff_sn'])) {
            $data['staff_sn'] = $value['staff_sn'];
            $data['realname'] = HR\Staff::where('staff_sn', $value['staff_sn'])->value('realname');
        }
        // 费用品牌
        if (!empty($value['cost_brand'])) {
            $cost = explode('/', $value['cost_brand']);
            $costIds = HR\CostBrand::whereIn('name', $cost)->pluck('id')->toArray();
            $data['cost_brands'] = $costIds;
        }
        foreach ($value as $k => $v) { 
            if (in_array($k, [
                'realname', 'mobile', 'shop_sn', 'dingtalk_number', 'wechat_number', 'national', 'politics', 'gender',
                'marital_status', 'id_card_number', 'account_number', 'account_bank', 'account_name', 'height', 'weight',
                'household_address', 'living_address', 'native_place', 'education', 'remark', 'concat_name', 'concat_tel', 'concat_type'
            ])) {
                $data[$k] = $v;
            }
            if ($v && $k === 'brand') {
                $data['brand_id'] = $this->getBrand($v);

            } elseif ($v && $k === 'department') {
                $name = $v;
                if (strpos($v, '-') !== false) {
                    $department = explode('-', $v);
                    $name = end($department);
                }
                $data['department_id'] = $this->getDepartment($name);

            } elseif ($v && $k === 'position') {
                $data['position_id'] = $this->getPosition($v);

            } elseif ($v && $k === 'status') {
                $status = ['试用期' => 1, '在职' => 2, '停薪留职' => 3, '离职' => -1, '自动离职' => -2, '开除' => -3, '劝退' => -4];
                $data['status_id'] = $status[$v];

            } elseif ($v && $k === 'household_province') {
                $data['household_province_id'] = $this->getDistrict($v);

            } elseif ($v && $k === 'household_city') {
                $data['household_city_id'] = $this->getDistrict($v);

            } elseif ($v && $k === 'household_county') {
                $data['household_county_id'] = $this->getDistrict($v);

            } elseif ($v && $k === 'living_province') {
                $data['living_province_id'] = $this->getDistrict($v);

            } elseif ($v && $k === 'living_city') {
                $data['living_city_id'] = $this->getDistrict($v);

            } elseif ($v && $k === 'living_county') {
                $data['living_county_id'] = $this->getDistrict($v);
            }
        }

        return $data;
    }

     // 缓存职位
     protected function getBrand($name)
     {
        $key = "brand_list";
        $brand = Cache::get($key, function () use ($key) {
            $brand = Brand::select('id', 'name')->get();
            Cache::put($key, $brand, now()->addMinutes(10));

            return $brand;
        });
 
        return $brand->where('name', $name)->pluck('id')->last();   
     }
 
     // 缓存部门
     protected function getDepartment($name)
     {
        $key = "department_list";
        $department = Cache::get($key, function () use ($key) {
            $department = HR\Department::select('id', 'name')->get();
            Cache::put($key, $department, now()->addMinutes(10));

            return $department;
        });
 
        return $department->where('name', $name)->pluck('id')->last();   
     }
     
     // 缓存职位
     protected function getPosition($name)
     {
        $key = "position_list";
        $position = Cache::get($key, function () use ($key) {
            $position = HR\Position::select('id', 'name')->get();
            Cache::put($key, $position, now()->addMinutes(10));

            return $position;
        });
 
        return $position->where('name', $name)->pluck('id')->last();   
     }

     // 缓存地区
     protected function getDistrict($name)
     {
        $key = "district_list";
        $district = Cache::get($key, function () use ($key) {
            $district = District::select('id', 'name')->get();
            Cache::put($key, $district, now()->addMinutes(10));

            return $district;
        });

        return $district->where('name', $name)->pluck('id')->last();
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
            'concat_name' => 'bail|required|max:10',
            'concat_tel' => 'bail|required|cn_phone',
            'concat_type' => 'bail|required|max:5',
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
                    if (!HR\Department::where('name', $name)->count()) {
                        $fail('没有部门名称为 “'.$value.'” 的部门！');
                    }
                }
            ],
            'cost_brand' => [
                'bail',
                function ($attribute, $value, $fail) {
                    $cost = HR\CostBrand::pluck('name');
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
        if (isset($value['staff_sn'])) {
            $rules = array_merge($rules, [
                'staff_sn' => 'required|exists:staff,staff_sn',
                'mobile' => [
                    'required',
                    'cn_phone',
                    Rule::unique('staff')->ignore($value['staff_sn'], 'staff_sn'),
                ],
                'id_card_number' => 'ck_identity',
                'dingtalk_number' => 'max:50',
                'realname' => 'string|max:10',
            ]);
        }

        return Validator::make($value->toArray(), $rules, $this->message());
    }

    /**
     * 统一处理验证错误信息.
     * 
     * @return array
     */
    protected function message(): array
    {
        return [
            'required' => ':attribute为必填项，不能为空。',
            'unique' => ':attribute已经存在，请重新填写。',
            'in' => ':attribute必须在【:values】中选择。',
            'max' => ':attribute不能大于 :max 个字。',
            'exists' => ':attribute填写错误。',
            'date_format' => '时间格式错误',
        ];
    }
}