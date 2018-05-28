<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Authority;
use App\Models\Position;
use App\Models\Department;
use App\Models\Role;
use App\Models\Brand;

class RbacController extends Controller {

    protected $authority;
    protected $plugin;

    public function __construct() {
        $this->authority = app('Authority');
        $this->plugin = app('Plugin');
    }

    /* -- authority start -- */

    public function showAuthorityPage() {
        return view('system.authority');
    }

    public function getAuthorityList(Request $request) {
        return $this->plugin->dataTables($request, new Authority);
    }

    public function getAuthorityTreeView(Request $request) {
        if ($request->has('position_id')) {
            $checked = Position::find($request->position_id)->authority->pluck('id')->toArray();
        } else if ($request->has('department_id')) {
            $checked = Department::find($request->department_id)->authority->pluck('id')->toArray();
        } else if ($request->has('role_id')) {
            $checked = Role::find($request->role_id)->authority->pluck('id')->toArray();
        } else if ($request->has('staff_sn')) {
            $checked = $this->authority->getAuthoritiesByStaffSn($request->staff_sn);
        } else {
            $checked = [];
        }
        $authorities = Authority::where('parent_id', 0)->orderBy('sort', 'asc')->get();
        $response = $this->changeAuthorityIntoZtreeNode($authorities, $checked);
        return $response;
    }

    public function setAuthority(Request $request) {
        if ($request->has('authorities')) {
            $authorities = $request->authorities;
        } else {
            $authorities = [];
        }
        if ($request->has('position_id')) {
            Position::find($request->position_id)->authority()->sync($authorities);
        } elseif ($request->has('department_id')) {
            Department::find($request->department_id)->authority()->sync($authorities);
        } elseif ($request->has('role_id')) {
            Role::find($request->role_id)->authority()->sync($authorities);
        } else {
            return ['status' => -1, 'message' => '缺少目标id'];
        }
        return ['status' => 1, 'message' => '权限配置成功'];
    }

    public function reOrderAuthority(Request $request) {
        $nodes = $request->nodes;
        Authority::reOrder($nodes);
        return ['status' => 1, 'message' => '排序成功'];
    }

    public function refreshAuthority() {
        $this->authority->forgetAuthorities();
        return redirect()->back();
    }

    private function changeAuthorityIntoZtreeNode($authorities, $checked) {
        $data = [];
        foreach ($authorities as $authority) {
            $dataTmp = [
                'name' => $authority->auth_name,
                'id' => $authority->id,
                'drag' => true,
                'iconSkin' => ' _',
                'children' => $this->changeAuthorityIntoZtreeNode($authority->_children, $checked),
            ];
            if ($authority->is_public) {
                $dataTmp['chkDisabled'] = true;
                $dataTmp['checked'] = true;
            } elseif (in_array($authority->id, $checked)) {
                $dataTmp['checked'] = true;
            }
            if (!empty($authority->menu_logo)) {
                $dataTmp['iconSkin'] = 'fa fa-fw ' . $authority->menu_logo . ' _';
            }
            $data[] = $dataTmp;
        }
        return $data;
    }

    /* -- authority end -- */
    /* -- role start -- */

    public function showRolePage() {
        $data['brand'] = Brand::orderBy('sort', 'asc')->get();
        return view('system.role')->with($data);
    }

    public function getRoleList(Request $request) {
        return $this->plugin->dataTables($request, new Role);
    }

    public function addRoleByOne(Request $request) {
        $data = $request->except(['_url', '_token', 'department', 'brand_id']);
        $role = Role::create($data);
        $this->changeRoleConnection($request, $role);
        return ['status' => 1, 'message' => '添加成功'];
    }

    public function editRoleByOne(Request $request) {
        $data = $request->except(['_url', '_token', 'department', 'brand_id']);
        $id = $request->id;
        $role = Role::find($id);
        $role->update($data);
        $this->changeRoleConnection($request, $role);
        return ['status' => 1, 'message' => '编辑成功'];
    }

    public function deleteRoleByOne(Request $request) {
        $id = $request->id;
        Role::find($id)->delete();
        return ['status' => 1, 'message' => '删除成功'];
    }

    private function changeRoleConnection($request, $role) {
        $department = $request->has('department') ? $request->department : [];
        $allBrand = Brand::pluck('id')->toArray();
        $brand = $request->has('brand_id') && $request->brand_id != $allBrand ? $request->brand_id : [];
        $role->department()->sync($department);
        $role->brand()->sync($brand);
    }

    /* -- role end -- */
}
