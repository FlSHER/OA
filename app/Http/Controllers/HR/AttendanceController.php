<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Curl;

class AttendanceController extends Controller {

    public function showManagePage() {
        return view('hr.attendance.attendance');
    }

    public function getList(Request $request) {
        $url = config('api.url.attendance.getlist');
        $response = Curl::setUrl($url)->sendMessageByPost($request->all());
        return $response;
    }

    //查 店铺考勤数据
    public function showStaffInfo(Request $request) {
        // return json_encode($data); 

        $url = config('api.url.attendance.getlist');

        // $data = Curl::setUrl($url)->sendMessageByPost($request->all());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        $args['file'] = json_encode($request);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args['file']);
        $result = curl_exec($ch);

        $data['status'] = 1;
        $data['msg'] = $result;

        // return json_encode($data);
        // return json_encode($data);
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
        // curl_setopt($ch, CURLOPT_ENCODING, '');
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        // $args['file'] = json_encode($request);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $args['file']); 
        $result = curl_exec($ch);
        // echo '$result';
        curl_close($ch);
        // return '33 ';
        // $cellData = [
        //     ['学号','姓名','成绩'],
        //     ['10001','AAAAA','99'],
        //     ['10002','BBBBB','92'],
        //     ['10003','CCCCC','95'],
        //     ['10004','DDDDD','89'],
        //     ['10005','EEEEE','96'],
        // ];

        $cellData = json_decode($result, true);
        // $title = ['序号','员工号','员工名字','出勤','总业绩','迟到','请假'];
        $title = ['序号', '员工号', '员工名字', '出勤', '总业绩', '迟到', '请假'];

        array_unshift($cellData, $title);

        $excelName = 'statistic_' . time();

        \Excel::create($excelName, function($excel) use ($cellData) {

            $excel->sheet('score', function($sheet) use ($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth('A', 5);

                // $sheet->setWidth();
                // $sheet->setAutoSize(true);
            });
            // })->save('xls');
        })->store('xlsx', './statistic/');

        return '/statistic/' . $excelName . '.xlsx';
    }

}
