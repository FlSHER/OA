<?php

/**
 * 表单提交后的增删改查
 * create by Fisher <fisher9389@sina.com>
 */

namespace App\Services\Tools\CURDs;

use App\Contracts\CURD;
use App\Models\HR\Staff;
use App\Models\HR\Attendance\WorkingSchedule;
use Illuminate\Http\Exception\HttpResponseException;

class TransferCurdService extends CURD
{

    protected $model = 'App\Models\HR\Attendance\StaffTransfer';

    protected function saving($model, $data)
    {
        if ($model->status == 1 && $model->isDirty('arriving_shop_sn')) {
            $originalShopSn = $model->getOriginal('arriving_shop_sn');
            $newShopSn = $model->getAttribute('arriving_shop_sn');
            $staffSn = $model->staff_sn;
            Staff::find($staffSn)->update(['shop_sn' => $newShopSn]);
            WorkingSchedule::where('staff_sn', $staffSn)
                ->where('shop_sn', $originalShopSn)
                ->update(['shop_sn' => $newShopSn]);
        }
    }


}
