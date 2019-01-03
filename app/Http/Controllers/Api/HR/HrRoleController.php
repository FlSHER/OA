<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\HrRole;
use Illuminate\Http\Request;
use App\Http\Requests\HrRoleRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\HrRole as HrRoleResource;

class HrRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = HrRole::query()
            ->with(['staff', 'brand', 'department'])
        	->filterByQueryString()
        	->sortByQueryString()
        	->withPagination();

        if (isset($list['data'])) {
        	$list['data'] = HrRoleResource::collection($list['data']);

        	return $list;
        }

        return HrRoleResource::collection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\HrRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HrRoleRequest $request, HrRole $hr_role)
    {
        $data = $request->all();
        $hr_role->name = $data['name'];

        return $hr_role->getConnection()->transaction(function () use ($hr_role, $data) {
            $hr_role->save();
            if (!empty($data['brand'])) {
                $hr_role->brand()->attach($data['brand']);
            }
            if (!empty($data['department'])) {
                $hr_role->department()->attach($data['department']);
            }
            
            $hr_role->load(['staff', 'brand', 'department']);

            return response()->json($hr_role, 201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\HrRole  $role
     * @return \Illuminate\Http\Response
     */
    public function show(HrRole $role)
    {
        $role->load(['staff', 'brand', 'department']);

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\HrRoleRequest  $request
     * @param  \App\Models\HR\HrRole  $role
     * @return \Illuminate\Http\Response
     */
    public function update(HrRoleRequest $request, HrRole $hr_role)
    {
        $data = $request->all();
        $hr_role->name = $data['name'];

        return $hr_role->getConnection()->transaction(function () use ($hr_role, $data) {
            $hr_role->save();
            if (!empty($data['staff'])) {
                $hr_role->staff()->sync($data['staff']);
            }
            if (!empty($data['brand'])) {
                $hr_role->brand()->sync($data['brand']);
            }
            if (!empty($data['department'])) {
                $hr_role->department()->sync($data['department']);
            }
            $hr_role->load(['staff', 'brand', 'department']);

            return response()->json($hr_role, 201);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\HrRole  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(HrRole $hr_role)
    {
        return $hr_role->getConnection()->transaction(function () use ($hr_role) {
            $hr_role->staff()->detach();
            $hr_role->brand()->detach();
            $hr_role->department()->detach();
            $hr_role->delete();

            return response()->json(null, 204);
        });
    }
}
