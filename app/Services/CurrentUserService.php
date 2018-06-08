<?php

/**
 * 当前用户功能类
 * create by Fisher 2017/1/15 <fisher9389@sina.com>
 */

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\HR\Staff;

class CurrentUserService
{

    protected $userInfo;

    public function __construct()
    {
        if ($this->isLogin()) {
            if (!empty(Auth::user()->items)) {
                $this->userInfo = Auth::user()->items;
            } else {
                $this->userInfo = Auth::user();
            }
        }
    }

    public function __get($name)
    {
        return isset($this->userInfo[$name]) ? $this->userInfo[$name] : '无效属性';
    }

    /**
     * 获取员工编号
     * @return int
     */
    public function getStaffSn()
    {
        return $this->userInfo['staff_sn'];
    }

    /**
     * 获取姓名
     * @return string
     */
    public function getName()
    {
        return $this->userInfo['realname'];
    }

    /**
     * 获取当前员工信息
     * @return array
     */
    public function getInfo()
    {
        return $this->userInfo;
    }

    /**
     * 检查员工是否登录
     * @return type
     */
    public function isLogin()
    {
        return Auth::check();
    }

    /**
     * 判断当前员工是否为开发者
     * @return boolean
     */
    public function isDeveloper()
    {
        return $this->userInfo['username'] == 'developer' ? true : false;
    }

}
