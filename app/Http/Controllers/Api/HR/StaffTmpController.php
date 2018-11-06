<?php 

namespace App\Http\Controllers\Api\HR;

use App\Models\HR\Staff;
use App\Models\HR\StaffTmp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffTmpController extends Controller
{
	
    /**
     * get all records for staff。
     * 
     * @param  Staff  $staff
     * @return mixed
     */
    public function index(Staff $staff)
    {
        $list = $staff->tmp()->with(['staff', 'admin'])->get();

        return response()->json($list, 200);
    }

    /**
     * get a single record.
     * 
     * @param  Staff  $staff
     * @return mixed
     */
    public function show(StaffTmp $tmp)
    {
        $tmp->load(['staff', 'admin']);

        return response()->json($tmp, 200);
    }

    /**
     * restore a sigle record.
     * 
     * @param  StaffTmp $tmp
     * @return mixed
     */
    public function restore(StaffTmp $tmp)
    {
        abort_if($tmp->status !== 1, 422, '禁止还原');

        $tmp->status = 2;
        $tmp->getConnection()->transaction(function () use ($tmp) {
            $tmp->save();

            // 解锁下一条被锁定的记录.
            if ($nextTmp = StaffTmp::byLock()->oldest('operate_at')->first()) {
            	$nextTmp->status = 1;
            	$nextTmp->save();
            }
        });
        $tmp->load(['staff', 'admin']);

        return response()->json($tmp, 201);
    }
}