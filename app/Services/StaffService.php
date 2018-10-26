<?php 

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\HR\Staff as StaffModel;
use Illuminate\Http\Exception\HttpResponseException;
use App\Services\Tools\OperationLogs\StaffOperationLogService;

class StaffService
{
    protected $dirty = [];

    /**
     * 创建
     * @param type $data
     * @return typeg
     */
    public function create($data)
    {
        $this->save($data);
        return ['status' => 1, 'message' => '添加成功'];
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function update($data)
    {
        $this->save($data);
        if ($this->isDirty()) {
            return ['status' => 1, 'message' => '编辑成功'];
        } else {
            return ['status' => -1, 'message' => '未发现改动'];
        }
    }

    public function save(array $data)
    {
        if (isset($data['staff_sn']) && !empty($data['staff_sn'])) {
            $model = StaffModel::find($data['staff_sn']);
        } else {
            $model = new StaffModel();
        }
        $this->fillDataAndSave($model, $data);
    }

    /**
     * 保存一条数据
     * 
     * @param type $model
     * @param type $data
     * @throws \Illuminate\Database\QueryException
     */
    protected function fillDataAndSave($model, $data)
    {
        
        $this->reset();
        \DB::beginTransaction();
        try {
            $this->setRelation($model, $data);
            $model->fill($data);
            $this->saving($model, $data); //前置操作
            $this->addDirty($model);
            if (!$this->hasTransfer($model, $data)) { //没有预约操作才走更新
                $model->save();
                $this->saved($model, $data); //后置操作
                if ($this->isDirty()) {
                    $log = new StaffOperationLogService();
                    $log->model($model)->write($this->dirty, $data);
                }
            } else {
                $this->appendOperationType($model, $data);
            }
            \DB::commit();
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            \DB::rollBack();
            throw $err;
        }
    }

    /**
     * 记录更详细的变更类型.
     * 
     * @param  [type] $model
     * @param  array $data
     */
    public function appendOperationType($model, $data)
    {
        $type = $data['operation_type'];
        if (in_array($type, ['transfer', 'import_transfer'])
            && array_has($this->dirty, 'position_id')
        ) { // 职位变更 默认为升职 降职未考虑
            $newData = array_merge($data, [
                'operation_type' => 'rise_position',
            ]);

            $log = new StaffOperationLogService();
            $log->model($model)->write($this->dirty, $newData);
        }
        
    }

    /**
     * 是否有可预约操作。
     * 
     * @param  [type]  $model
     * @param  [type]  $data
     * @return boolean
     */
    protected function hasTransfer($model, $data)
    {
        $operationType = $data['operation_type'];

        if (in_array($operationType, ['transfer', 'import_transfer']) 
            && strtotime($data['operate_at']) > time()
            && empty($model->tmp)
        ) {
            return true;
        }

        return false;
    }

    /**
     * 更新关系关联
     * @param App\Models\HR\Staff $model
     * @param array $data
     */
    protected function setRelation($model, $data)
    {   
        if (array_has($data, 'relative')) {
            $model->relative()->sync($data['relative']);
        }

        if (array_has($data, 'cost_brands')) {
            $model->cost_brands()->sync($data['cost_brands']);
        }
    }

    public function saving($model, array $data)
    {
        // 操作设置
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
        // 如果是离职操作并且跳过了离职交接 修改状态为已离职
        if ($data['operation_type'] == 'leave' && array_has($data, 'skip_leaving') && $data['skip_leaving']) {
            $data['operation_type'] = 'leaving';
            $this->save($data);
        }
    }

    /**
     * 设置离职记录。
     * 
     * @param [type] $model
     */
    private function setLeaving($model)
    {
        $model->leaving()->create([
            'staff_sn' => $model->staff_sn,
            'original_status_id' => $model->status_id,
        ]);
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


    /**
     * 加入Dirty
     * @param type $model
     * @param type $relation
     */
    protected function addDirty($model, $relation = null)
    {
        if (empty($relation)) {
            $newDirty = $this->getDirtyWithOriginal($model);
        } else {
            $newDirty = [$relation => $this->getDirtyWithOriginal($model)];
        }
        $this->dirty = array_collapse([$this->dirty, $newDirty]);
    }

    /**
     * 重置 Dirty，LogService
     */
    public function reset()
    {
        $this->dirty = [];
    }

    /**
     * 检查模型及其关联是否有Dirty（变动）
     * @return type
     */
    protected function isDirty()
    {
        return !empty($this->dirty);
    }

    /**
     * 获取带有原值的Dirty
     * @param type $model
     * @return type
     */
    protected function getDirtyWithOriginal($model)
    {
        $dirty = [];
        foreach ($model->getDirty() as $key => $value) {
            $dirty[$key] = [
                'original' => $model->getOriginal($key, ''),
                'dirty' => $value,
            ];
        }
        return $dirty;
    }

    private function operating($model, array $data)
    {
        $operateAt = $data['operate_at'];
        $operationType = $data['operation_type'];

        switch ($operationType) {
            case 'entry':
                $model->setAttribute('hired_at', $operateAt);
                break;
            case 'reinstate':
                $model->setAttribute('hired_at', $operateAt);
                $model->setAttribute('employed_at', null);
                $model->setAttribute('left_at', null);
                $model->setAttribute('is_active', 1);
                break;
            case 'leave':
                $model->setAttribute('left_at', $operateAt);
                break;
            case 'leaving':
                $model->setAttribute('is_active', 0);
                break;
        }
        if (empty($model->employed_at) && $model->status_id > 1) {
            $model->setAttribute('employed_at', $operateAt);
        }
    }

}   