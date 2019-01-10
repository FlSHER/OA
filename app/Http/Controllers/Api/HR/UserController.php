<?php

namespace App\Http\Controllers\Api\HR;

use Encypt;
use Validator;
use Carbon\Carbon;
use App\Models\HR;
use Illuminate\Http\Request;
use App\Services\StaffService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessRequest;
use App\Http\Resources\HR\StaffResource;
use App\Http\Resources\HR\StaffCollection;
use App\Services\Dingtalk\Server\UserService;
use App\Http\Requests\StoreStaffRequest as StaffRequest;

class UserController extends Controller
{
    protected $userService;
    protected $staffService;

    public function __construct(StaffService $staffService, UserService $userService)
    {   
        $this->userService = $userService;
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
        $list = HR\Staff::withApi()
            ->when($roleId, function ($query) use ($roleId) {
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
    public function store(StaffRequest $request)
    {
        $data = $request->all();
        $curd = $this->staffService->create($data);
        if ($curd['status'] === 1) {
            $staff = HR\Staff::withApi()->orderBy('staff_sn', 'desc')->first();
            $params = $this->makeFillData($data);
            $params['hiredDate'] = strtotime($staff->hired_at);
            $result = $this->userService->create($params);

            if ($result['errcode'] !== 0) {
                $staff->delete();
                return response()->json(['message' => $result['errmsg']], 500);
            }

        } elseif ($curd['status'] === -1) {
            return response()->json($curd, 201);
        }
        
        return response()->json(new StaffResource($staff), 201);
    }

    /**
     * 获取钉钉部门ID.
     * 
     * @param  int $dept_id
     */
    public function getSourceId($dept_id)
    {
        if (empty($dept_id)) { // 顶级部门
            return 1;
        }
        $sourceId = \App\Models\Department::query()
            ->where('id', $dept_id)
            ->value('source_id');

        return $sourceId;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(StaffRequest $request, HR\Staff $staff)
    {
        $data = $request->all();
        $curd = $this->staffService->update($data);
        if ($curd['status'] === 1) {
            $params = $this->makeFillData($data);
            $result = $this->userService->update($params);

            if ($result['errcode'] != 0) {
                return response()->json(['message' => $result['errmsg']], 500);
            }

        } elseif ($curd['status'] === -1) {
            return response()->json($curd);
        }

        return response()->json(new StaffResource($staff), 201);
    }

    /**
     * 组装钉钉更新字段.
     * 
     * @param  array $data
     * @return array
     */
    public function makeFillData($data)
    {
        $mapWithKey = [
            'realname' => 'name',
            'remark' => 'remark',
            'mobile' => 'mobile',
        ];
        $temp = [];
        collect($data)->map(function ($value, $key) use (&$temp, $mapWithKey) {
            if (array_has($mapWithKey, $key)) {
                $temp[$mapWithKey[$key]] = $value;

            } elseif ($key == 'department_id') {
                $sourceId = $this->getSourceId($value);
                $temp['department'] = [$sourceId];

            } elseif ($key == 'position_id') {
                $name = \App\Models\Position::find($value)->name;
                $temp['position'] = $name;

            } elseif ($key == 'staff_sn') {
                $temp['userid'] = $value;
                $temp['jobnumber'] = $value;
            }
        });

        return $temp;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(HR\Staff $staff)
    {
        $staff->load(['relative', 'position', 'department', 'brand', 'shop', 'cost_brands', 'tags', 'tags.category']);
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
            ->whereNotIn('operation_type', ['active', 'delete'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($logs, 200);
    }

    public function formatLog(HR\Staff $staff)
    {
        $logs = HR\StaffLog::with('staff', 'admin')
            ->where('staff_sn', $staff->staff_sn)
            ->whereNotIn('operation_type', ['edit', 'active', 'delete'])
            ->orderBy('id', 'asc')
            ->get();
        $format = $logs->filter(function ($item) {
            if (in_array($item->operation_type, ['人事变动', '导入变动', '职位变动']) && !empty($item->changes) ) {
                return collect($item->changes)->filter(function ($v, $k) {
                    return ($k === '职位' || $k === 'position');
                })->isNotEmpty();
            }
            return true;
        })->map(function ($item) {
            if (in_array($item->operation_type, ['人事变动', '导入变动', '职位变动'])) {
                $item->changes = collect($item->changes)->filter(function ($v, $k) {
                    return ($k === '职位' || $k === 'position');
                })->collapse();
            } else {
                $item->changes = [];
            }
            return $item;
        })->values();

        return response()->json($format, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(HR\Staff $staff)
    {
        return $staff->getConnection()->transaction(function () use ($staff) {
            $result = $this->userService->delete($staff->staff_sn);
            if ($result['errcode'] == 0) {
                $staff->cost_brands()->detach();
                $staff->delete();
            } else {
                return response()->json(['message' => $result['errmsg']], 500);
            }

            return response()->json(null, 204);
        });
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
    public function process(ProcessRequest $request)
    {
        $data = $request->all();
        $this->staffService->update($data);
        $operateAt = Carbon::parse($data['operate_at'])->gt(now());

        return response()->json([
            'message' => $operateAt ? '预约成功' : '操作成功',
            'changes' => $operateAt ? [] : $data,
        ], 201);
    }

    /**
     * 人事变动操作。
     *
     * @param  Request $request
     * @return mixed
     */
    public function transfer(ProcessRequest $request)
    {
        $data = $request->all();
        $this->staffService->update($data);
        $data['cost_brands'] = HR\CostBrand::whereIn('id', $data['cost_brands'])->get();
        $operateAt = Carbon::parse($data['operate_at'])->gt(now());

        return response()->json([
            'message' => $operateAt ? '预约成功' : '操作成功',
            'changes' => $operateAt ? [] : $data,
        ], 201);
    }

    /**
     * 离职操作。
     *
     * @param  Request $request
     * @return mixed
     */
    public function leave(ProcessRequest $request)
    {
        $data = $request->all();
        $this->staffService->update($data);
        !$data['skip_leaving'] && $data['status_id'] = 0;
        $operateAt = Carbon::parse($data['operate_at'])->gt(now());

        return response()->json([
            'message' => $operateAt ? '预约成功' : '操作成功',
            'changes' => $operateAt ? [] : $data,
        ], 201);
    }

    /**
     * 处理离职交接.
     *
     * @param  Request $request
     * @return mixed
     */
    public function leaving(ProcessRequest $request)
    {
        $data = $request->all();
        $this->staffService->update($data);
        $operateAt = Carbon::parse($data['operate_at'])->gt(now());

        return response()->json([
            'message' => $operateAt ? '预约成功' : '操作成功',
            'changes' => $operateAt ? [] : ['status_id' => -1],
        ], 201);
    }

}
