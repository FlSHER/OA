<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\Authority;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RbacCollection;

class RbacController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Authority::query()
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
        
        if (isset($list['data'])) {
            $list['data'] = new RbacCollection($list['data']);

            return $list;
        }
        return new RbacCollection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Authority $authority)
    {
        $rules = [
            'auth_name' => 'required|string|max:20',
        ];
        $message = [
            'auth_name.required' => '权限名称不能为空',
            'auth_name.max' => '权限名称不能超过 :max 个字',
        ];
        $this->validate($request, $rules, $message);
        $authority->fill($request->all());
        $authority->is_lock = $request->is_lock ?: 0;
        $authority->is_public = $request->is_public ?: 0;
        $authority->save();

        return response()->json($authority, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Authority $authority)
    {
        $rules = [
            'auth_name' => 'required|string|max:20',
        ];
        $message = [
            'auth_name.required' => '权限名称不能为空',
            'auth_name.max' => '权限名称不能超过 :max 个字',
        ];
        $this->validate($request, $rules, $message);
        $authority->fill($request->all());
        $authority->save();

        return response()->json($authority, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Authority $authority)
    {
        $authority->delete();

        return response()->json(null, 204);
    }

}