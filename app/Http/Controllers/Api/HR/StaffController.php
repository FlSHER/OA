<?php

namespace App\Http\Controllers\Api\HR;

use Encypt;
use Validator;
use Carbon\Carbon;
use App\Models\HR;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Resources\HR\StaffResource;
use App\Http\Requests\UpdateStaffRequest;
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
        $list = HR\Staff::when($roleId, function ($query) use ($roleId) {
            $query->whereHas('role', function ($query) use ($roleId) {
                if (is_array($roleId)) {
                    $query->whereIn('id', $roleId);
                } else {
                    $query->where('id', $roleId);
                }
            });
        })
            ->with('relative', 'position', 'department', 'brand', 'shop', 'cost_brands', 'status', 'tags')
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
            $staff = HR\Staff::query()
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
    public function update(UpdateStaffRequest $request, HR\Staff $staff)
    {
        $data = $request->all();

        $curd = $this->staffService->update($data);

        $result = $staff->where('staff_sn', $staff->staff_sn)->first();
        $result->load(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands']);

        return response()->json(new StaffResource($result), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(HR\Staff $staff)
    {
        $staff->load(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands', 'tags']);
        $staff->oa = app('Authority')->getAuthoritiesByStaffSn($staff->staff_sn);

        return new StaffResource($staff);
    }


    /**
     * 员工变动日志列表。
     *
     * @param  Staff $staff
     * @return mixed
     */
    public function logs(HR\Staff $staff)
    {
        $logs = HR\StaffLog::with('staff', 'admin')
            ->where('staff_sn', $staff->staff_sn)
            ->whereNotIn('operation_type', ['edit', 'active', 'delete'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($logs, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(HR\Staff $staff)
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
    public function resetPass(HR\Staff $staff)
    {
        $salt = mt_rand(100000, 999999);
        $newPass = Encypt::password('123456', $salt);

        $staff->password = $newPass;
        $staff->salt = $salt;
        $staff->save();

        return response()->json(['message' => '重置成功'], 201);
    }

    /**
     * 解锁员工.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return mixed
     */
    public function unlock(HR\Staff $staff)
    {
        abort_if($staff->is_active !== 0, 422, '员工未锁定，无法激活！');
        $staff->is_active = 1;
        $staff->save();

        return response()->json([
            'message' => '激活成功',
            'changes' => ['is_active' => 1],
        ], 201);
    }

    /**
     * 锁定员工.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return mixed
     */
    public function locked(HR\Staff $staff)
    {
        abort_if($staff->is_active !== 1, 422, '员工未激活，无法锁定！');
        $staff->is_active = 0;
        $staff->save();

        return response()->json([
            'message' => '锁定成功',
            'changes' => ['is_active' => 0],
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
        $operateAt = Carbon::parse($data['operate_at'])->gt(now());

        return response()->json([
            'message' => '操作成功',
            'changes' => $operateAt ? [] : $data,
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

        $data['cost_brands'] = HR\CostBrand::whereIn('id', $data['cost_brands'])->get();
        $operateAt = Carbon::parse($data['operate_at'])->gt(now());

        return response()->json([
            'message' => '操作成功',
            'changes' => $operateAt ? [] : $data,
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
        if (!$data['skip_leaving']) {
            $data['status_id'] = 0;
        }
        return response()->json([
            'message' => '离职成功',
            'changes' => $data,
        ], 201);
    }

    /**
     * 处理离职交接.
     *
     * @param  Request $request
     * @return mixed
     */
    public function leaving(Request $request)
    {
        $leaving = HR\Staff::find($request->staff_sn)->leaving;
        if ($request->has('operate_at')) {
            $leavingInfo = [
                'staff_sn' => $leaving->staff_sn,
                'status_id' => $leaving->original_status_id,
                'operate_at' => $request->operate_at,
                'operation_type' => 'leaving',
                'operation_remark' => $request->operation_remark,
            ];
            if (!empty($request->left_at)) {
                $leavingInfo['left_at'] = $request->left_at;
            }
            $request->replace($leavingInfo);
            $leaving->delete();
            $result = $this->staffService->update($request->all());
            $operateAt = Carbon::parse($request->operate_at);
            $result['changes'] = $request->all();
            return response()->json([
                'changes' => $request->all(),
                'message' => '操作成功',
                'status' => 1,
            ], 201);
        } else {
            $operatorSn = app('CurrentUser')->staff_sn;
            $operatorName = app('CurrentUser')->realname;
            $data = $request->all();
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $data[$k . '_operator_sn'] = $operatorSn;
                    $data[$k . '_operator_name'] = $operatorName;
                    $data[$k . '_operate_at'] = time();
                }
            }
            $leaving->fill($data)->save();
            return ['status' => 1, 'message' => '交接成功'];
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
            'operation_type' => 'required|in:entry,employ,transfer,leave,active,leaving',
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
                    'cost_brands' => 'array',
                    'status_id' => 'required|in:1,2,3',
                    'brand_id' => 'required|exists:brands,id',
                    'shop_sn' => 'max:10|exists:shops,shop_sn',
                    'position_id' => 'required|exists:positions,id',
                    'department_id' => 'required|exists:departments,id',
                ]);
                break;
        }

        return Validator::make($value, $rules, $this->message())->validate();
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
