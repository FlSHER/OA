<?php

/**
 * 操作日志类
 * create by Fisher 2017/1/14 <fisher9389@sina.com>
 */

namespace App\Services\Tools\OperationLogs;

use App\Contracts\OperationLog;

class StaffOperationLogService extends OperationLog
{

    protected $table = 'staff_log';
    protected $relationMap = [
        'gender_id' => 'gender.name',
        'national_id' => 'national.name',
        'politics_id' => 'politics.name',
        'department_id' => 'department.full_name',
        'position_id' => 'position.name',
        'brand_id' => 'brand.name',
        'status_id' => 'status.name',
        'household_province_id' => 'household_province.name',
        'household_city_id' => 'household_city.name',
        'household_county_id' => 'household_county.name',
        'living_province_id' => 'living_province.name',
        'living_city_id' => 'living_city.name',
        'living_county_id' => 'living_county.name',
    ];

    public function __construct($tableName = null, \Illuminate\Database\Eloquent\Model $model = null)
    {
        parent::__construct($tableName, $model);
        $this->trans('fields.staff');
    }

    protected function makeBasicInfo($others)
    {
        $data = parent::makeBasicInfo($others);
        $data['staff_sn'] = $this->model->staff_sn;
        $data['operation_type'] = $others['operation_type'];
        $data['operation_remark'] = $others['operation_remark'];
        $data['operate_at'] = $others['operate_at'];
        return $data;
    }

}
