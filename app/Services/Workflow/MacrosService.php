<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Workflow;

/**
 * 宏控件
 * Description of Macros
 *
 * @author admin
 */
class MacrosService {

    /**
     * 宏控件处理
     * @param type $html
     */
    public function getMacrosHtml($html) {
        preg_match('/orgtype=[\'"]\w+[\'"]/', $html, $type); //验证宏控件的类型
        $value = '';
        if (!empty($type)) {
            $type = substr($type[0], 9, -1);
            $value = $this->getInfo($type);
        }
        $macros_html = preg_replace('/(<option.*?>)?{macros}(<\/option>)?/', $value, $html);
        return $macros_html;
    }

    /**
     * 宏控件处理相应的值
     * @param type $type
     * @return string
     */
    private function getInfo($type) {
        $info = session('admin');
        switch ($type) {
            case 'sys_userid':
                $value = $info['staff_sn'];
                break;
            case 'sys_datetime':
                $value = date('Y-m-d H:i', time());
                break;
            case 'sys_date':
                $value = date('Y-m-d', time());
                break;
            case 'sys_date_cn':
                $value = date('Y年m月d日', time());
                break;
            case 'sys_date_cn_short1':
                $value = date('Y年m月', time());
                break;
            case 'sys_date_cn_short4':
                $value = date('Y', time());
                break;
            case 'sys_date_cn_short3':
                $value = date('Y年', time());
                break;
            case 'sys_date_cn_short2':
                $value = date('m月d日', time());
                break;
            case 'sys_time':
                $value = date('H:i', time());
                break;
            case 'sys_week':
                $week = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
                $value = $week[date('w', time())];
                break;
            case 'sys_realname':
                $value = $info['realname'];
                break;
            case 'sys_dept_full':
                $value = $info->department->full_name;
                break;
            case 'sys_dept':
                $value = $info->department->name;
                break;
            case 'sys_position_name':
                $value = $info->position->name;
                break;
            case 'sys_realname_date':
                $value = $info['realname'] . ' ' . date('Y-m-d', time());
                break;
            case 'sys_realname_time':
                $value = $info['realname'] . ' ' . date('Y-m-d H:i:s', time());
                break;
            case 'y_sys_list_department_list'://部门列表
                $value = $this->getDepartmentList();
                break;
            case 'y_sys_list_user_list'://人员列表
                $data = $this->getUserList();
                $value = $data['data'];
                break;
            case 'y_sys_list_position_list'://角色列表
                $value = $this->getPositionList();
                break;
            case 'y_sys_list_self_department_name'://部门主管，（本部门）
                $value = $this->getSelfDepartmentManage($info);
                break;
            case 'y_sys_list_prev_department_name'://部门主管，（上级部门）
                $value = $this->getPrevDepartmentManage($info);
                break;
            default:
                $value = '';
        }
        return $value;
    }

    /**
     * 得到部门列表
     */
    private function getDepartmentList() {
        $data = \App\Models\Department::select('id', 'name', 'full_name')->get();
        $array = '';
        $array = '<option value="" selected=""></option>';
        foreach ($data as $k => $v) {
            $array.= '<option value="' . $v->id . '">' . $v->full_name . '</option>';
        }
        return $array;
    }

    /**
     * 得到人员列表
     */
    public function getUserList($array = []) {
        $page_object = new \App\Services\Workflow\PageService;
        $page = $page_object->getPage($array); //分页
        $data = \App\Models\HR\Staff::where('status_id', '>', 0)->select('staff_sn', 'realname')->skip($page['start'])->take($page['length'])->get();
        $count = \App\Models\HR\Staff::where('status_id', '>', 0)->count();

        $str = '';
        if (empty($array)) {
            $str = '<option value="" selected></option>';
        }
        foreach ($data as $k => $v) {
            $str.= '<option value="' . $v->staff_sn . '">' . $v->realname . '</option>';
        }

        if ($count > $page['length'] && empty($array)) {
            $str.='<option  value="add_page" p="' . $page['p'] . '" length="' . $page['length'] . '" style="color:red;">加载更多</option>';
        }
        return ['data' => $str, 'p' => $page['p'], 'length' => $page['length']];
    }

    /**
     * 得到角色列表
     */
    private function getPositionList() {
        $data = \App\Models\Position::select('id', 'name')->get();
        $array = '<option value="" selected=""></option>';
        foreach ($data as $k => $v) {
            $array .='<option value="' . $v->id . '">' . $v->name . '</option>';
        }
        return $array;
    }

    /**
     * 得到本部门主管
     */
    private function getSelfDepartmentManage($info) {
        $manager_sn = $info->department->manager_sn;
        $manager_name = $info->department->manager_name;
        return $this->departmentManageCheck($manager_sn, $manager_name);
    }

    /**
     * 得到上级部门主管
     */
    private function getPrevDepartmentManage($info) {
        $data = '';
        if (!empty($info->department->top)) {
            $manager_sn = $info->department->top->manager_sn;
            $manager_name = $info->department->top->manager_name;
            $data = $this->departmentManageCheck($manager_sn, $manager_name);
        }
        return $data;
    }

    /**
     * 部门主管数据处理 判断是否为数组
     */
    private function departmentManageCheck($manager_sn, $manager_name) {
        $array = '<option value="" selected=""></option>';
        if (!empty($manager_sn)) {
            if (is_array($manager_sn)) {
                $array = '<option value="" selected=""></option>';
                foreach ($manager_sn as $k => $v) {
                    $array .='<option value="' . $v->manager_sn . '">' . $v->manager_name . '</option>';
                }
            } else {
                $array .='<option value="' . $manager_sn . '">' . $manager_name . '</option>';
            }
        }
        return $array;
    }

}
