<?php

namespace App\Services;

class FormDesignService {

    /**
     * 控件处理
     * @param type $data
     * @return type
     */
    public static function getDataValue($data) {
        $pattern = '/(<input.*?>|<select.*?>.*?<\/select>)/';
        preg_match_all($pattern, $data, $all);
        foreach ($all[0] as $key => $val) {
            preg_match('/leipiplugins=[\'"]\w+[\'"]/', $val, $leipip);
            if (!empty($leipip)) {
                $leipiplugins = substr($leipip[0], 14, -1);
                if ($leipiplugins == 'listctrl') {//列表控件
                    $listCtrl = new \App\Services\Workflow\ListCtrlService;
                    $listCtrl_html = $listCtrl->getTableHtml($val); //处理列表控件
                    $data = str_replace($val, $listCtrl_html, $data);
                }elseif($leipiplugins =='macros'){//宏控件
                    $macros = new \App\Services\Workflow\MacrosService;
                    $macros_html = $macros->getMacrosHtml($val);
                    $data = str_replace($val, $macros_html, $data);
                }
            }
        }
        return $data;
    }
}
