<?php

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\Staff;
use App\Models\HR\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HR\PositionCollection;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Position::query()
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
            
        if (isset($list['data'])) {
            $list['data'] = new PositionCollection($list['data']);
            
            return $list;
        }

        return new PositionCollection($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Position $position)
    {
        $rules = [
            'name' => 'required|unique:positions',
            'level' => 'required|integer|max:100',
        ];
        $message = [
            'name.required' => '职位名称不能为空',
            'name.unique' => '职位名称已存在',
            'level.required' => '职级不能为空',
            'level.max' => '职级不能大于 :max',
        ]; 
        $this->validate($request, $rules, $message);
        $data = $request->all();
        $position->name = $data['name'];
        $position->level = $data['level'];
        $position->is_public = $data['is_public'];
        
        $position->getConnection()->transaction(function () use ($position, $data) {
            $position->save();
            $position->brand()->attach($data['brands']);
        });

        $position->load('brand');
        $position->brands = $position->brand;
        
        return response()->json($position, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Position $position
     * @return \Illuminate\Http\Response
     */
    public function show(Position $position)
    {
        $position->load('brand');

        return response()->json($position, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Position $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        $rules = [
            'name' => 'required',
            'level' => 'required',
        ];
        $message = [
            'name.required' => '职位名称不能为空',
            'level.required' => '职级不能为空', 
        ];
        $this->validate($request, $rules, $message);
        $data = $request->all();
        $position->name = $data['name'];
        $position->level = $data['level'];
        $position->is_public = $data['is_public'];
        
        $position->getConnection()->transaction(function () use ($position, $data) {
            $position->save();
            $position->brand()->detach();
            $position->brand()->attach($data['brands']);
        });
        
        $position->load('brand');
        $position->brands = $position->brand;
        
        return response()->json($position, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Position $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        $hasStaff = $position->staffs->isNotEmpty();
        if ($hasStaff) {
            return response()->json(['message' => '有在职员工使用的职位不能删除'], 422);
        }
        $position->getConnection()->transaction(function () use ($position) {
            $position->brand()->detach();
            $position->delete();
        });

        return response()->json(null, 204);
    }
}
