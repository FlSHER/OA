<?php

namespace App\Http\Controllers\Api\Resources;

use App\Models\HR\Staff;
use Illuminate\Http\Request;
use App\Services\StaffService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\HR\StaffResource;
use App\Http\Resources\HR\StaffCollection;

class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        preg_match('/role\.id=(.*?)(;|$)/', $request->filters, $match);
        $roleId = false;
        if ($match) {
            $roleId = is_numeric($match[1]) ? $match[1] : json_decode($match[1], true);
            $newFilters = preg_replace('/role\.id=.*?(;|$)/', '$3', $request->filters);
            $request->offsetSet('filters', $newFilters);
        }
        $list = Staff::when($roleId, function ($query) use ($roleId) {
            $query->whereHas('role', function ($query) use ($roleId) {
                if (is_array($roleId)) {
                    $query->whereIn('id', $roleId);
                } else {
                    $query->where('id', $roleId);
                }
            });
        })
        ->with('relative', 'position', 'department', 'brand', 'shop')
        ->filterByQueryString()
        ->sortByQueryString()
        ->withPagination();

        if (isset($list['data'])) {
            $list['data'] = new StaffCollection(collect($list['data']));

            return $list;
        } else {
            return new StaffCollection($list);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStaffRequest $request)
    {
        $data = $request->all();

        $curd = $this->staffService->create($data);

        if ($curd['status'] == 1) {
            $staff = Staff::query()
                ->with(['relative', 'position', 'department', 'brand', 'shop'])
                ->orderBy('staff_sn', 'desc')
                ->first();

            return response()->json(new StaffResource($staff), 201);
        }

        return response()->json($curd, 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $data = $request->all();

        $curd = $this->staffService->update($data);

        if ($curd['status'] == 1) {
            $result = $staff->where('staff_sn', $staff->staff_sn)->first();
            $result->load(['relative', 'position', 'department', 'brand', 'shop']);

            return response()->json(new StaffResource($result), 201);
        }

        return response()->json($curd, 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        $staff->load(['relative', 'position', 'department', 'brand', 'shop']);

        return new StaffResource($staff);
    }

}
