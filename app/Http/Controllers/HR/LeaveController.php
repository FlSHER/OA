<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LeaveController extends Controller {

    protected $model = 'App\Models\HR\Attendance\Vacate';

    public function showManagePage() {
        return view('hr.attendance.leave');
    }

    public function getList(Request $request) {
        return app('Plugin')->dataTables($request, $this->model);
    }

    public function getInfo(Request $request) {
        $id = $request->id;
        $model = $this->model;
        return $model::find($id);
    }

    
}
