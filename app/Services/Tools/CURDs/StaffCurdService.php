<?php

/**
 * 表单提交后的增删改查
 * create by Fisher <fisher9389@sina.com>
 */

namespace App\Services\Tools\CURDs;

use App\Contracts\CURD;
use Illuminate\Http\Exception\HttpResponseException;

class StaffCurdService extends CURD
{

    protected $model = 'App\Models\HR\Staff';

    protected function saving($model, $data)
    {
        $this->operating($model, $data);
        $operationType = $data['operation_type'];
        if ($operationType == 'leave' && $model->status_id != -2) {
            $this->setLeaving($model);
        } elseif (in_array($operationType, ['transfer', 'import_transfer']) && strtotime($data['operate_at']) > time()) {
            $this->transferLater($model, $data);
        }
    }

    protected function saved($model, $data)
    {
        if ($data['operation_type'] == 'leave' && array_has($data, 'skip_leaving') && $data['skip_leaving']) {
            $data['operation_type'] = 'leaving';
            $this->save($data);
        }
    }

    protected function afterRelative($model, $data)
    {
        if (array_has($this->dirty, 'relative')) {
            $this->setAntiRelative($model);
        }
    }

    private function operating($model, $data)
    {
        $operationType = $data['operation_type'];
        $operateAt = $data['operate_at'];
        switch ($operationType) {
            case 'import_entry':
            case 'entry':
                $model->setAttribute('hired_at', $operateAt);
                break;
            case 'reinstate':
                $model->setAttribute('hired_at', $operateAt);
                $model->setAttribute('employed_at', null);
                $model->setAttribute('left_at', null);
                break;
            case'leave':
                $model->setAttribute('left_at', $operateAt);
                break;
        }
        if (empty($model->employed_at) && $model->status_id > 1) {
            $model->setAttribute('employed_at ', $operateAt);
        }
    }

    private function setLeaving($model)
    {
        $model->leaving()->create(['staff_sn' => $model->staff_sn, 'original_status_id' => $model->status_id]);
        $model->setAttribute('status_id', 0);
    }

    private function transferLater($model, $data)
    {
        if (!empty($model->tmp)) {
            throw new HttpResponseException(response('该员工有未执行操作', 403));
        } else {
            $tmpData = $model->getDirty();
            $tmpData['operate_at'] = $data['operate_at'];
            $model->tmp()->create($tmpData);
            $this->addDirty($model);
            $model->setRawAttributes($model->getOriginal());
        }
    }

    private function setAntiRelative($model)
    {
        $antiRelative = [];
        foreach ($model->relative as $value) {
            $type = app('db')->table('staff_relative_type')->find($value['pivot']['relative_type']);
            $oppositeType = app('db')->table('staff_relative_type')
                ->where([['group_id', '=', $type->opposite_group_id]])
                ->where(function ($query) use ($model) {
                    $query->where('gender_id', $model->gender_id)
                        ->orWhere('gender_id', 0);
                })->value('id');
            $antiRelative[$value['relative_sn']] = [
                'relative_name' => $model->realname,
                'relative_type' => $oppositeType
            ];
        }
        $model->anti_relative()->sync($antiRelative);
    }

}
