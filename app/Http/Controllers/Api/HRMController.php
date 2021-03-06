<?php

namespace App\Http\Controllers\Api;

use ApiResponse;
use App\Http\Requests;
use App\Contracts\CURD;
use App\Models\HR\Shop;
use App\Models\HR\Staff;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Contracts\OperationLog;
use App\Http\Controllers\Controller;

class HRMController extends Controller
{

    protected $start;
    protected $length;
    protected $search;
    protected $model; //模型
    protected $availableColumns; //全部字段
    protected $curdService;
    protected $logService;

    public function __construct(OperationLog $logService, CURD $curd)
    {
        $request = request();
        $this->start = $request->has('start') ? $request->start : 0;
        $this->length = $request->has('length') ? $request->length : 0;
        $this->logService = $logService;
        $this->curdService = $curd->log($this->logService);
    }

    public function getCurrentUserInfo(Request $request)
    {
        $currentUser = Staff::api()->find(app('CurrentUser')->staff_sn);
        if (!empty($currentUser->department)) {
            $currentUser->department->setAttribute('parentIds', $currentUser->department->parentIds);
            $currentUser->department->setAttribute('childrenIds', $currentUser->department->childrenIds);
        }
        return ApiResponse::makeSuccessResponse($currentUser, 200);
    }

    /**
     * 获取员工信息
     * @param Request $request
     * @return type
     */
    public function getUserInfo(Request $request)
    {
        $only = $request->has('only') ? $request->only : false;
        return $this->getInfo($request, 'App\Models\HR\Staff', $only);
    }

    /**
     * 获取部门信息
     * @param Request $request
     * @return type
     */
    public function getDepartmentInfo(Request $request)
    {
        return $this->getInfo($request, 'App\Models\Department');
    }

    /**
     * 获取店铺信息
     * @param Request $request
     * @return type
     */
    public function getShopInfo(Request $request)
    {
        return $this->getInfo($request, 'App\Models\HR\Shop');
    }

    public function setShopInfo(Request $request)
    {
        $shop = Shop::where('shop_sn', $request->shop_sn)->first();
        $updateSuccess = $shop->update($request->input());
        if ($updateSuccess) {
            return app('ApiResponse')->makeSuccessResponse('修改成功', 200);
        } else {
            return app('ApiResponse')->makeErrorResponse('修改失败', 500);
        }
    }

    /**
     * 获取品牌信息
     * @param Request $request
     * @return type
     */
    public function getBrandInfo(Request $request)
    {
        return $this->getInfo($request, 'App\Models\Brand');
    }

    /**
     * 获取职位信息
     * @param Request $request
     * @return type
     */
    public function getPositionInfo(Request $request)
    {
        return $this->getInfo($request, 'App\Models\Position');
    }

    /**
     * 修改员工信息
     * @param Request $request
     * @return mixed
     */
    public function changeStaffInfo(Request $request)
    {
        return $this->changeInfo($request->input(), 'App\Models\HR\Staff', $request->staff_sn);
    }

    /**
     * 删除员工操作.
     *
     * @param Request $request
     * @return void
     */
    public function deleteStaff(Request $request, Staff $staff)
    {
        $request->offsetSet('staff_sn', $staff->staff_sn);
        $request->offsetSet('operation_type', 'delete');
        $request->offsetSet('operate_at', date('Y-m-d'));
        $request->offsetSet('operation_remark', '');
        $curd = $this->curdService->delete($staff, ['info']);
        return $curd;
    }

    /**
     * 修改店铺信息
     * @param Request $request
     * @return mixed
     */
    public function changeShopInfo(Request $request)
    {
        return $this->changeInfo($request->input(), 'App\Models\HR\Shop', $request->shop_sn);
    }

    /**
     * 修改信息
     * @param $data
     * @param $model
     * @param null $primaryKey
     * @return mixed
     */
    protected function changeInfo($data, $model, $primaryKey = null)
    {
        try {
            $primaryKey = empty($primaryKey) ? request()->input('id') : $primaryKey;
            if (!is_array($primaryKey)) {
                $primaryKey = [$primaryKey];
            }
            foreach ($primaryKey as $key) {
                $model::find($key)->fill($data)->save();
            }
            return app('ApiResponse')->makeSuccessResponse('修改成功', 200);
        } catch (\Exception $e) {
            return app('ApiResponse')->makeErrorResponse($e->getMessage(), 501, $e->getStatusCode());
        }
    }

    /**
     * 获取模型信息
     * @param Request $request
     * @param type $model
     * @return type
     */
    private function getInfo(Request $request, $model, $only = false)
    {
        try {
            $this->modelInit($request, $model);
            $this->addCondition($request);

            $recordsTotal = $model::api()->count();
            $recordsFiltered = $this->model->count();
//            if ($recordsFiltered > 500 && $this->length == 0) {
//                abort(500, '获取数据过多（' . $recordsFiltered . '条），请添加分页或筛选条件');
//            }

            $data = $this->model->when($this->length > 0, function ($query) {
                return $query->skip($this->start)->take($this->length);
            })->get();
            if ($only) {
                $data = $data[0];
            }
            return app('ApiResponse')->makeSuccessResponse($data, 200, ['recordsFiltered' => $recordsFiltered, 'recordsTotal' => $recordsTotal]);
        } catch (\Exception $e) {
            return app('ApiResponse')->makeErrorResponse($e->getMessage(), 501, $e->getStatusCode());
        }
    }

    /**
     * 模型初始化
     * @param type $request
     * @param type $model
     */
    private function modelInit($request, $model)
    {
        $this->model = $model::api();
        if ($request->has('auth_limit') && $request->auth_limit) {
            $this->model = $this->model->visible();
        }
        $this->getColumns($model);
    }

    /**
     * 根据参数生成可选部门id
     * @param Request $request
     * @return type
     */
    private function makeRealDepartmentId(Request $request)
    {
        $departmentId = is_array($request->department_id) ? $request->department_id : explode(',', $request->department_id);
        if ($request->children) {
            $departments = Department::whereIn('id', $request->department_id)->get();
            foreach ($departments as $v) {
                $departmentId = array_collapse([$departmentId, $v->childrenIds]);
            }
        }
        $departmentId = count($departmentId) == 1 ? $departmentId[0] : $departmentId;
        $request->offsetSet('department_id', $departmentId);
    }

    /**
     * 根据参数添加筛选条件
     * @param Request $request
     */
    private function addCondition(Request $request)
    {
        if (!empty($request->department_id)) {
            $this->makeRealDepartmentId($request);
        }
        $this->search = $request->has('search') ? $request->search : [];
        foreach ($request->all() as $key => $value) {
            $this->trunParamsToSql($key, $value);
        }
        if (!empty($this->search)) {
            $where = $this->changeSearchConditionToSql($this->search);
            $this->model = $this->model->where($where);
        }
    }

    /**
     * 获取所有字段
     * @param \App\Http\Controllers\Api\model $model
     */
    private function getColumns($model)
    {
        $model = new $model;
        $this->availableColumns = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
    }

    /**
     * 处理传入参数
     * @param type $key
     * @param type $value
     */
    private function trunParamsToSql($key, $value)
    {
        $columns = $this->availableColumns;
        if (is_numeric($key) && is_array($value)) {
            $this->model->where([$value]);
        } elseif (in_array($key, $columns) && $value != '') {
            $this->model = is_array($value) ? $this->model->whereIn($key, $value) : $this->model->where($key, $value);
        }
    }

    /**
     * 将搜索条件拼接为sql
     * @param type $search
     * @return type
     */
    private function changeSearchConditionToSql($search)
    {
        $where = [];
        foreach ($search as $k => $v) {
            if ($v != '') {
                $where[] = [$k, 'like', "%$v%"];
            }
        }
        return $where;
    }

}
