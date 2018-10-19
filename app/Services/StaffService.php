<?php 

namespace App\Services;

use App\Models\HR\Staff as StaffModel;
use Illuminate\Http\Exception\HttpResponseException;

class StaffService
{
	protected $dirty;

	protected $model;

	protected $attributes = null;


    public function beforAction(array $data)
	{
		$model = $this->makeModel($data);
		// 操作设置
		$this->operating($model, $data);
		$operationType = $data['operation_type'];
		if ($operationType == 'leave' && $model->status_id != -2) {
            $this->setLeaving($model);
        } elseif (in_array($operationType, ['transfer', 'import_transfer']) && strtotime($data['operate_at']) > time()) {
            $this->transferLater($model, $data);
        }
		
	}

	private function makeModel(array $data)
	{
		if (isset($data['staff_sn']) && !empty($data['staff_sn'])) {
			$model = StaffModel::find($data['staff_sn']);
        } else {
            $model = new StaffModel();
        }
        $model->fill($data);

        return $model;
	}

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

	public function makeFillData(array $fillable)
	{

	}

	
}	