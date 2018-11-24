<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleCollection;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->with('staff', 'brand', 'department', 'authority')
            ->filterByQueryString()
            ->withPagination();

        return new RoleCollection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Role $role)
    {
        $this->validate($request, [
            'role_name' => 'required|unique:roles|max:10',
        ],[
            'role_name.required' => '角色名称不能为空',
            'role_name.unique' => '角色名称已存在',
            'role_name.max' => '角色名称不能超过 :max 个字',
        ]);
        $data = $request->all();
        $role->role_name = $data['role_name'];

        return $role->getConnection()->transaction(function () use ($role, $data) {
            $role->save();
            if (!empty($data['brand'])) {
                $role->brand()->attach($data['brand']);
            }
            if (!empty($data['department'])) {
                $role->department()->attach($data['department']);
            }
            
            $role->load([
                'staff' => function ($query) {
                    $query->select('staff.staff_sn', 'staff.realname');
                }, 
                'department' => function ($query) {
                    $query->select('id', 'name');
                },
                'brand' => function ($query) {
                    $query->select('id', 'name');
                },
            ]);

            return response()->json($role, 201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->load([
            'staff' => function ($query) {
                $query->select('staff.staff_sn', 'staff.realname');
            },  
            'department' => function ($query) {
                return $query->select('id', 'name');
            },
            'brand' => function ($query) {
                return $query->select('id', 'name');
            },
        ]);

        return response()->json($role, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'role_name' => ['required', Rule::unique('roles')->ignore($role->id), 'max:10'],
        ],[
            'role_name.required' => '角色名称不能为空',
            'role_name.unique' => '角色名称已存在',
            'role_name.max' => '角色名称不能超过 :max 个字',
        ]);
        $data = $request->all();
        $role->role_name = $data['role_name'];

        return $role->getConnection()->transaction(function () use ($role, $data) {
            $role->save();

            if (!empty($data['brand'])) {
                $role->brand()->sync($data['brand']);
            }

            if (!empty($data['department'])) {
                $role->department()->sync($data['department']);
            }

            if (!empty($data['authority'])) {
                $role->authority()->sync($data['authority']);
                session()->forget('authorities');
            }
            
            if (!empty($data['staff'])) {
                $role->staff()->sync($data['staff']);
            }

            $role->load([
                'staff' => function ($query) {
                    $query->select('staff.staff_sn', 'staff.realname');
                }, 
                'department' => function ($query) {
                    $query->select('id', 'name');
                },
                'brand' => function ($query) {
                    $query->select('id', 'name');
                },
                'authority' => function ($query) {
                    $query->select('id', 'auth_name');
                },
            ]);

            return response()->json($role, 201);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        return $role->getConnection()->transaction(function () use ($role) {
            $role->delete();
            $role->staff()->detach();
            $role->brand()->detach();
            $role->authority()->detach();
            $role->department()->detach();

            return response()->json(null, 204);
        });
    }
}
