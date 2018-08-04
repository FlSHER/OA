<?php

/**
 * 后台权限服务
 * create by Fisher 2016/8/28 <fisher9389@sina.com>
 */

namespace App\Services;

use App\Models\Authority;
use App\Models\HR\Shop;
use App\Models\HR\Staff;
use App\Models\Department;
use App\Models\Brand;

class AuthorityService
{

    /**
     * 获取当前权限id
     * @return int
     */
    public function getCurrentAuthorityId($url = null)
    {
        $authorityIdArr = $this->getAuthorityIdArr($url);
        $authorityId = end($authorityIdArr);
        return $authorityId;
    }

    /**
     * 获取当前菜单名称
     * @return type
     */
    public function getCurrentMenuName($url = null)
    {
        return Authority::find($this->getCurrentAuthorityId($url))->menu_name;
    }

    /**
     * 获取当前及所有上级权限id
     * @return array
     */
    public function getAuthorityIdArr($url = null)
    {
        $url = empty($url) ? request()->path() : $url;
        $authorityUri = rtrim($url, '/');
        $authorityUriArr = $this->explodeToUriArr($authorityUri);
        $authorityIdArr = $this->getIdArrByUri($authorityUriArr);
        return $authorityIdArr;
    }

    /**
     * 检查当前管理员是否拥有某一权限
     * @param int $authorityId
     * @return boolean
     */
    public function checkAuthority($authorityId)
    {
        $authorities = $this->getAvailableAuthorities();
        return in_array($authorityId, $authorities) ? true : false;
    }

    public function checkDepartment($departmentId)
    {
        $availableDepartments = $this->getAvailableDepartments();
        return in_array($departmentId, $availableDepartments) ? true : false;
    }

    public function checkBrand($brandId)
    {
        $availableBrands = $this->getAvailableBrands();
        return in_array($brandId, $availableBrands) ? true : false;
    }

    /**
     * 获取当前管理员拥有的权限
     * @param int $staffSn
     * @return array
     */
    public function getAvailableAuthorities($staffSn = '')
    {
        if (session()->has('authorities')) {
            $authorities = session('authorities');
        } else {
            if (empty($staffSn))
                $staffSn = app('CurrentUser')->getStaffSn();
            $authorities = $this->getAuthoritiesByStaffSn($staffSn);
            session()->put('authorities', $authorities);
        }
        return $authorities;
    }

    /**
     * 获取当前管理员可操作部门
     * @param int $staffSn
     * @return array
     */
    public function getAvailableDepartments($staffSn = '')
    {
        if (session()->has('available_departments')) {
            $departments = session('available_departments');
        } else {
            if (empty($staffSn))
                $staffSn = app('CurrentUser')->getStaffSn();
            $departments = $this->getAvailableDepartmentsByStaffSn($staffSn);
            session()->put('available_departments', $departments);
        }
        return $departments;
    }

    /**
     * 获取当前管理员可操作品牌
     * @param int $staffSn
     * @return array
     */
    public function getAvailableBrands($staffSn = '')
    {
        if (session()->has('available_brands')) {
            $brands = session('available_brands');
        } else {
            if (empty($staffSn))
                $staffSn = app('CurrentUser')->getStaffSn();
            $brands = $this->getAvailableBrandsByStaffSn($staffSn);
            session()->put('available_brands', $brands);
        }
        return $brands;
    }

    /**
     * 获取当前管理员可操作店铺
     * @param string $staffSn
     * @return mixed
     */
    public function getAvailableShops($staffSn = '')
    {
        if (session()->has('available_shops')) {
            $shops = session('available_shops');
        } else {
            if (empty($staffSn))
                $staffSn = app('CurrentUser')->getStaffSn();
            $shops = $this->getAvailableShopsByStaffSn($staffSn);
            session()->put('available_shops', $shops);
        }
        return $shops;
    }

    /**
     * 清除权限
     */
    public function forgetAuthorities()
    {
        session()->forget('authorities');
        session()->forget('available_departments');
        session()->forget('available_brands');
        session()->forget('available_shops');
    }

    /**
     * 根据员工编号获取权限（部门，职位，角色）
     * @param int $staffSn
     * @return array
     */
    public function getAuthoritiesByStaffSn($staffSn)
    {
        if ($this->isDeveloper($staffSn)) {
            $authorities = Authority::get()->pluck('id')->toArray();
            array_push($authorities, 0);
        } else {
            $staff = Staff::find($staffSn);
            $departmentAuth = $staff->department->authority->pluck('id')->toArray();
            $positionAuth = $staff->position->authority->pluck('id')->toArray();
            $roles = $staff->role;
            $authorities = array_merge($departmentAuth, $positionAuth);
            foreach ($roles as $role) {
                $authoritiesTmp = $role->authority->pluck('id')->toArray();
                $authorities = array_merge($authorities, $authoritiesTmp);
            }
            $publicAuth = Authority::where(['is_public' => 1])->get()->pluck('id')->toArray();
            $authorities = array_merge($authorities, $publicAuth);
            $authorities = array_unique($authorities);
        }
        return array_values($authorities);
    }

    /**
     * 根据员工编号获取可操作部门（角色）
     * @param int $staffSn
     * @return array
     */
    public function getAvailableDepartmentsByStaffSn($staffSn)
    {
        if ($this->isDeveloper($staffSn)) {
            $departments = Department::withTrashed()->get()->pluck('id')->toArray();
            array_push($departments, 0);
        } else {
            $roles = Staff::find($staffSn)->role;
            $departments = Department::where('is_public', 1)->pluck('id')->toArray();
            foreach ($roles as $role) {
                $departmentsTmp = $role->department->pluck('id')->toArray();
                $departments = array_merge($departments, $departmentsTmp);
            }
            $departments = array_unique($departments);
        }
        return $departments;
    }

    /**
     * 根据员工编号获取可操作品牌（角色）
     * @param int $staffSn
     * @return array
     */
    public function getAvailableBrandsByStaffSn($staffSn)
    {
        if ($this->isDeveloper($staffSn)) {
            $brands = Brand::get()->pluck('id')->toArray();
        } else {
            $roles = Staff::find($staffSn)->role;
            $brands = Brand::where('is_public', 1)->pluck('id')->toArray();
            foreach ($roles as $role) {
                $brandsTmp = $role->brand->pluck('id')->toArray();
                $brands = array_merge($brands, $brandsTmp);
            }
            $brands = array_unique($brands);
        }
        return $brands;
    }

    public function getAvailableShopsByStaffSn($staffSn)
    {
        if ($this->isDeveloper($staffSn)) {
            $shops = Shop::get()->pluck('shop_sn')->toArray();
        } else {
            $shops = Shop::visible($staffSn)->pluck('shop_sn');
        }
        return $shops;
    }

    private function explodeToUriArr($authorityUri)
    {
        $authorityUriArr = explode('/', $authorityUri);
        return $authorityUriArr;
    }

    private function getIdArrByUri($authorityUriArr)
    {
        $authorityId = 0;
        foreach ($authorityUriArr as $authorityUri) {
            $authorityId = $this->getIdByUri($authorityUri, $authorityId);
            $authorityIdArr[] = $authorityId;
        }
        return $authorityIdArr;
    }

    private function getIdByUri($uri, $parentId)
    {
        $authority = Authority::where(['access_url' => $uri, 'parent_id' => $parentId])->select('id')->first();
        if (!isset($authority->id)) {
            return 'none';
        }
        return $authority->id;
    }

    private function isDeveloper($staffSn)
    {
        return $staffSn == config('auth.developer.staff_sn');
    }

}
