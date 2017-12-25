<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\HR\ViolationReason;
use App\Models\HR\Violation;
use App\Models\HR\Staff;
use DB;

class ViolationService
{
    /*
    *  positio 职位
    *  nurber   次数
    *  reasons    原因
    *  reasns     初始金额
    */
    public function calculate(Array $violat)
    {

        if ($violat['fruit'] == "后台") {
            switch ($violat['reason_id']) {
                case "13":
                case "32":
                case "33":
                case "21":
                case "22":
                case "34":
                    return $this->settlemen($violat);
                    break;
            }
            $violat['reason'] = $this->reasns($violat)->content;
            $violat['price'] = $this->reasns($violat)->prices;
            return $violat;
            if ($violat['reason_id'] != 0) {
                $violat = $this->staffdetails($violat);
                return $violat;
            }
        } elseif ($violat['fruit'] == "市场") {
            if ($violat['reason_id'] != 0) {
                $violat = $this->staffdetails($violat);
                $violat['reason'] = $this->reasns($violat)->content;
                return $violat;
            } else {
                $violat = $this->staffdetails($violat);
                return $violat;
            }
        }
    }

    private function nurber($violat)
    {
        $time = strtotime($violat['committed_at']);
        $jhsgs = date("Y-m", $time);
        $numbers = Violation::where('staff_sn', $violat['staff_sn'])->where('type_id', $violat['type_id'])->where('committed_at', 'like', '%' . $jhsgs . '%')->count();
        $numbes = $numbers + 1;
        if ($numbes >= 4) {
            $numbes = 4;
        }
        return $numbes;
    }

    private function positio($violat)
    {
        if ($violat['level'] < 8) {
            $multiple = 3;
        } elseif ($violat['level'] > 10) {
            $multiple = 1;
        } else {
            $multiple = 2;
        }
        return $multiple;
    }

    private function reasns($violat)
    {
        $Viola = ViolationReason::find($violat['reason_id']);
        return $Viola;
    }

    private function settlemen($violat)
    {
        $violat = $this->staffdetails($violat);
        if ($this->nurber($violat) >= 4) {
            $violat['times'] = 4;
            $violat['reason'] = $this->reasns($violat)->content;
            $violat['price'] = $this->reasns($violat)->prices * $this->positio($violat) * 2 + 40;
            return $violat;
        } elseif ($this->nurber($violat) >= 2) {
            $violat['times'] = $this->nurber($violat);
            $violat['reason'] = $this->reasns($violat)->content;
            $violat['price'] = $this->reasns($violat)->prices * $this->positio($violat) * 2;
            return $violat;
        } else {
            $violat['times'] = $this->nurber($violat);
            $violat['reason'] = $this->reasns($violat)->content;
            $violat['price'] = $this->reasns($violat)->prices * $this->positio($violat) * $this->nurber($violat);
            return $violat;
        }
    }

    private function staffdetails($violat)
    {
        $dsda = Staff::where('staff_sn', $violat['staff_sn'])->where('realname', $violat['staff_name'])->first();
        $violat['department_id'] = $dsda->department->id;
        $violat['department'] = $dsda->department->full_name;//部门
        $violat['level'] = $dsda->position->level;
        $violat['position_id'] = $dsda->position->id;
        $violat['position'] = $dsda->position->name;//职位
        $violat['brand_id'] = $dsda->brand->id;
        $violat['brand'] = $dsda->brand->name;//品牌
        return $violat;
    }

}


