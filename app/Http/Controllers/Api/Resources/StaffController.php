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
     * 🐟员工入职操作(工作流).
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function entrant(Request $request)
    {
        $data = $request->input('data', []);
        Log::info($data);
        if ($request->type == 'finish') {
            $params = array_merge($data, [
                'operation_type' => 'entry',
                'shop_sn' => $data['shop']['value'],
                'recruiter_sn' => $data['recruiter']['value'],
                'recruiter_name' => $data['recruiter']['text'],
                'account_active' => ($data['account_active'] == '是') ? 1 : 0,
                'relatives' => $this->makeRelatives($data['relatives']),
            ]);
            Log::info($params);
            // $this->entrantStaffValidator($params);
        }

        return response()->json(['status' => 1, 'msg' => 'ok'], 201);
    }

    protected function makeRelatives($original)
    {
        $relatives = [];
        foreach ((array)$original as $key => $val) {
            $relative[$key] = [
                'relative_type' => $val['relative_type'],
                'relative_sn' => $val['relative_staff']['value'],
                'relative_name' => $val['relative_staff']['text'],
            ];
        }
        return $relatives;
    }


    /**
     * 转正操作(工作流).
     * 
     * @param  Request $request
     * @return mixed
     */
    public function process(Request $request)
    {
        $data = $request->input('data', []);
        if ($request->type == 'finish') {
            $params = array_merge($data, [
                'operation_type' => 'employ',
                'staff_sn' => $data['staff']['value'],
            ]);
            $this->processValidator($params);
            $this->staffService->update($params);

            return response()->json(['message' => '转正成功'], 201);
        }
    }

    /**
     * 人事变动操作(工作流).
     * 
     * @param  Request $request
     * @return mixed
     */
    public function transfer(Request $request)
    {
        $data = $request->input('data', []);
        if ($request->type == 'finish') {
            $params = array_merge($data, [
                'operation_type' => 'transfer',
                'staff_sn' => $data['staff']['value'],
            ]);
            $this->processValidator($params);
            $this->staffService->update($params);

            return response()->json(['message' => '操作成功'], 201);
        }
    }

    /**
     * 离职操作(工作流).
     * 
     * @param  Request $request
     * @return mixed
     */
    public function leave(Request $request)
    {
        $data = $request->input('data', []);
        if ($request->type == 'finish') {
            $params = array_merge($data, [
                'operation_type' => 'leave',
                'staff_sn' => $data['staff']['value'],
            ]);
            $this->processValidator($params);
            $this->staffService->update($params);

            return response()->json(['message' => '操作成功'], 201);
        }
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
                'cost_brands' => ['required|array'],
                'position_id' => 'required|exists:positions,id',

            ]);
        } elseif ($type == 'leave') { // 离职中
            $rules = array_merge($rules, [
                'status_id' => 'required|in:-1,-2,-3,-4',
                'skip_leaving' => 'in:0,1',
            ]);
        } elseif ($type == 'reinstate') { // 再入职

        }

        return Validator::make($value, $rules, $message)->validate();
    }

    /**
     * 员工入转操作验证.
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
            'in' => ':attribute 必须在【:values】中选择。',
            'max' => ':attribute 不能大于 :max 个字。',
            'exists' => ':attribute 填写错误。',
            'unique' => ':attribute 已经存在，请重新填写。',
            'required' => ':attribute 为必填项，不能为空。',
            'between' => ':attribute 值 :input 不在 :min - :max 之间。',
            'required_with' => ':attribute 不能为空。',
        ];
        return Validator::make($value, $rules, $message)->validate();
    }

}
