<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ListCtrl
 * 列表控件类
 * @author admin
 */

namespace App\Services\Workflow;

class ListCtrlService {

    //put your code here
    /**
     * 处理控件 生成table
     * @param type $html 控件input
     */
    public function getTableHtml($html) {
        $input_attr = $this->getInputAttr($html);
        $html_str = '';
        if (!empty($input_attr['orgtitle_arr'])) {//表头
            $html_str = $this->get_Table_Html($input_attr);
        }
        return $html_str;
    }

    /**
     * 得到input的属性
     * @param type $html
     */
    private function getInputAttr($html) {
        $input['orgtitle_arr'] = $this->getOrgtitle($html); //表头
        $input['table_id'] = $this->getName($html); //name值
        $input['orgcoltype'] = $this->getOrgcolType($html); //类型
//        $input['orgunit'] = $this->getOrgunit($html); //单位
        $input['orgsum'] = $this->getOrgsun($html); //合计
        $input['orgcolvalue'] = $this->getOrgcolvalue($html); //默认值
        $input['isaddtable'] = $this->getIsaddtable($html); //是否新建子表
        $input['source'] = $this->getSource($html); //数据来源（数据库db_database）
        $input['datasource'] = $this->getDatasource($html); //数据来源 表
        $input['rownum'] = $this->getRownum($html); //工作办理时默认显示行数
        $input['dbfields'] = $this->getDbfields($html); //数据库字段
        $input['selectcheckbox'] = $this->getSelectcheckbox($html); //查询
        $input['orgwidth'] = $this->getOrgwidth($html); //宽
        $input['equation'] = $this->getEquation($html); //计算公式
        $input['colwidth'] = $this->getColwidth($html); //列表宽
        $input['orgfields'] = $this->getOrgfields($html); //字段名

        $colwidth = preg_replace('/(`$)/', '', $input['colwidth']);
        $input['colwidth_arr'] = explode('`', $colwidth); //列表宽 数组
        $orgcoltype = preg_replace('/(`$)/', '', $input['orgcoltype']);
        $input['orgcoltype_arr'] = explode('`', $orgcoltype); //类型 数组
        $orgcolvalue = preg_replace('/(`$)/', '', $input['orgcolvalue']);
        $input['orgcolvalue_arr'] = explode('`', $orgcolvalue); //默认值 数组
        $equation = preg_replace('/(`$)/', '', $input['equation']);
        $input['equation_arr'] = explode('`', $equation); //计算公式 数组
        $input['orgfields_arr'] = explode('`', $input['orgfields']); //字段名 数组
        $orgsum = preg_replace('/(`$)/', '', $input['orgsum']);
        $input['orgsum_arr'] = explode('`', $orgsum); //合计 数组
        return $input;
    }

    /**
     * 得到table的html
     * @param type $input_attr
     */
    private function get_Table_Html($input_attr) {
        $str = '<div style="width: ' . $input_attr['orgwidth'] . '; margin:0 auto;">';
        $str .= '<table class="listctrl" id="' . $input_attr['table_id'] . '"  orgsum="' . $input_attr['orgsum'] . '"  dbfields="' . $input_attr['dbfields'] . '" equation="' . $input_attr['equation'] . '" style="border-collapse:collapse;width:100%;" border="1" cellspacing="0" cellpadding="2" >';

        $str .=$this->getTableThead($input_attr); //thead表头
        $str .= '<tbody>';
        $trHtml = $this->getTableTbodyTr($input_attr); //tbody里的 tr
        $str .=$trHtml['str'];
        $str .='</tbody>';
        $str .="</table>";
        $str .= $this->getAdd_Compute_button_html($input_attr); //新增行数 与 计算 按钮

        $str .='<textarea hidden class="add_tr">' . htmlspecialchars($trHtml['add_tr']) . '</textarea>'; //用于添加
        $str .="<div>";
        return $str;
    }

    /**
     * 处理table的thead 头
     * @param type $input_attr
     */
    private function getTableThead($input_attr) {
        $html = '<thead><tr style="font-weight:bold;font-size:14px;"><th nowrap="" style="width:5%;">序号</th>';
        foreach ($input_attr['orgtitle_arr'] as $k => $v) {
            $html .= '<th style="text-align:center;width:' . $input_attr['colwidth_arr'][$k] . 'px;">' . $v . '</th>';
        }
        $html .='<th style="width:10%;">操作</th></tr></thead>';
        return $html;
    }

    /**
     * 处理table的 tbody 下的 tr
     * @param type $input_attr
     * @return string
     */
    private function getTableTbodyTr($input_attr) {
        $str = '';
        if (!empty($input_attr['rownum'])) {//工作办理时默认显示行数不为空
            for ($i = 0; $i < $input_attr['rownum']; $i++) {
                $str .='<tr>';
                $str .='<td>' . ($i + 1) . '</td>';
                for ($j = 0; $j < count($input_attr['orgcoltype_arr']); $j++) {
                    $str.='<td style="background:#f9fbd5;">' . $this->orgcoltypeData($input_attr['orgcoltype_arr'][$j], ['equation' => $input_attr['equation_arr'][$j], 'orgcolvalue' => $input_attr['orgcolvalue_arr'][$j], 'field' => $input_attr['orgfields_arr'][$j] . '_' . $i, 'colwidth' => $input_attr['colwidth_arr'][$j]]) . '</td>';
                }
                $str .= $this->getAction_buttons($input_attr); //操作按钮（选择与删除）
                $str .='</tr>';
            }
        }

        //处理tr的值 主要用于新增 
        $add_tr = $this->getAddHiddenHtml($input_attr);

        if (in_array(1, $input_attr['orgsum_arr'])) {//添加合计
            $str .=$this->getSumHtml($input_attr); //合计标签
        }
        return ['str' => $str, 'add_tr' => $add_tr];
    }
    
    /**
     * 处理隐藏tr的值 主要用于新增 
     * @param type $input_attr
     */
    private function getAddHiddenHtml($input_attr){
        $add_tr = '<tr>';
        $add_tr .='<td>1</td>';
        for ($j = 0; $j < count($input_attr['orgcoltype_arr']); $j++) {
            $add_tr.='<td style="background:#f9fbd5;">' . $this->orgcoltypeData($input_attr['orgcoltype_arr'][$j], ['equation' => $input_attr['equation_arr'][$j], 'orgcolvalue' => $input_attr['orgcolvalue_arr'][$j], 'field' => $input_attr['orgfields_arr'][$j] . '_x', 'colwidth' => $input_attr['colwidth_arr'][$j]]) . '</td>';
        }
        $add_tr .= $this->getAction_buttons($input_attr); //操作按钮（选择与删除）
        $add_tr .='</tr>';
        return $add_tr;
    }

    /**
     * 处理 操作按钮 (选择与删除)
     * @param type $input_attr.
     */
    private function getAction_buttons($input_attr) {
        if ($input_attr['datasource'] != 1) {
            $str = '<td>'
                    . '<span style="background-color:#aaa;font-size:12px;border:1px solid #FFFACD;display:inline-block;width:30px; text-align:center;cursor:pointer;"onclick="listCtrlData.optionalField(\'' . $input_attr['source'] . '\',\'' . $input_attr['datasource'] . '\',\'' . $input_attr['dbfields'] . '\',this,\'' . $input_attr['table_id'] . '\')">选择</span>'
                    . '<span style="background-color:#aaa;border: 1px solid #FFFACD;font-size:12px;display:inline-block;width:30px;text-align:center;cursor:pointer;" onclick="listCtrlData.del(this)">删除</span>'
                    . '</td>';
        } else {
            $str = '<td><span style="background-color:#aaa;border: 1px solid #FFFACD;font-size:12px;display:inline-block;width:30px;text-align:center;cursor:pointer;" onclick="listCtrlData.del(this)">删除</span></td>';
        }
        return $str;
    }

    /**
     * 合计
     * @param type $input_attr
     */
    private function getSumHtml($input_attr) {
        $str = "<tr class='hiddens'><td></td>";
        foreach ($input_attr['orgtitle_arr'] as $k => $v) {
            if (1 == $input_attr['orgsum_arr'][$k]) {
                $str .= "<td style='text-align:right;padding-right:5px;'>0</td>";
            } else {
                $str .= "<td></td>";
            }
        }
        $str .= "<td><span style='font-size:12px;text-align: center;background-color:#aaa;font-size:12px;border: 1px solid #FFFACD;display:inline-block;width:30px;cursor:pointer;'onclick=\"listCtrlData.total(this)\">合计</span></td></tr>";
        return $str;
    }

    /**
     * 得到新增行数 与 计算 按钮的html
     * @param type $input_attr
     */
    private function getAdd_Compute_button_html($input_attr) {
        $str = '<span style="font-size:14px;font-weight: normal;white-space:nowrap;">'
                . '<input type="button" class="add_new" onclick="listCtrlData.add(this);" value="新增">'
                . '<input type="input" class="add_row" onkeyup="value=value.replace(/[^0-9]/g,\'\')" style="margin-left:5px;" maxlength="2" size="2" value="1">行'
                . '</span>';
        $str .= '<input type="button" onclick="listCtrlData.calculate(\'' . $input_attr['table_id'] . '\');" value="计算">';
        return $str;
    }

    /**
     * 表头
     * @param type $html
     * @return type
     */
    private function getOrgtitle($html) {
        preg_match('/(?<=orgtitle\=[\'"]).*?(?=[\'"])/', $html, $orgtitle);
        $orgtitle = $orgtitle[0];
        $orgtitle = preg_replace('/(`$)/', '', $orgtitle);
        $orgtitleArr = explode('`', $orgtitle);
        return $orgtitleArr;
    }

    /**
     * 得到标签的name值
     * @param type $val
     */
    private function getName($html) {
        preg_match('/(?<=name\=[\'"]).*?(?=[\'"])/', $html, $attr);
        $name = '';
        if (!empty($attr)) {
            $name = $attr[0];
        }
        return $name;
    }

    /**
     * 类型
     * @param type $val
     */
    private function getOrgcolType($html) {
        preg_match('/(?<=orgcoltype\=[\'"]).*?(?=[\'"])/', $html, $attr);
        $orgcoltype = '';
        if (!empty($attr)) {
            $orgcoltype = $attr[0];
        }
        return $orgcoltype;
    }

    /**
     * 单位
     * @param type $val
     */
    private function getOrgunit($html) {
        preg_match('/(?<=orgunit\=[\'"]).*?(?=[\'"])/', $html, $attr);
        $orgunit = '';
        if (!empty($attr)) {
            $orgunit = $attr[0];
        }
        return $orgunit;
    }

    /**
     * 合计
     * @param type $val
     */
    private function getOrgsun($val) {
        preg_match('/(?<=orgsum=[\'"]).*?(?=[\'"])/', $val, $attr);
        $orgsum = '';
        if (!empty($attr)) {
            $orgsum = $attr[0];
        }
        return $orgsum;
    }

    /**
     * 默认值
     * @param type $val
     */
    private function getOrgcolvalue($val) {
        preg_match('/(?<=orgcolvalue\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $orgcolvalue = '';
        if (!empty($attr)) {
            $orgcolvalue = $attr[0];
        }
        return $orgcolvalue;
    }

    /**
     * 是否新建子表
     * @param type $val
     */
    private function getIsaddtable($val) {
        preg_match('/(?<=isaddtable\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $isaddtable = '';
        if (!empty($attr)) {
            $isaddtable = $attr[0];
        }
        return $isaddtable;
    }

    /**
     * 数据来源 
     * @param type $val
     */
    private function getSource($val) {
        preg_match('/(?<=source\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $source = '';
        if (!empty($attr)) {
            $source = $attr[0];
        }
        return $source;
    }

    /**
     * 数据来源表
     * @param type $val
     */
    private function getDatasource($val) {
        preg_match('/(?<=datasource\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $datasource = '';
        if (!empty($attr)) {
            $datasource = $attr[0];
        }
        return $datasource;
    }

    /**
     * 工作办理时默认显示行数
     * @param type $val
     */
    private function getRownum($val) {
        preg_match('/(?<=rownum\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $rownum = '';
        if (!empty($attr)) {
            $rownum = $attr[0];
        }
        return $rownum;
    }

    /**
     * 数据库字段
     * @param type $val
     */
    private function getDbfields($val) {
        preg_match('/(?<=dbfields\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $dbfields = '';
        if (!empty($attr)) {
            $dbfields = $attr[0];
        }
        return $dbfields;
    }

    /**
     * 查询
     * @param type $val
     */
    private function getSelectcheckbox($val) {
        preg_match('/(?<=selectcheckbox\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $selectcheckbox = '';
        if (!empty($attr)) {
            $selectcheckbox = $attr[0];
        }
        return $selectcheckbox;
    }

    /**
     * 宽
     * @param type $val
     */
    private function getOrgwidth($val) {
        preg_match('/(?<=orgwidth\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $orgwidth = '';
        if (!empty($attr)) {
            $orgwidth = $attr[0];
        }
        return $orgwidth;
    }

    /**
     * 计算公式
     * @param type $val
     */
    private function getEquation($val) {
        preg_match('/(?<=equation\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $equation = '';
        if (!empty($attr)) {
            $equation = $attr[0];
        }
        return $equation;
    }

    /**
     * 列宽度
     */
    private function getColwidth($val) {
        preg_match('/(?<=colwidth\=[\'"]).*?(?=[\'"])/', $val, $attr);
        $colwidth = '';
        if (!empty($attr)) {
            $colwidth = $attr[0];
        }
        return $colwidth;
    }

    /**
     * 字段名
     * @param type $val
     */
    private function getOrgfields($html) {
        preg_match('/(?<=orgfields\=[\'"]).*?(?=[\'"])/', $html, $attr);
        $orgfields = '';
        if (!empty($attr)) {
            $orgfields = $attr[0];
        }
        return $orgfields;
    }

    /**
     * 根据type类型 生成标签
     * @param type $type
     * @param type $attr_data
     * equation:计算公式
     * orgcolvalue:默认值数组
     * field:字段名
     * colwidth:列宽度
     */
    private function orgcoltypeData($type, $attr_data) {
        $str = '';
        switch ($type) {
            case 'text'://单行输入框
                $str = $this->getText($attr_data);
                break;
            case 'textarea'://多行输入框
                $str = $this->getTextarea($attr_data);
                break;
            case 'select'://下拉菜单
                $str = $this->getSelect($attr_data);
                break;
            case 'radio'://单选框
                $str = $this->getRadio($attr_data);
                break;
            case 'checkbox'://复选框
                $str = $this->getCheckbox($attr_data);
                break;
            case 'date'://日期
                $str = $this->getDate($attr_data);
                break;
            case 'datetime'://日期+时间
                $str = $this->getTime($attr_data);
                break;
            case 'int'://数值
                $str = $this->getInt($attr_data);
                break;
        }
        return $str;
    }

    /**
     * 单行文本
     * @param type $attr_data
     */
    private function getText($attr_data) {
        if ($attr_data['equation'] !== '') {//计算公式
            if (!empty($attr_data['orgcolvalue'])) {//默认值
                $str = '<input name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px;background: #f9fbd5;height:100%;" type="text" readonly value="' . $attr_data['orgcolvalue'] . '"/>';
            } else {
                $str = '<input name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px;background: #f9fbd5;height:100%;" type="text" readonly value="0"/>';
            }
        } else {
            $str = '<input name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px; height:100%;" type="text" value="' . $attr_data['orgcolvalue'] . '"/>';
        }
        return $str;
    }

    /**
     * 多行输入框
     * @param type $attr_data
     */
    private function getTextarea($attr_data) {
        $str = '<textarea name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px;height:100%;" >' . $attr_data['orgcolvalue'] . '</textarea>';
        return $str;
    }

    /**
     * 下拉菜单
     * @param type $attr_data
     */
    private function getSelect($attr_data) {
        $attr_data['orgcolvalue'] = str_replace('，', ',', $attr_data['orgcolvalue']);
        $str = '<select name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px; height:100%;">';
        if (strstr($attr_data['orgcolvalue'], ',')) {//含有（，）值进行分割
            $value_array = explode(',', $attr_data['orgcolvalue']);
            foreach ($value_array as $k => $v) {
                if (strstr($v, '|')) {//默认值处理
                    $value = explode('|', $v);
                    if ($value[0] === $value[1]) {//判断两个值是否相等
                        $str .= '<option value="' . $value[0] . '" selected>' . $value[0] . '</option>';
                    } else {
                        $str .= '<option value="' . $v . '">' . $v . '</option>';
                    }
                } else {
                    $str .= '<option value="' . $v . '">' . $v . '</option>';
                }
            }
        } else {
            $str .= '<option value="' . $attr_data['orgcolvalue'] . '">' . $attr_data['orgcolvalue'] . '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    /**
     * 单选框
     * @param type $attr_data
     */
    private function getRadio($attr_data) {
        $attr_data['orgcolvalue'] = str_replace('，', ',', $attr_data['orgcolvalue']);
        $str = '';
        if (strstr($attr_data['orgcolvalue'], ',')) {//含有（，）值进行分割
            $value_array = explode(',', $attr_data['orgcolvalue']);
            foreach ($value_array as $k => $v) {
                if (strstr($v, '|')) {//默认值处理
                    $value = explode('|', $v);
                    if ($value[0] === $value[1]) {//判断两个值是否相等
                        $str .= '<input name="' . $attr_data['field'] . '"  type="radio" value="' . $value[0] . '" checked>' . $value[0] . '&nbsp;';
                    } else {
                        $str .= '<input name="' . $attr_data['field'] . '"  type="radio" value="' . $v . '">' . $v . '&nbsp;';
                    }
                } else {
                    $str .= '<input name="' . $attr_data['field'] . '"  type="radio" value="' . $v . '">' . $v . '&nbsp;';
                }
            }
        } else {
            $str = '<input name="' . $attr_data['field'] . '" type="radio" value="' . $attr_data['orgcolvalue'] . '">' . $attr_data['orgcolvalue'] . '&nbsp;';
        }
        return $str;
    }

    /**
     * 复选框
     * @param type $attr_data
     */
    private function getCheckbox($attr_data) {
        $attr_data['orgcolvalue'] = str_replace('，', ',', $attr_data['orgcolvalue']);

        $str = '';
        if (strstr($attr_data['orgcolvalue'], ',')) {//含有（，）值进行分割
            $value_array = explode(',', $attr_data['orgcolvalue']);
            foreach ($value_array as $k => $v) {
                if (strstr($v, '|')) {//默认值处理
                    $value = explode('|', $v);
                    if ($value[0] === $value[1]) {//判断两个值是否相等
                        $str .= '<input name="' . $attr_data['field'] . '"  type="checkbox" value="' . $value[0] . '" checked>' . $value[0] . '&nbsp;';
                    } else {
                        $str .= '<input name="' . $attr_data['field'] . '" type="checkbox" value="' . $v . '">' . $v . '&nbsp;';
                    }
                } else {
                    $str .= '<input name="' . $attr_data['field'] . '" type="checkbox" value="' . $v . '">' . $v . '&nbsp;';
                }
            }
        } else {
            $str = '<input name="' . $attr_data['field'] . '" type="checkbox" value="' . $attr_data['orgcolvalue'] . '">' . $attr_data['orgcolvalue'] . '&nbsp;';
        }
        return $str;
    }

    /**
     * 日期
     * @param type $attr_data
     */
    private function getDate($attr_data) {
        $str = '<input name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px;height:100%;" type="text"readonly onclick="WdatePicker()" value="' . $attr_data['orgcolvalue'] . '" />';
        return $str;
    }

    /**
     * 日期+时间
     * @param type $attr_data
     */
    private function getTime($attr_data) {
        $str = '<input name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px;height:100%;" type="text" readonly onclick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})"  value="' . $attr_data['orgcolvalue'] . '" />';
        return $str;
    }

    /**
     * 数值
     * @param type $attr_data
     */
    private function getInt($attr_data) {
        $str = '<input name="' . $attr_data['field'] . '" style="width:' . $attr_data['colwidth'] . 'px;padding:0px;border: 0px; height:100%;" type="text" value="' . $attr_data['orgcolvalue'] . '" onkeyup="value=value.replace(/[^0-9]/g,\'\')" onpaste="value=value.replace(/[^0-9]/g,\'\')" oncontextmenu="value=value.replace(/[^0-9]/g,\'\')">';
        return $str;
    }

}
