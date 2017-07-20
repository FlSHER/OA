<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Http\Request;
use Curl;
use App\Http\Controllers\Controller;
use App\Services\EncyptionService;
use Excel;

class ReimburseController extends Controller {

    /**
     * 进入报销审批界面
     * @return view
     */
    public function showReimbursePage() {
        return view('finance.reimburse');
    }

    public function checkAllReimbursements() {
        return view('finance.check_reimburse');
    }

    /**
     * 获取待处理报销单
     * @return json
     */
    public function getHandleList(Request $request) {
        $message = $request->except(['_url']);
        $userid = session('admin')['dingding'];
        $message['userid'] = $userid;
        $url = config('api.url.reimburse.list');
        $list = Curl::setUrl($url)->sendMessageByPost($message);
        return $list;
    }

    public function getAuditedList(Request $request) {
        $message = $request->except(['_url']);
        $userid = session('admin')['dingding'];
        $message['userid'] = $userid;
        $url = config('api.url.reimburse.audited');
        $list = Curl::setUrl($url)->sendMessageByPost($message);
        return $list;
    }

    public function getAllAuditedList(Request $request) {
        $message = $request->except(['_url']);
        $message['userid'] = 'all';
        $url = config('api.url.reimburse.audited');
        $list = Curl::setUrl($url)->sendMessageByPost($message);
        return $list;
    }

    public function getRejectedList(Request $request) {
        $message = $request->except(['_url']);
        $userid = session('admin')['dingding'];
        $message['userid'] = $userid;
        $url = config('api.url.reimburse.rejected');
        $list = Curl::setUrl($url)->sendMessageByPost($message);
        return $list;
    }

    /**
     * 驳回
     * @param Request $request
     * @return string
     */
    public function reject(Request $request) {
        $url = config('api.url.reimburse.reject');
        $message = $request->only(['reim_id', 'remarks']);
        $message['userid'] = session('admin')['dingding'];
        $response = Curl::setUrl($url)->sendMessageByPost($message);
        return $response;
    }

    /**
     * 审核通过
     * @param Request $request
     * @return string
     */
    public function agree(Request $request) {
        $url = config('api.url.reimburse.agree');
        $message = $request->only(['reim_id', 'expenses', 'audited_cost']);
        $message['userid'] = session('admin')['dingding'];
        $response = Curl::setUrl($url)->sendMessageByPost($message);
        return $response;
    }

    public function delete(Request $request) {
        $url = config('api.url.reimburse.delete');
        $message = $request->only(['reim_id']);
        $message['userid'] = session('admin')['dingding'];
        $response = Curl::setUrl($url)->sendMessageByPost($message);
        return $response;
    }

    /**
     * 获取消费详情数据
     * @param Request $request
     * @return json
     */
    public function getExpensesByReimId(Request $request) {
        $reimId = $request->reim_id;
        $url = config('api.url.reimburse.detail');
        $message = ['reim_id' => $reimId];
        $reimbursements = Curl::setUrl($url)->sendMessageByPost($message);
        return $reimbursements;
    }

    /**
     * 打印
     * @param Request $request
     * @param EncyptionService $encypt
     * @return type
     */
    public function printReimbursement(Request $request, EncyptionService $encypt) {
        $url = config('api.url.reimburse.print');
        $message = ['reim_id' => $request->reim_id];
        $reimbursements = Curl::setUrl($url)->sendMessageByPost($message);
        if ($reimbursements['accountantid'] != session('admin')['dingding']) {
            return "错误：用户信息不匹配！";
        }
        if ($reimbursements['status_id'] < 5) {
            $reimbursements['cost'] = $reimbursements['approved_cost'];
        } else {
            $reimbursements['cost'] = $reimbursements['audited_cost'];
        }
        $reimbursements['costCn'] = $encypt->numberToCNY($reimbursements['cost']);
        $data = [
            'reimbursements' => $reimbursements
        ];
        return view('finance.reimburse_print', $data);
    }

    /**
     * 导出为Excel
     * @param Request $request
     * @param Excel $excel
     */
    public function exportAsExcel(Request $request) {
        if ($request->type == 'all') {
            $message = ['userid' => 'all'];
//            $username = "消费明细";
        } else {
            $userid = session('admin')['dingding'];
            $message = ['userid' => $userid];
//            $username = session('admin')['realname'];
        }
        $message['params'] = json_decode($request->params, true);
        $url = config('api.url.reimburse.expenses');
        $reimbursements = Curl::setUrl($url)->sendMessageByPost($message);
        $expenses = $reimbursements['expenses']; //明细
        $reimburse = $reimbursements['reimburse']; //报销单信息
        $payee_info = $reimbursements['payee_info']; //收款人信息
        foreach ($expenses as $k => $v) {
            $expenses[$k]['审核前费用'] = floatval($v['审核前费用']);
            $expenses[$k]['审核后费用'] = floatval($v['审核后费用']);
        }
        foreach ($reimburse as $k => $v) {
            $reimburse[$k]['审核前金额'] = floatval($v['审核前金额']);
            $reimburse[$k]['审核后金额'] = floatval($v['审核后金额']);
        }
        foreach ($payee_info as $k => $v) {
            $payee_info[$k]['金额'] = floatval($v['金额']);
        }
        Excel::create("报销单-" . date('YmdHis', time()), function($excel) use ($expenses, $reimburse, $payee_info) {
            $excel->setCreator('消费明细')
                    ->setCompany('报销单')
                    ->setCompany('收款人信息');
            //消费明细
            $excel->sheet('消费明细', function($sheet) use ($expenses) {
                $sheet->setColumnFormat(["A" => "@", "B:C" => "0.00", "D:Q" => "@"]);
                $sheet->setAutoSize(true);
                $sheet->with($expenses);
                $sheet->row('1', function($row) {
                    $row->setBackground('#26651e');
                    $row->setFontColor('#ffffff');
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });
                $sheet->freezeFirstRow();
            });
            //报销单信息
            $excel->sheet('报销单', function($sheet) use ($reimburse) {
                $sheet->setColumnFormat(["A:F" => "@", "G:H" => "0.00", "I:Q" => "@"]);
                $sheet->setAutoSize(true);
                $sheet->with($reimburse);
                $sheet->row('1', function($row) {
                    $row->setBackground('#3754ca');
                    $row->setFontColor('#ffffff');
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });
                $sheet->freezeFirstRow();
            });
            //收款人信息
            $excel->sheet('收款人信息', function($sheet) use ($payee_info) {
                $sheet->setColumnFormat(["A:D" => "@", "E" => "0.00", "F:H" => "@"]);
                $sheet->setAutoSize(true);
                $sheet->with($payee_info);
                $sheet->row('1', function($row) {
                    $row->setBackground('#845151');
                    $row->setFontColor('#ffffff');
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

}
