<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

class AttendanceController extends Controller {

    protected $model = 'App\Models\Attendance\Attendance';

    public function showManagePage() {
        return view('hr.attendance.attendance');
    }

    public function getList(Request $request) {
        return app('Plugin')->dataTables($request, $this->model);
    }

    /**
     * 导出员工考勤数据
     */
    public function exportStaffData(Request $request) {
        $url = config('api.url.statistic.stafflist');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        $cellData = json_decode($result, true);
        $title = ['序号', '员工号', '员工名字', '出勤', '总业绩', '迟到', '请假'];

        array_unshift($cellData, $title);

        $excelName = 'statistic_' . time();

        \Excel::create($excelName, function($excel) use ($cellData) {

            $excel->sheet('score', function($sheet) use ($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth('A', 5);
            });
        })->store('xlsx', './statistic/');

        return '/statistic/' . $excelName . '.xlsx';
    }

}
