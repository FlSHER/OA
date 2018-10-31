<?php

namespace App\Http\Controllers\Api\Resources;

use Validator;
use App\Models\HR\Staff;
use App\Models\HR\StaffStatus;
use Illuminate\Http\Request;
use App\Services\StaffService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\HR\StaffResource;
use App\Http\Resources\HR\StaffCollection;
use App\Http\Resources\CurrentUserResource;

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
            $staff = Staff::withApi()->orderBy('staff_sn', 'desc')->first();

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
            $result = $staff->withApi()->where('staff_sn', $staff->staff_sn)->first();

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
     * 获取员工状态列表.
     * 
     * @return array
     */
    public function status()
    {
        $list = StaffStatus::query()
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();

        return response()->json($list, 200);
    }

    /**
     * 获取员工属性.
     * 
     * @return array
     */
    public function property()
    {
        return [
            ['1' => '108将'],
            ['2' => '36天罡'],
            ['3' => '24金刚'],
            ['4' => '18罗汉'],
        ];
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
        $data = $request->all();
        if ($data['type'] == 'finish') {
            $this->processValidator($data['data']);
            $this->staffService->update($data['data']);

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
                'skip_leaving' => 'in:0',
            ]);
        } elseif ($type == 'leaving') { // 已离职
            $rules = array_merge($rules, [
                'status_id' => 'required|in:-1,-2,-3,-4',
                'skip_leaving' => 'in:1',
            ]);
        } elseif ($type == 'reinstate') { // 再入职

        }

        return Validator::make($value, $rules, $message)->validate();
    }

}
