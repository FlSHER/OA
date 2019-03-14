<?php

namespace App\Http\Controllers\Api\HR;

use Validator;
use App\Models\HR;
use App\Models\Department;
use App\Models\I\District;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\StaffService;
use App\Support\ParserIdentity;

class ExcelStaffController extends Controller
{
    /**
     * 导入错误信息.
     *
     * @var array
     */
    protected $errors;

    /**
     * 上传字段中英文映射.
     *
     * @var array
     */
    protected $staffWithMap;
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * 批量操作员工.
     *
     * @param  Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        $type = $request->input('type');

        return app()->call([
            $this, camel_case('import_' . $type)
        ]);
    }

    /**
     * 批量导入.
     *
     * @param Request $request
     * @return void
     */
    public function importAdd(Request $request)
    {
        $data = array_filter($request->input('data', []));
        $formatData = $this->combineImportData($request);
        foreach ($formatData as $key => $value) {
            $this->makeValidator($value, $this->addRules($value));
            if (empty($this->errors)) {
                $makeVal = $this->makeFillStaff($value);
                $makeVal['operation_type'] = 'import_entry';
                $makeVal['operation_remark'] = 'Excel批量导入';
                $this->staffService->create($makeVal);
                $body[] = true;
            } else {
                $error['row'] = $key + 1;
                $error['rowData'] = $data[$key];
                $error['message'] = $this->errors;
                $errors[] = $error;
                continue;
            }
        }
        $response['data'] = !empty($body) ? $body : [];
        $response['errors'] = !empty($errors) ? $errors : [];
        $response['headers'] = !empty($data[0]) ? $data[0] : [];
        empty($response['errors']) && $this->cacheClear();

        return response()->json($response, 201);
    }

    /**
     * 批量更新.
     *
     * @param Request $request
     * @return void
     */
    public function importEdit(Request $request)
    {
        $data = array_filter($request->input('data', []));
        $formatData = $this->combineImportData($request);
        foreach ($formatData as $key => $value) {
            $this->makeValidator($value, $this->editRules($value));
            if (empty($this->errors)) {
                $makeVal = $this->makeFillStaff($value);
                $makeVal['operation_type'] = 'edit';
                $makeVal['operation_remark'] = 'Excel批量编辑';
                $this->staffService->update($makeVal);
                $body[] = true;
            } else {
                $error['row'] = $key + 1;
                $error['rowData'] = $data[$key];
                $error['message'] = $this->errors;
                $errors[] = $error;
                continue;
            }
        }
        $response['data'] = !empty($body) ? $body : [];
        $response['errors'] = !empty($errors) ? $errors : [];
        $response['headers'] = !empty($data[0]) ? $data[0] : [];
        empty($response['errors']) && $this->cacheClear();

        return response()->json($response, 201);
    }

    /**
     * 批量变动.
     *
     * @param Request $request
     * @return void
     */
    public function importTransfer(Request $request)
    {
        $data = array_filter($request->input('data', []));
        $formatData = $this->combineImportData($request);
        foreach ($formatData as $key => $value) {
            $this->makeValidator($value, $this->transferRules($value));
            if (empty($this->errors)) {
                $makeVal = $this->makeFillStaff($value);
                $makeVal['operation_type'] = 'import_transfer';
                $makeVal['operation_remark'] = 'Excel批量变动';
                $this->staffService->update($makeVal);
                $body[] = true;
            } else {
                $error['row'] = $key + 1;
                $error['rowData'] = $data[$key];
                $error['message'] = $this->errors;
                $errors[] = $error;
                continue;
            }
        }
        $response['data'] = !empty($body) ? $body : [];
        $response['errors'] = !empty($errors) ? $errors : [];
        $response['headers'] = !empty($data[0]) ? $data[0] : [];
        empty($response['errors']) && $this->cacheClear();

        return response()->json($response, 201);
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
        $data = array_filter($request->input('data', []), function ($item) {
            return !empty($item) && !empty($item[0]);
        });
        $cols = $request->input('cols', []);
        if (count($data) <= 1) return false;

        $this->staffWithMap = collect($cols)->combine($data[0]);
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
        // 给默认执行时间
        $value['operate_at'] = !empty($value['operate_at']) ? $value['operate_at'] : now()->toDateString();
        $data = [];
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

            } elseif ($v && $k === 'brand') {
                $data['brand_id'] = $this->getBrand($v);

            } elseif ($v && $k === 'department') {
                $data['department_id'] = $this->getDepartment($v);

            } elseif ($v && $k === 'position') {
                $data['position_id'] = $this->getPosition($v);

            } elseif ($v && $k === 'status') {
                $status = ['试用期' => 1, '在职' => 2, '停薪留职' => 3];
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

            } elseif ($v && $k === 'hired_at') {
                if (is_numeric($v)) {
                    $data['hired_at'] = date('Y-m-d', (($v - 25569) * 24 * 3600));
                } else {
                    $data['hired_at'] = $v;
                }
            } elseif ($v && $k === 'operate_at') {
                if (is_numeric($v)) {
                    $data['operate_at'] = date('Y-m-d', (($v - 25569) * 24 * 3600));
                } else {
                    $data['operate_at'] = $v;
                }
            }
        }

        return $data;
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

            // merge 高级权限数据
            if ($hasAuth && (($checkBrand && $checkDepart) || $item->status_id < 0)) {

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
        $property = ['无', '109将', '36天罡', '24金刚', '18罗汉'];

        $birthday = '';
        $year = (int)substr($item->id_card_number, 6, 4);
        $month = (int)substr($item->id_card_number, 10, 2);
        $day = (int)substr($item->id_card_number, 12, 2);
        if (checkdate($month, $day, $year)) {
            $birthday = sprintf('%s-%s-%s', $year, $month, $day);
        }
        return [
            'staff_sn' => $item->staff_sn,
            'realname' => $item->realname,
            'gender' => $item->gender,
            'brand' => $item->brand->name,
            'cost_brand' => $item->cost_brands->implode('name', '/'),
            'shop_sn' => $item->shop_sn,
            'shop_name' => $item->shop->name ?? '',
            'department' => $item->department->full_name,
            'position' => $item->position->name,
            'position_level' => $item->position->level,
            'status' => $item->status->name,
            'birthday' => $birthday,
            'hired_at' => $item->hired_at,
            'employed_at' => $item->employed_at,
            'left_at' => $item->left_at,
            'property' => $property[$item->property],
            'account_number' => $item->account_number,
            'account_name' => $item->account_name,
            'account_bank' => $item->account_bank,
            'job_source' => $item->job_source,
            'birthday' => $parser->isValidate() ? $parser->birthday() : '',
            'position_pull' => $transfer->isNotEmpty() ? $transfer[0] : '',
            'position_push' => $transfer->isNotEmpty() ? $transfer[1] : '',
            'position_push_at' => $transfer->isNotEmpty() ? $transfer[2] : '',
            'recruiter_name' => $item->recruiter_name,
            'remark' => $item->remark,
        ];
    }

    /**
     * 获取员工最后一次职位变动记录.
     * 
     * @param  \App\Models\HR\Staff $item
     * 
     * @return Collection
     */
    public function getLastChange($item)
    {
        $transfer = collect([]);
        $lastLog = $item->change_log()->where('operation_type', 'transfer')->latest()->first();

        if (!empty($lastLog->changes) && is_array($lastLog->changes)) {
            $transfer = collect($lastLog->changes)->filter(function ($v, $k) {
                return ($k === '职位' || $k === 'position');
            })->collapse();
            $transfer->isNotEmpty() && $transfer->push($lastLog->created_at->toDateString());
        }

        return $transfer;
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
        $makeHouseholdCity = implode('-', [
            $temp[$item->household_province_id] ?? '',
            $temp[$item->household_city_id] ?? '',
            $temp[$item->household_county_id] ?? '',
        ]);
        $makeLivingCity = implode('-', [
            $temp[$item->living_province_id] ?? '',
            $temp[$item->living_city_id] ?? '',
            $temp[$item->living_county_id] ?? '',
        ]);

        return [
            'mobile' => $item->mobile,
            'id_card_number' => $item->id_card_number,
            'national' => $item->national,
            'wechat_number' => $item->wechat_number,
            'education' => $item->education,
            'politics' => $item->politics,
            'marital_status' => $item->marital_status,
            'height' => $item->height,
            'weight' => $item->weight,
            'household_city' => $makeHouseholdCity . ' ' . $item->household_address,
            'living_city' => $makeLivingCity . ' ' . $item->living_address,
            'native_place' => $item->native_place,
            'concat_name' => $item->concat_name,
            'concat_tel' => $item->concat_tel,
            'concat_type' => $item->concat_type,
        ];
    }

    // 缓存职位
    protected function getBrand($name)
    {
        $key = "brand_list";
        $brand = Cache::get($key, function () use ($key) {
            $brand = HR\Brand::select('id', 'name')->get();
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
            $department = Department::select('id', 'name', 'full_name')->get();
            Cache::put($key, $department, now()->addMinutes(10));

            return $department;
        });

        return $department->where('full_name', $name)->pluck('id')->last();
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

    protected function cacheClear()
    {
        Cache::forget('brand_list');
        Cache::forget('position_list');
        Cache::forget('district_list');
        Cache::forget('department_list');
    }

    /**
     * 批量入职 rules.
     *
     * @return void
     */
    protected function addRules($item)
    {
        return array_merge($this->commonRules($item), [
            'realname' => 'required|string|max:10',
            'concat_name' => 'required|between:2,10',
            'concat_tel' => 'required|cn_phone',
            'concat_type' => 'required|max:5',
            'mobile' => ['required', 'cn_phone', unique_validator('staff', false)],
            'wechat_number' => ['between:6,20', unique_validator('staff', false)],
            'id_card_number' => ['required', 'ck_identity', unique_validator('staff', false)],
            'dingtalk_number' => ['max:50', unique_validator('staff', false)],
            'status' => [
                'required',
                Rule::exists('staff_status', 'name')->where(function ($query) {
                    $query->where('id', '>', 0);
                }),
            ],
        ]);
    }

    /**
     * 批量编辑信息 rules.
     *
     * @param array $item
     * @return void
     */
    protected function editRules($item)
    {
        $uniqueRule = Rule::unique('staff')->ignore($item['staff_sn'], 'staff_sn')->where(function ($query) {
            $query->whereNull('deleted_at');
        });
        return array_merge($this->commonRules($item), [
            'staff_sn' => 'required|exists:staff,staff_sn,deleted_at,NULL',
            'realname' => 'string|max:10',
            'concat_name' => 'between:2,10',
            'concat_tel' => 'cn_phone',
            'concat_type' => 'rmax:5',
            'mobile' => ['cn_phone', $uniqueRule],
            'wechat_number' => ['between:6,20', $uniqueRule],
            'id_card_number' => ['ck_identity', $uniqueRule],
            'dingtalk_number' => ['max:50', $uniqueRule],
            'status' => [
                Rule::exists('staff_status', 'name')->where(function ($query) {
                    $query->where('id', '>', 0);
                }),
            ],
        ]);
    }

    /**
     * 批量变动信息 rules.
     *
     * @param array $item
     * @return void
     */
    protected function transferRules($item)
    {
        return array_merge($this->commonRules($item), [
            'staff_sn' => 'required|exists:staff,staff_sn,deleted_at,NULL',
            'status' => [
                Rule::exists('staff_status', 'name')->where(function ($query) {
                    $query->where('id', '>', 0);
                }),
            ],
        ]);
    }

    /**
     * 批量(入职|编辑|变动)通用部分规则.
     *
     * @param array $item
     * @return rules
     */
    protected function commonRules($item)
    {
        return [
            'gender' => 'in:未知,男,女',
            'national' => 'exists:i_national,name',
            'politics' => 'exists:i_politics,name',
            'education' => 'exists:i_education,name',
            'marital_status' => 'exists:i_marital_status,name',
            'household_province' => 'exists:i_district,name',
            'household_county' => 'exists:i_district,name',
            'household_city' => 'exists:i_district,name',
            'living_province' => 'exists:i_district,name',
            'living_county' => 'exists:i_district,name',
            'living_city' => 'exists:i_district,name',
            'household_address' => 'string|max:30',
            'living_address' => 'string|max:30',
            'account_bank' => 'max:20',
            'account_name' => 'max:10',
            'account_number' => 'between:16,19',
            'remark' => 'max:100',
            'height' => 'integer|between:140,220',
            'weight' => 'integer|between:30,150',
            'shop_sn' => 'exists:shops,shop_sn,deleted_at,NULL|max:10',
            'position' => 'exists:positions,name,deleted_at,NULL',
            'department' => 'max:100|exists:departments,full_name,deleted_at,NULL',
            'brand' => 'exists:brands,name,deleted_at,NULL',
            'cost_brand' => [
                'required_with:brand',
                function ($attribute, $content, $fail) use ($item) {
                    $brandName = HR\CostBrand::pluck('name');
                    $costBrands = array_filter(explode('/', $content));
                    $brand = collect($costBrands)->map(function ($brand) use ($brandName) {
                        if (!$brandName->contains($brand)) {
                            return $brand;
                        }
                    })->filter();
                    if ($brand->isNotEmpty()) {
                        $fail("“{$brand->implode('，')}” 费用品牌不存在！");
                    }

                    // 验证品牌/费用品牌的关联性
                    $brand_id = $this->getBrand($item['brand']);
                    $brands = HR\CostBrand::with('brands')->whereIn('name', $costBrands)->get();
                    $brand = $brands->map(function ($item) use ($brand_id) {
                        if (!$item->brands->contains($brand_id)) {
                            return $item->name;
                        }
                    })->filter();
                    if ($brand->isNotEmpty()) {
                        $fail("“{$brand->implode('，')}” 不是所属品牌的费用品牌");
                    }
                }
            ],
        ];
    }

    /**
     * 验证导入字段.
     *
     * @param  行数据 $item
     * @param  验证规则 $rules
     *
     * @return void
     */
    protected function makeValidator($item, $rules)
    {
        $item = $item->toArray();
        $validator = Validator::make($item, $rules, [
            'in' => ':attribute必须在【:values】中选择。',
            'max' => ':attribute不能大于 :max 个字。',
            'exists' => ':attribute填写错误。',
            'unique' => ':attribute已经存在，请重新填写。',
            'required' => ':attribute为必填项，不能为空。',
            'between' => ':attribute参数 :input 不在 :min - :max 之间。',
            'required_with' => ':attribute不能为空。',
            'date_format' => '时间格式错误',
        ]);
        if ($this->staffWithMap) {
            $this->errors = [];
            foreach ($validator->errors()->getMessages() as $key => $error) {
                $this->errors[$this->staffWithMap[$key]] = $error;
            }
        }
    }
}