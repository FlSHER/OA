<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Contracts\OperationLog;
use App\Contracts\ExcelImport;
use App\Contracts\CURD;

class TransferController extends Controller
{

    protected $model = 'App\Models\HR\Attendance\StaffTransfer';
    protected $transPath = 'fields.transfer';
    protected $curdService;
    protected $logService;
    protected $importService;

    public function __construct(OperationLog $logService, ExcelImport $importService, CURD $curd)
    {
        $this->logService = $logService;
        $this->importService = $importService->extension('xlsx')->trans($this->transPath);
        $this->curdService = $curd;
    }

    public function showManagePage()
    {
        return view('hr.attendance.transfer');
    }

    public function getList(Request $request)
    {
        $model = $this->model;
        return app('Plugin')->dataTables($request, $model::visible());
    }

    public function getInfo(Request $request)
    {
        $id = $request->id;
        $model = $this->model;
        $info = $model::with(['tag'])->find($id);
        return $info;
    }

    public function addOrEdit(Request $request)
    {
        $this->validate($request, $this->makeValidator($request), [], trans($this->transPath));
        if ($request->has('id')) {
            return $this->editByOne($request);
        } else {
            return $this->addByOne($request);
        }
    }

    public function addByOne(Request $request)
    {
        $model = $this->model;
        $count = $model::where($request->only(['staff_sn', 'leaving_date', 'arriving_shop_sn']))
            ->where('status', '>=', 0)->count();
        if ($count > 0) {
            $response['message'] = '调动已存在';
        } else {
            $request->offsetSet('maker_sn', app('CurrentUser')->staff_sn);
            $request->offsetSet('maker_name', app('CurrentUser')->realname);
            $data = $request->all();
            $response = $this->curdService->create($data);
        }
        return $response;
    }

    public function editByOne(Request $request)
    {
        $data = $request->all();
        $response = $this->curdService->update($data);
        return $response;
    }

    public function cancel(Request $request)
    {
        $data = [
            'id' => $request->id,
            'status' => -1,
        ];
        $response = $this->curdService->update($data);
        return $response;
    }

    public function import(Request $request)
    {
        $excelData = $this->importService->load($request);
        $success = 0;
        $fails = [];
        foreach ($excelData as $v) {
            $fail = $v;
            try {
                $subRequest = new Request($v);
                $response = $this->addOrEdit($subRequest);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $response['message'] = $e->validator->errors()->all();
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }
            if (array_has($response, 'status') && $response['status'] == 1) {
                $success++;
            } else {
                $fail['realname'] = $fail['staff_name'];
                $fail['reason'] = $response['message'];
                $fails[] = $fail;
            }
        }
        $data = [
            'count' => $success,
            'fails' => $fails
        ];
        if (count($fails) > 0) {
            $data['failReport'] = app('App\Contracts\ExcelExport')->setPath('/hr/transfer/import_fail_report/')->setBaseName('店铺人员调动导入失败明细')->trans($this->transPath)->export(['明细' => $fails]);
        }
        return view('hr/staff/staff_import')->with($data);
    }

    public function export(Request $request)
    {
        $exportData = $this->getList($request)['data'];
        $columns = [];
        foreach ($request->columns as $v) {
            if (empty($v['name'])) {
                $columns[$v['data']] = $v['data'];
            } else {
                $columns[$v['name']] = $v['data'];
            }
        }
        $file = app('App\Contracts\ExcelExport')->setPath('hr/transfer/export/')->setBaseName('店铺人员调动')->setColumns($columns)->trans($this->transPath)->export(['sheet1' => $exportData]);
        return ['state' => 1, 'file_name' => $file];
    }

    protected function makeValidator($input)
    {
        $validator = [
            'leaving_shop_sn' => ['exists:shops,shop_sn,deleted_at,NULL'],
            'arriving_shop_sn' => ['required', 'exists:shops,shop_sn,deleted_at,NULL'],
            'arriving_shop_duty_id' => ['exists:attendance.shop_duty,id'],
            'leaving_date' => ['required', 'date', 'after:2000-1-1'],
            'remark' => ['max:200'],
        ];
        if (empty($input['id'])) {
            $validator['staff_sn'] = ['required_with:staff_name'];
            $validator['staff_name'] = ['required', 'exists:staff,realname,staff_sn,' . $input['staff_sn']];
        }
        return $validator;
    }

}
