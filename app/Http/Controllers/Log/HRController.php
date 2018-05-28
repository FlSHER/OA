<?php

namespace App\Http\Controllers\Log;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HRController extends Controller {

    public function showStaffLogPage() {
        return view('log.staff_log');
    }

    public function getStaffLogList(Request $request) {
        return app('Plugin')->dataTables($request, 'App\Models\HR\StaffLog');
    }

    public function showViolationLogPage() {
        return view('log.violation_log');
    }

    public function getViolationLogList(Request $request) {
        return app('Plugin')->dataTables($request, 'App\Models\HR\ViolationLog');
    }

}
