<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;

class LeaveController extends Controller {

    protected $model = 'App\Models\HR\Attendance\Vacate';

    public function showManagePage() {
        return view('hr.attendance.leave');
    }

    public function getList(Request $request) {
        return app('Plugin')->dataTables($request, $this->model);
    }

    public function getInfo(Request $request) {
        $id = $request->id;
        $model = $this->model;
        return $model::find($id);
    }

    public function excelHandel(Request $request) {
        $fieldArr = [
            '完成' => 1,
            '同意' => 1,
            '拒绝' => 0,
        ];

        $filePath = $file = $_FILES['wenjian']['tmp_name'];
        // $filePath = '/public/a.xls';

        $exlHandle = Excel::load($filePath)->get()->toArray();
        $newArr = [];
        foreach ($exlHandle as $key => $value) {
            if ($value['审批状态'] == '已撤销')
                break;
            array_push($newArr, [
                'sponsor' => $value['发起人工号'],
                'sponsor_name' => $value['发起人姓名'],
                'title' => $value['标题'],
                'subject_status' => $fieldArr[$value['审批状态']],
                'subject_result' => $fieldArr[$value['审批结果']],
                'subject_name' => $value['历史审批人姓名'],
                'department' => $value['发起人部门'],
                'holiday_type' => isset($value['请假类型']) ? $value['请假类型'] : '',
                'start_time' => $value['开始时间'],
                'end_time' => $value['结束时间'],
                // 'holiday_detail'=>$value['请假事由'],
                'holiday_detail' => isset($value['请假事由']) ? $value['请假事由'] : '',
                    // 'subject_result'=>$value['审批结果']
            ]);
        }

        $url = config('api.url.holiday.imports');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        $args['file'] = json_encode($newArr);
        // $args['file']= 'asda';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $result = curl_exec($ch);

        $data['status'] = 1;
        $data['msg'] = $result;

        return json_encode($data);
    }

}
