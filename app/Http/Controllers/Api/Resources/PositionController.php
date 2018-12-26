<?php

namespace App\Http\Controllers\Api\Resources;

use App\Models\HR\Staff;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PositionRequest;
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
    public function store(PositionRequest $request, Position $position)
    {
        $data = $request->all();
        $position->fill($data);
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
     * @param  \App\Models\Position $position
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
     * @param  \App\Models\Position $position
     * @return \Illuminate\Http\Response
     */
    public function update(PositionRequest $request, Position $position)
    {
        $data = $request->all();
        $position->fill($data);
        $position->getConnection()->transaction(function () use ($position, $data) {
            $position->save();
            $position->brand()->sync($data['brands']);
        });
        
        $position->load('brand');
        $position->brands = $position->brand;
        
        return response()->json($position, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Position $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        if ($position->staff->isNotEmpty()) {
            return response()->json(['message' => '有在职员工使用的职位不能删除'], 422);
        }
        $position->getConnection()->transaction(function () use ($position) {
            $position->brand()->detach();
            $position->delete();
        });

        return response()->json(null, 204);
    }
}