<?php

namespace App\Services\App;

/**
 * 审核类
 * Description of AuditService
 *
 * @author admin
 */
use App\Models\Reimburse\Auditor;
use App\Models\Reimburse\ReimDepartment;
use App\Models\Reimburse\Reimbursement;
use DB;
use Illuminate\Http\Request;

class AttendanceService
{
    protected $midNight = '04:00';

    public function getAttendanceDate($format = 'Y-m-d', $date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d H:i:s');
        }
        $timestamp = date('H:i', strtotime($date)) >= $this->midNight ? strtotime($date) : strtotime($date . ' -1 day');
        return date($format, $timestamp);
    }
} 
