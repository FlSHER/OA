<?php

namespace App\Http\Controllers\Api\Resources;

use Validator;
use App\Models\HR\Staff;
use App\Models\I\National;
use App\Models\HR\StaffStatus;
use Illuminate\Http\Request;
use App\Services\StaffService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\HR\StaffResource;
use App\Http\Resources\HR\StaffCollection;
use App\Http\Resources\CurrentUserResource;
use Illuminate\Support\Facades\Log;

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
        $list = Staff::withApi()->when($roleId, function ($query) use ($roleId) {
            $query->whereHas('role', function ($query) use ($roleId) {
                if (is_array($roleId)) {
                    $query->whereIn('id', $roleId);
                } else {
                    $query->where('id', $roleId);
                }
            });
        })
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
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        $staff->load(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands', 'tags']);

        return new StaffResource($staff);
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
     * 🐟员工入职流程.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function entrant(Request $request)
    {
        $data = $request->input('data', []);
        $original = $this->filterData($data, [
            'id' ,'run_id', 'shop', 'recruiter', 'household', 'living',
            'relatives', 'created_at', 'updated_at', 'deleted_at',
        ]);
        if ($request->type === 'finish') {
            $params = array_merge($original, [
                'operation_type' => 'entry',
                'shop_sn' => $data['shop']['value'],
                'recruiter_sn' => $data['recruiter']['value'],
                'recruiter_name' => $data['recruiter']['text'],
                'account_active' => ($data['account_active'] == '是') ? 1 : 0,
                'relatives' => $this->makeRelatives($data['relatives']),
            ]);
            $this->entrantStaffValidator($params);
            $result = $this->staffService->create($params);

            return response()->json($result, 201);
        }

        return response()->json(['status' => 0, 'msg' => '流程验证错误'], 422);
    }

    // 转换关系数据结构
    protected function makeRelatives($original)
    {
        $relatives = [];
        foreach ((array)$original as $key => $val) {
            $relatives[$key] = [
                'relative_type' => $val['relative_type'],
                'relative_sn' => $val['relative_staff']['value'],
                'relative_name' => $val['relative_staff']['text'],
            ];
        }
        return $relatives;
    }

    /**
     * 转正流程.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function process(Request $request)
    {
        $data = $request->input('data', []);
        $original = $this->filterData($data, [
            'id' ,'run_id', 'staff', 'created_at', 'updated_at', 'deleted_at'
        ]);
        if ($request->type === 'finish') {
            $params = array_merge($original, [
                'operation_type' => 'employ',
                'staff_sn' => $data['staff']['value'],
            ]);
            $this->processValidator($params);
            $result = $this->staffService->update($params);

            return response()->json($result, 201);
        }

        return response()->json(['status' => 0, 'msg' => '流程验证错误'], 422);
    }

    /**
     * 人事变动流程.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function transfer(Request $request)
    {
        $data = $request->input('data', []);
        $original = $this->filterData($data, [
            'id' ,'run_id', 'staff', 'department_id', 'created_at', 'updated_at', 'deleted_at'
        ]);
        if ($request->type === 'finish') {
            $params = array_merge($original, [
                'operation_type' => 'transfer',
                'staff_sn' => $data['staff']['value'],
                'department_id' => $data['department_id']['value'],
            ]);
            $this->processValidator($params);
            $result = $this->staffService->update($params);

            return response()->json($result, 201);
        }

        return response()->json(['status' => 0, 'msg' => '流程验证错误'], 422);
    }

    /**
     * 离职流程.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function leave(Request $request)
    {
        $data = $request->input('data', []);
        $original = $this->filterData($data, [
            'id' ,'run_id', 'staff', 'created_at', 'updated_at', 'deleted_at'
        ]);
        if ($request->type === 'finish') {
            $params = array_merge($original, [
                'operation_type' => 'leave',
                'staff_sn' => $data['staff']['value'],
                'skip_leaving' => ($data['skip_leaving'] == '是') ? 1 : 0,
            ]);
            $this->processValidator($params);
            $result = $this->staffService->update($params);

            return response()->json($result, 201);
        }

        return response()->json(['status' => 0, 'msg' => '流程验证错误'], 422);
    }

    /**
     * 晋升流程.
     * 
     * @param  Request $request
     * @return mixed
     */
    public function promotion(Request $request)
    {
        $data = $request->input('data', []);
        $original = $this->filterData($data, [
            'id', 'run_id', 'department_id', 'staff', 'created_at', 'updated_at', 'deleted_at'
        ]);
        if ($request->type === 'finish') {
            $params = array_merge($original, [
                'operation_type' => 'position',
                'staff_sn' => $data['staff']['value'],
                'department_id' => $data['department_id']['value'],
            ]);
            Log::info($params);
            $this->processValidator($params);
            $result = $this->staffService->update($params);

            return response()->json($result, 201);
        }

        return response()->json(['status' => 0, 'msg' => '流程验证错误'], 422);
    }

    /**
     * 过滤回调数据。
     * 
     * @param  array $data
     * @param  array  $fields
     * @return array
     */
    protected function filterData($data, $fields = [])
    {
        return array_filter($data, function($k) use ($fields) {

            return !in_array($k, $fields);

        }, ARRAY_FILTER_USE_KEY);
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
            'operation_type' => 'required|in:entry,employ,transfer,leave,reinstate,active,leaving,position',
            'operation_remark' => 'max:100',
        ];
        switch ($value['operation_type']) {
            case 'employ'://转正
                $rules = array_merge($rules, [
                    'status_id' => 'required|in:2',
                ]);
                break;
            case 'leave': //离职
                $rules = array_merge($rules, [
                    'status_id' => 'required|in:-1,-2,-3,-4',
                    'skip_leaving' => 'in:0,1',
                ]);
                break;
            case 'transfer': //人事变动
                $rules = array_merge($rules, [
                    'cost_brands' => 'required|array',
                    'status_id' => 'required|in:1,2,3',
                    'brand_id' => 'required|exists:brands,id',
                    'shop_sn' => 'max:10|exists:shops,shop_sn',
                    'position_id' => 'required|exists:positions,id',
                    'department_id' => 'required|exists:departments,id',
                ]);
                break;
            case 'position': //晋升流程
                $rules = array_merge($rules, [
                    'position_id' => 'required|exists:positions,id',
                    'department_id' => 'required|exists:departments,id',
                ]);
                break;
            case 'reinstate': //再入职
                break;
        }
        $message = [
            'required' => ':attribute 为必填项，不能为空。',
            'in' => ':attribute 必须在【:values】中选择。',
            'max' => ':attribute 不能大于 :max 个字。',
            'exists' => ':attribute 填写错误。',
            'date_format' => '时间格式错误',
        ];

        return Validator::make($value, $rules, $message)->validate();
    }

    /**
     * 员工入职操作验证.
     * 
     * @param  array $value
     * @return mixed
     */
    protected function entrantStaffValidator($value)
    {
        $rules = [
            'realname' => 'bail|required|string|max:10',
            'brand_id' => 'bail|required|exists:brands,id',
            'department_id' => 'bail|required|exists:departments,id',
            'position_id' => 'bail|required|exists:positions,id',
            'mobile' => 'bail|required|unique:staff,mobile|cn_phone',
            'id_card_number' => 'bail|required|ck_identity',
            'property' => 'bail|in:0,1,2,3,4',
            'gender' => 'bail|required|in:未知,男,女',
            'education' => 'bail|exists:i_education,name',
            'national' => 'bail|exists:i_national,name',
            'politics' => 'bail|exists:i_politics,name',
            'shop_sn' => 'bail|exists:shops,shop_sn|max:10',
            'status_id' => 'bail|required|exists:staff_status,id',
            'marital_status' => 'bail|exists:i_marital_status,name',
            'household_province_id' => 'bail|exists:i_district,id',
            'household_city_id' => 'bail|exists:i_district,id',
            'household_county_id' => 'bail|exists:i_district,id',
            'living_province_id' => 'bail|exists:i_district,id',
            'living_city_id' => 'bail|exists:i_district,id',
            'living_county_id' => 'bail|exists:i_district,id',
            'household_address' => 'bail|string|max:30',
            'living_address' => 'bail|string|max:30',
            'concat_name' => 'bail|max:10',
            'concat_tel' => 'bail|cn_phone',
            'concat_type' => 'bail|max:5',
            'dingtalk_number' => 'bail|max:50',
            'account_bank' => 'bail|max:20',
            'account_name' => 'bail|max:10',
            'account_number' => 'bail|between:16,19',
            'remark' => 'bail|max:100',
            'height' => 'bail|integer|between:140,220',
            'weight' => 'bail|integer|between:30,150',
            'operate_at' => 'bail|required|date',
            'operation_remark' => 'bail|max:100',
            'relatives.*.relatives_sn' => ['required_with:relatives_type,relative_name'],
            'relatives.*.relative_stype' => ['required_with:relatives_sn,relative_name'],
            'relatives.*.relative_nsame' => ['required_with:relative_tsype,relative_sn'],
        ];
        $message = [
            'in' => ':attribute必须在【:values】中选择。',
            'max' => ':attribute不能大于 :max 个字。',
            'exists' => ':attribute填写错误。',
            'unique' => ':attribute已经存在，请重新填写。',
            'required' => ':attribute为必填项，不能为空。',
            'between' => ':attribute参数 :input 不在 :min - :max 之间。',
            'required_with' => ':attribute不能为空。',
        ];

        return Validator::make($value, $rules, $message)->validate();
    }

}
