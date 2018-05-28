<?php

/**
 * 后台菜单服务
 * create by Fisher 2016/8/28 <fisher9389@sina.com>
 */

namespace App\Services;

use App\Models\Authority;
use App\Models\App;

class MenuService {

    public function getMenuData($parentId = 0) {
        $staffSn = app('CurrentUser')->getStaffSn();
        $avalibaleAuthorities = app('Authority')->getAvailableAuthorities($staffSn);
        $authorities = Authority::where(['parent_id' => $parentId, 'is_menu' => 1])
                        ->where(function($query) use ($avalibaleAuthorities) {
                            $query->whereIn('id', $avalibaleAuthorities)
                            ->orWhere(['is_public' => 1]);
                        })
                        ->orderBy('sort', 'asc')
                        ->get()->all();
        $menu = array_map(function($value) {
            $children = $this->getMenuData($value->id);
            if (!empty($children)) {
                $value->children = $children;
            }
            return $value;
        }, $authorities);
        return $menu;
    }

    public function getAppData() {
        $app = App::where(['is_active' => 1])->get();
        return $app;
    }

}
