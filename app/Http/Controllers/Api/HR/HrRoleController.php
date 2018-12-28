<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\HrRole;
use Illuminate\Http\Request;
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
        $list = HrRole::with('staff', 'brand', 'department')
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
