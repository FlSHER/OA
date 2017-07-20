<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//value 查询单个值

namespace App\Http\Controllers\HR;

use App\Models\HR\ViolationType;
use App\Models\HR\ViolationLog;
use App\Models\HR\Violation;
use Illuminate\Http\Request;
use App\Models\HR\ViolationReason;
use App\Models\Position;
use App\Services\dingdingApi;
use Illuminate\Database\Query\Builder;
use Illuminate\Routing\Controller;
use App\Http\Controllers\HR\Staff;
use Excel;

class ViolationController extends controller {
    /* 添加大爱试图 */

    public function showEnterPage() {
        $pods = ViolationType::get();
        return view('hr/violation_add')->with(['pods' => $pods]);
    }

    /*  保存大爱单列表 */

    public function getEnterList(Request $request) {
        $staffSn = app('CurrentUser')->getStaffSn();
        $where = 'ISNULL(submitted_at) AND maker_sn =' . $staffSn;
        return app('Plugin')->dataTables($request, new Violation, $where);
    }

    //保存大爱 
    public function addByOne(Request $request) {
        $option = $request->except(['_token', '_url']);
        $violat = app('HRM');
        $optione = $violat->calculate($option);
        $optio = [
            'maker_sn' => app('CurrentUser')->getStaffSn(),
            'maker_name' => app('CurrentUser')->getName(),
        ];
        $options = array_merge($optione, $optio);
        $addBy = Violation::create($options);
        return $addBy;
    }

    /* 单个查询 */

    public function getInfo(Request $request) {
        $asd = implode("", $request->except(['_token', '_url']));
        $single = Violation::find($asd);
        return $single;
    }

    /* 编辑大爱单 */

    public function editByOne(Request $request) {
        $compile = $request->except(['_token', '_url']);
        $editBy = Violation::where('id', $compile['id'])->update($compile);
        return $editBy;
    }

    /* 删除大爱单 */

    public function deleteByOne(Request $request) {
        $remo = implode("", $request->except(['_token', '_url']));
        $removes = Violation::where('id', $remo)->delete();
        return $removes;
    }

    /* 大爱类型类型试图 */

    public function showCategoryPage() {
        return view('hr/violation_type');
    }

    /* 大爱类型列表 */

    public function getCategoryList(Request $request) {
        return app('Plugin')->dataTables($request, new ViolationType);
    }

    /* 添加大爱类型 */

    public function addCategoryByOne(Request $request) {
        $classification = $request->except(['_token', '_url']);
        $classificati = ViolationType::create($classification);
        return $classificati;
    }

    /* 修改大爱类型 */

    public function editCategoryByOne(Request $request) {
        $compile = $request->except(['_token', '_url']);
        $editBy = ViolationType::where('id', $compile['id'])->update($compile);
        return $editBy;
    }

    /* 单个查询 */

    public function getCategoryInfo(Request $request) {
        $asd = implode("", $request->except(['_token', '_url']));
        $single = ViolationType::find($asd);
        return $single;
    }

    /* 删除大爱类型 */

    public function deleteCategoryByOne(Request $request) {
        $Categ = implode("", $request->except(['_token', '_url']));
        $removs = ViolationType::where('id', $Categ)->delete();
        return $removs;
    }

    /* 大爱原因试图 */

    public function showReasonPage() {
        $typesd = ViolationType::get();
        return view('hr/violation_reason')->with(['typed' => $typesd]);
    }

    /* 添加大爱原因 */

    public function addReasonByOne(Request $request) {
        $addtype = $request->except(['_token', '_url']);
        if (empty($addtype['prices'])) {
            array_forget($addtype, 'prices');
        }
        $caddtypes = ViolationReason::create($addtype);
        return $caddtypes;
    }

    /* 大爱原因列表 */

    public function getReasonList(Request $request) {
        return app('Plugin')->dataTables($request, new ViolationReason);
    }

    /* 编辑大爱原因 */

    public function editReasonByOne(Request $request) {
        $compile = $request->except(['_token', '_url']);
        $editBy = ViolationReason::where('id', $compile['id'])->update($compile);
        return $editBy;
    }

    /* 单个查询 */

    public function getReasonInfo(Request $request) {
        $asd = implode("", $request->except(['_token', '_url']));
        $demand = ViolationReason::with('type')->find($asd);
        return $demand;
    }

    /* 删除大爱原因 */

    public function deleteReasonByOne(Request $request) {
        $Categ = implode("", $request->except(['_token', '_url']));
        $removs = ViolationReason::where('id', $Categ)->delete();
        return $removs;
    }

    /* 提交大爱单 */

    public function submit(Request $request, \App\Services\DingdingApi $api) {
        $subi = $request->except(['_token', '_url']);
        $respect = '您在';
        $firsts = '第';
        $frequency = '次';
        $amount = '金额为';
        $datas = array_pluck($subi['data'], 'id');
        $time = [
            'submitted_at' => date('Y-m-d H:m:s', time()),
        ];
        $lsdf = Violation::whereIn('id', $datas)->update($time);
        $ilknt = Violation::whereIn('id', $datas)->get()->toArray();
        foreach ($ilknt as $dingdin) {
            if (!empty($dingdin['dingding'])) {
                $dingding = $dingdin['dingding'];
                $staff_name = $dingdin['staff_name']; //姓名
                $department = $dingdin['department']; //部门
                $reason = $dingdin['reason']; //违纪原因
                $committed_at = $dingdin['committed_at']; //违纪时间
                $times = $dingdin['times'];
                $price = $dingdin['price']; //违纪金额
                $content = $staff_name . ":" . $department . ";" . $respect . $committed_at . $reason . $firsts . $times . $frequency . ";" . $amount . $price . "。";
                $iohi = $api->sendViolationMessage($dingding, $content);
                return ['state' => 0, 'file_name' => $iohi];
            } else {

                return ['state' => 1, 'file_name' => $dingdin['staff_name'] . $dingdin['department'] . '没有钉钉编号'];
            }
        }
    }

    /* 修改提交后的大爱单 */

    public function amend(Request $request) {
        $subie = $request->except(['_token', '_url']);
        $hiuah = Violation::with('tiontype')->where('id', $subie['id'])->orWhere('submitted_at', '=', '')->first()->toArray();
        Violation::where('id', $subie['id'])->update($subie);
        $obliterate = Violation::with('tiontype')->where('id', $subie['id'])->orWhere('submitted_at', '=', '')->first()->toArray();
        $tiontyp = array_except($hiuah['tiontype']['0'], ['created_at', 'updated_at', 'id']);
        $obliterat = array_except($obliterate['tiontype']['0'], ['created_at', 'updated_at', 'id']);
        $aiosuo = array_except($hiuah, ['created_at', 'submitted_at', 'updated_at', 'deleted_at', 'maker_name', 'maker_sn', 'paid_at', 'tiontype']);
        $aioobliteratesuos = array_except($obliterate, ['created_at', 'submitted_at', 'updated_at', 'deleted_at', 'maker_name', 'maker_sn', 'paid_at', 'tiontype']);
        $aiosuo['type'] = $tiontyp['name'];
        $aioobliteratesuos['type'] = $obliterat['name'];
        $sds = array_diff_assoc($aiosuo, $aioobliteratesuos);
        $oihk = array_diff_assoc($aioobliteratesuos, $aiosuo);
        $iuhk = [];
        foreach ($sds as $k => $v) {
            foreach ($oihk as $ke => $vie) {
                if ($k === $ke) {
                    $ku = [];
                    $ku = $v . '==》' . $vie;
                    $iuhk[] = $ku;
                }
            }
        }
        $optios = [
            'operation' => $iuhk,
            'operated_at' => date('Y-m-d H:m:s', time()),
            'violation_id' => $subie['id'],
            'operator_sn' => app('CurrentUser')->getStaffSn(),
            'operator_name' => app('CurrentUser')->getName(),
        ];
        $succeed = ViolationLog::create($optios);
        return $succeed;
    }

    /* 提交后的大爱单试图 */

    public function showManagePage() {
        $pods = ViolationType::get();
        return view('hr/violation')->with(['pods' => $pods]);
    }

    /* 提交后的大爱单列表 */

    public function getList(Request $request) {
        return app('Plugin')->dataTables($request, new Violation);
    }

    //vfprintf
    //private
    /* 确认已交钱 */
    function delivery(Request $request) {
        $id = implode("", $request->except(['_token', '_url']));
        $time = date("Y-m-d H:s:m", time());
        $nihao = [
            'paid_at' => $time,
        ];
        $deliv = Violation::where('id', $id)->update($nihao);
        return $deliv;
    }

    function export(Request $request) {
        $staff = $this->getList($request)['data'];
        foreach ($staff as $key => $value) {
            if ($value['fruit'] == '后台') {
                $responser['员工姓名'] = $value['staff_name'];
                $responser['员工部门'] = $value['department'];
                $responser['职位'] = $value['position'];
                $responser['开单日期'] = $value['created_at'];
                $responser['原因'] = $value['reason'];
                $responser['违纪时间'] = $value['committed_at'];
                $responser['次数'] = $value['times'];
                $responser['金额'] = $value['price'];
                $responser['分数'] = '';
                $responser['开单人'] = $value['supervisor_name'];
                $staffer[$key] = $responser;
            }
        };
        foreach ($staff as $key => $value) {
            if ($value['fruit'] == '市场') {
                $response['品牌'] = $value['brand'];
                $response['团队部门'] = $value['department'];
                $response['职位'] = $value['position'];
                $response['工号'] = $value['staff_sn'];
                $response['姓名'] = $value['staff_name'];
                $response['状态'] = $value['committed_at'];
                $response['违纪原因'] = $value['reason'];
                $response['违纪时间'] = $value['committed_at'];
                $response['大爱金额'] = $value['price'];
                $response['实交金额'] = $value['price'];
                $response['开单人'] = $value['supervisor_name'];
                $response['提交人'] = $value['maker_name'];
                $response['提交时间'] = $value['submitted_at'];
                $market[$key] = $response;
            }
        }
        $filename = 'hr/staff/export/大爱信息' . date('YmdHis') . '-' . session()->get('admin.staff_sn');
        Excel::create($filename, function($excel) use ($staffer, $market) {
            $excel->setCreator('后台大爱信息')
                    ->setCompany('市场大爱信息');
            $excel->sheet('市场大爱信息', function($sheet) use ($market) {
                $sheet->with($market);
                $sheet->row('1', function($row) {
                    $row->setBackground('#26651e');
                    $row->setFontColor('#ffffff');
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });
            });
            $excel->sheet('后台大爱信息', function($sheet) use ($staffer) {
                $sheet->with($staffer);
                $sheet->row('1', function($row) {
                    $row->setBackground('#26651e');
                    $row->setFontColor('#ffffff');
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });
            });
        })->save('xlsx');
        return ['state' => 1, 'file_name' => $filename];
    }

}
