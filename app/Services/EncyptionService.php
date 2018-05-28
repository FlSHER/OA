<?php

/**
 * 加密服务
 * create by Fisher 2016/8/27 <fisher9389@sina.com>
 */

namespace App\Services;

class EncyptionService {
    /* 金额转大写所需参数Start */

    private $basical = array(0 => "零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖");
    private $advanced = array(1 => "拾", "佰", "仟");

    /* 金额转大写所需参数End */

    /**
     * 密码加密运算
     * @param type $password
     */
    public function password($password, $salt) {
        for ($i = 1; $i <= 10; $i++) {
            $password = hash('sha256', $password . $salt);
        }
        return $password;
    }

    /* ---- 金额转大写Start ---- */

    public function numberToCNY($number) {
        $number = trim($number);
        if ($number > 999999999999)
            return "数字太大，无法处理";
        if ($number == '0')
            return "零";
        $data = explode(".", $number);
        $data[0] = $this->intToCNY($data[0]);
        $data[1] = $this->decToCNY($data[1]);
        return $data[0] . $data[1];
    }

    private function intToCNY($number) {
        $arr = array_reverse(str_split($number));
        $data = '';
        $zero = false;
        $zero_num = 0;
        foreach ($arr as $k => $v) {
            $_chinese = '';
            $zero = ($v == 0) ? true : false;
            $x = $k % 4;
            if ($x && $zero && $zero_num > 1)
                continue;
            switch ($x) {
                case 0:
                    if ($zero) {
                        $zero_num = 0;
                    } else {
                        $_chinese = $this->basical[$v];
                        $zero_num = 1;
                    }
                    if ($k == 8) {
                        $_chinese.='亿';
                    } elseif ($k == 4) {
                        $_chinese.='万';
                    }
                    break;
                default:
                    if ($zero) {
                        if ($zero_num == 1) {
                            $_chinese = $this->basical[$v];
                            $zero_num++;
                        }
                    } else {
                        $_chinese = $this->basical[$v];
                        $_chinese.=$this->advanced[$x];
                    }
            }
            $data = $_chinese . $data;
        }
        return $data . '元';
    }

    private function decToCNY($number) {
        if (strlen($number) < 2)
            $number.='0';
        $arr = array_reverse(str_split($number));
        $data = '';
        $zero_num = false;
        foreach ($arr as $k => $v) {
            $zero = ($v == 0) ? true : false;
            $_chinese = '';
            if ($k == 0) {
                if (!$zero) {
                    $_chinese = $this->basical[$v];
                    $_chinese.='分';
                    $zero_num = true;
                }
            } else {
                if ($zero) {
                    if ($zero_num) {
                        $_chinese = $this->basical[$v];
                    }
                } else {
                    $_chinese = $this->basical[$v];
                    $_chinese.='角';
                }
            }
            $data = $_chinese . $data;
        }
        return $data;
    }

    /* ---- 金额转大写End ---- */
}
