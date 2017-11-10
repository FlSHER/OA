<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
/* 引入模型 start */

use App\Models\HR\Staff;
use App\Models\Department;
use App\Models\HR\StaffStatus;
use App\Models\Brand;
use App\Models\HR\Shop;
/* 引入模型 end */

use App\Contracts\OperationLog;
use App\Contracts\ExcelImport;
use App\Contracts\CURD;

class StaffController extends Controller
{

    protected $model = 'App\Models\HR\Staff';
    protected $transPath = 'fields.staff';
    protected $importCount = 0;
    protected $importFails = [];
    protected $curdService;
    protected $logService;
    protected $importService;

    public function __construct(OperationLog $logService, ExcelImport $importService, CURD $curd)
    {
        $this->logService = $logService;
        $this->importService = $importService->extension('xlsx')->trans($this->transPath);
        $this->curdService = $curd->log($this->logService)->where('staff_sn', '>', '110000');
    }

    /**
     * 显示员工列表
     * @return view
     */
    public function showManagePage()
    {
        return view('hr/staff/staff');
    }

    /**
     * dateTables获取员工信息
     * @param Request $request
     * @return json
     */
    public function getStaffList(Request $request, $withAuth = false)
    {
        $staffModel = $this->model;
        if ($withAuth) {
            $staffModel = $staffModel::visible();
        }
        return app('Plugin')->dataTables($request, $staffModel);
    }

    /**
     * 导出员工信息
     * @param Request $request
     * @return type
     */
    public function exportStaff(Request $request)
    {
        $originalColumns = $request->columns;
        array_push($originalColumns, ['data' => 'info.id_card_number']);
        $request->offsetSet('columns', $originalColumns);
        $columns = [];
        foreach ($request->columns as $v) {
            if (!in_array($v['data'], ['mobile', 'username', 'info.id_card_number']) || app('Authority')->checkAuthority(59)) {
                if (empty($v['name'])) {
                    $columns[$v['data']] = $v['data'];
                } else {
                    $columns[$v['name']] = $v['data'];
                }
            }
        }
        array_push($columns, 'info.account_number');
        array_push($columns, 'info.account_name');
        array_push($columns, 'info.education');
        $exportData = $this->getStaffList($request, true)['data'];
        $file = app('App\Contracts\ExcelExport')->setPath('hr/staff/export/')->setBaseName('员工信息')->setColumns($columns)->trans($this->transPath)->export(['sheet1' => $exportData]);
        return ['state' => 1, 'file_name' => $file];
    }

    /**
     * 显示离职对接界面
     */
    public function showLeavingPage(Request $request)
    {
        $staff = $this->getInfo($request);
        return view('hr/staff/leaving')->with(['staff' => $staff]);
    }

    /**
     * 查看员工个人信息
     * @param Request $request
     * @return type
     */
    public function showPersonalInfo(Request $request)
    {
        $staff = $this->getInfo($request);
        return view('hr/staff/staff_info')->with(['staff' => $staff]);
    }

    /**
     * 获取员工信息
     * @param Request $request
     * @return type
     */
    public function getInfo(Request $request)
    {
        $staffSn = $request->staff_sn;
        $staff = Staff::with(['info', 'relative', 'appraise'])->find($staffSn);
        foreach ($staff->info->toArray() as $k => $v) {
            $staff->{$k} = $v;
        }
        return $staff;
    }

    /**
     * 添加或编辑单个员工
     * @param \App\Http\Requests\StaffRequest $request
     * @return type
     */
    public function addOrEditStaff(Requests\StaffRequest $request)
    {
        if ($request->has('staff_sn')) {
            return $this->editStaffByOne($request);
        } else {
            return $this->addStaffByOne($request);
        }
    }

    /**
     * 添加单个员工
     * @param Request $request
     * @return view
     */
    public function addStaffByOne(Requests\StaffRequest $request)
    {
        $curd = $this->curdService->create($request->all());
        return $curd;
    }

    /**
     * 编辑单个员工
     * @param Request $request
     * @return type
     */
    public function editStaffByOne(Requests\StaffRequest $request)
    {
        $curd = $this->curdService->update($request->all());
        return $curd;
    }

    /**
     * 删除员工
     * @param Request $request
     * @return type
     */
    public function deleteStaff(Request $request)
    {
        $request->offsetSet('operation_type', 'delete');
        $request->offsetSet('operate_at', date('Y-m-d'));
        $request->offsetSet('operation_remark', '');
        $curd = $this->curdService->delete($request->all(), ['info']);
        return $curd;
    }

    /**
     * 处理离职交接
     * @param Request $request
     * @return type
     */
    public function leaving(Request $request)
    {
        $leaving = Staff::find($request->staff_sn)->leaving;
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
            return $this->curdService->update($request->all());
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
     * 批量导入
     * @param Request $request
     * @return type
     */
    public function importStaff(Request $request)
    {
        $excelData = $this->importService->load($request);
        $this->saveImportData($excelData);
        $data = [
            'count' => $this->importCount,
            'fails' => $this->importFails
        ];
        if (count($data['fails']) > 0) {
            $data['failReport'] = $this->makeFailReport($data['fails']);
        }
        return view('hr/staff/staff_import')->with($data);
    }

    /**
     * 存储导入数据
     * @param type $excelData
     */
    private function saveImportData($excelData)
    {
        foreach ($excelData as $v) {
            $fail = $v;
            $v = new Requests\StaffRequest($v);
            try {
                $operationType = $v->has('staff_sn') ? 'import_transfer' : 'import_entry';
                $v->offsetSet('operation_type', $operationType);
                $this->getInfoFromIdCardNumber($v);
                $v->setContainer(app())->setRedirector(redirect());
                $v->authorize();
                $v->validate();
                $response = $this->addOrEditStaff($v);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $response['message'] = $e->validator->errors()->all();
            } catch (\Illuminate\Http\Exception\HttpResponseException $e) {
                $response['message'] = $e->getResponse()->getContent();
            } catch (\Exception $e) {
                $response['message'] = '系统异常：' . $e->getMessage();
            }
            if (array_has($response, 'status') && $response['status'] == 1) {
                $this->importCount++;
            } else {
                $fail['reason'] = $response['message'];
                $this->importFails[] = $fail;
            }
        }
    }

    /**
     * 生成导入失败明细
     * @param type $fails
     */
    private function makeFailReport(array $fails)
    {
        return $file = app('App\Contracts\ExcelExport')->setPath('hr/staff/import_fail_report/')->setBaseName('员工导入失败明细')->trans($this->transPath)->export(['明细' => $fails]);
    }

    /**
     * 获取搜索员工弹窗
     * @param Request $request
     * @return view
     */
    public function searchResult(Request $request)
    {
        $data['realname'] = $request->name;
        $data['target'] = json_encode($request->target);
        $data['mark'] = $request->has('mark') ? '_' . $request->mark : '';
        return view('hr/search_staff')->with($data);
    }

    /**
     * 获取多对多员工关联设置弹窗
     * @param Request $request
     * @return type
     */
    public function getMultiSetModal(Request $request)
    {
        $data = $request->except(['_url', '_token']);
        $model = new $request->eloquent;
        $value = $request->primary['value'];
        $data['submitUrl'] = $request->has('submit_url') ? asset($request->submit_url) : null;
        $staff = $model->find($value)->staff;
        $data['staff'] = $staff;
        return view('hr/set_staff')->with($data);
    }

    /**
     * 设置多对多关联员工的中间表
     * @param Request $request
     * @return type
     */
    public function multiSetStaff(Request $request)
    {
        if ($request->has('staff')) {
            $staff = $request->staff;
        } else {
            $staff = [];
        }
        $model = new $request->eloquent;
        $model->find($request->primary['value'])->staff()->sync($staff);
        return ['status' => 1];
    }

    /**
     * Excel数据源Web
     * @return type
     */
    public function showDataForExcel()
    {
        $data = [
            'department' => Department::where('parent_id', 0)->orderBy('sort', 'asc')->get(),
            'status' => StaffStatus::orderBy('sort', 'asc')->get(),
            'brand' => Brand::orderBy('sort', 'asc')->get(),
            'shop' => Shop::get(),
        ];
        return view('hr.excel_info')->with($data);
    }

    /**
     * 从身份证号获取性别及生日
     * @param Request $request
     * @return type
     */
    private function getInfoFromIdCardNumber(Request $request)
    {
        $idCarkNumber = $request->info['id_card_number'];
        if (empty($request->gender['name']) && !empty($idCarkNumber)) {
            $gender = $request->gender;
            $gender['name'] = substr($idCarkNumber, 16, 1) % 2 ? '男' : '女';
            $request->offsetSet('gender', $gender);
        }
        if (empty($request->birthday) && !empty($idCarkNumber)) {
            $birthDay = substr($idCarkNumber, 6, 4) . '-' . substr($idCarkNumber, 10, 2) . '-' . substr($idCarkNumber, 12, 2);
            $request->offsetSet('birthday', $birthDay);
        }
    }

}
