<?php

/**
 * 表单提交后的增删改查
 * create by Fisher <fisher9389@sina.com>
 */

namespace App\Contracts;

use App\Contracts\OperationLog as OperationLog;
use DB;

class CURD
{

    protected $model;
    protected $logService;
    protected $builder;
    protected $primaryKey;
    protected $relations = ['HasOne' => [], 'HasMany' => [], 'BelongsTo' => [], 'BelongsToMany' => []];
    protected $dirty = [];

    public function __construct($model = null, $log = null)
    {
        if (empty($model) && !empty($this->model))
            $model = $this->model;
        $model && $this->model($model);
        $log && $this->log($log);
    }

    /**
     * 调用未知方法时操作builder属性,继承QueryBuilder
     * @param type $name
     * @param type $arguments
     * @return \App\Contracts\CURD
     */
    public function __call($name, $arguments)
    {
        call_user_func_array([$this->builder, $name], $arguments);
        return $this;
    }

    /**
     * 关联模型
     * @param type $model
     * @return \App\Contracts\CURD
     */
    public function model($model)
    {
        if (is_object($model)) {
            $modelName = get_class($model);
        } else {
            $modelName = $model;
            $model = new $modelName();
        }
        $this->model = $model;
        $this->builder = $modelName::query();
        $this->primaryKey = $this->builder->getModel()->getKeyName();
        return $this;
    }

    /**
     * 关联日志服务
     * @param OperationLog $log
     * @return \App\Contracts\CURD
     */
    public function log($log = null)
    {
        if ($log instanceof OperationLog) {
            $this->logService = $log;
        } elseif (is_string($log)) {
            $this->logService = new $log();
        } else {
            $this->logService = $log;
        }
        return $this;
    }

    /**
     * 判断是否有关联的日志
     * @return type
     */
    public function hasLog()
    {
        return !empty($this->logService);
    }

    /**
     * 重置 Dirty，LogService
     */
    public function reset()
    {
        $this->dirty = [];
        $this->hasLog() && $this->log(get_class($this->logService));
    }

    /**
     * 创建或更新数据
     * @param type $data
     * @return type
     */
    public function createOrUpdate($data)
    {
        if (array_has($data, $this->primaryKey)) {
            return $this->update($data);
        } else {
            return $this->create($data);
        }
    }

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

    /**
     * 保存数据
     * @param type $model
     * @param type $data
     */
    protected function save($data)
    {
        if (array_has($data, $this->primaryKey) && !empty($data[$this->primaryKey])) {
            $model = $this->newBuilder()->find($data[$this->primaryKey]);
        } else {
            $model = $this->model;
        }
        $this->fillDataAndSave($model, $data);
    }

    /**
     * 保存一条数据
     * @param type $model
     * @param type $data
     * @throws \Illuminate\Database\QueryException
     */
    protected function fillDataAndSave($model, $data)
    {
        $this->reset();
        DB::beginTransaction();
        try {
            $this->getRelation($model, $data);
            $model->fill($data);
            $this->changeBelongsTo($model);
            $this->saving($model, $data); //saving回调
            $this->addDirty($model);
            $model->save();
            $this->saved($model, $data); //saved回调
            $this->fillDataToHasOne($model);
            $this->fillDataToHasMany($model);
            $this->changeBelongsToMany($model);
            $this->afterRelative($model, $data);
            if ($this->isDirty() && $this->hasLog()) {
                $this->logService->model($model)->write($this->dirty, $data);
            }
        } catch (\Exception $err) {
            DB::rollBack();
            throw $err;
        }
        DB::commit();
    }

    /**
     * 获取关联并分类
     * @param type $model
     * @param type $data
     */
    protected function getRelation($model, $data)
    {
        $this->relations = ['HasOne' => [], 'HasMany' => [], 'BelongsTo' => [], 'BelongsToMany' => []];
        foreach ($data as $k => $v) {
            if (method_exists($model, $k) && $model->$k() instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                $relationType = str_replace('Illuminate\Database\Eloquent\Relations\\', '', get_class($model->$k()));
                $this->relations[$relationType][$k] = $v;
            } else {
                continue;
            }
        }
    }

    /**
     * 修改一对一关联属性
     * @param  $model
     */
    protected function fillDataToHasOne($model)
    {
        foreach ($this->relations['HasOne'] as $relation => $data) {
            $relationQuery = $model->$relation();
            $foreignKey = $relationQuery->getPlainForeignKey();
            $parentKey = $relationQuery->getParentKey();
            $relationModel = $relationQuery->firstOrNew([$foreignKey => $parentKey])->fill($data);
            if ($relationModel->isDirty()) {
                $this->addDirty($relationModel, $relation);
                $relationModel->save();
            }
        }
    }

    /**
     * 修改一对多关联属性
     * @param $model
     */
    protected function fillDataToHasMany($model)
    {
        foreach ($this->relations['HasMany'] as $relation => $data) {
            $data = empty($data) ? [] : $data;
            $dirty = ['detached' => [], 'attached' => [], 'updated' => []];
            $relationQuery = $model->$relation();
            $keyName = $relationQuery->getModel()->getKeyName();
            $original = $relationQuery->pluck($keyName)->toArray();
            $order = array_pluck($data, $keyName);

            $attached = array_diff($order, $original);
            $dirty['attached'] = $this->attachHasManyRelations($attached, $relationQuery, $data);

            $updated = array_diff($order, $attached);
            $dirty['updated'] = $this->updateHasManyRelations($updated, $data, $model, $relation);

            $detached = $dirty['detached'] = array_diff($original, $order);
            $this->detachHasManyRelations($detached, $relationQuery);
            if (!empty(array_filter($dirty)))
                $this->dirty[$relation] = $dirty;
        }
    }

    /**
     * 增加一对多关联
     * @param type $attached
     * @param type $relationQuery
     * @param type $data
     * @return type
     */
    protected function attachHasManyRelations($attached, $relationQuery, $data)
    {
        $foreignKey = $relationQuery->getPlainForeignKey();
        $foreignKeyValue = $relationQuery->getParentKey();
        $dirty = [];
        foreach ($attached as $k => $v) {
            $existedModel = $relationQuery->getModel()->newQuery()->find($v);
            if (empty($existedModel)) {
                $data[$k][$foreignKey] = $foreignKeyValue;
                $existedModel = $relationQuery->create($data[$k]);
            } else {
                $existedModel->setAttribute($foreignKey, $foreignKeyValue);
                $existedModel->save();
            }
            $primaryKey = $existedModel->getKey();
            $dirty[$primaryKey] = $data[$k];
        }
        return $dirty;
    }

    /**
     * 更新一对多关联
     * @param type $updated
     * @param type $data
     * @param type $model
     * @param type $relation
     * @return type
     */
    protected function updateHasManyRelations($updated, $data, $model, $relation)
    {
        $dirty = [];
        foreach ($updated as $k => $v) {
            $existedModel = $model->$relation()->find($v)->fill($data[$k]);
            if ($existedModel->isDirty()) {
                $dirty[$v] = $this->getDirtyWithOriginal($existedModel);
                $existedModel->save();
            }
        }
        return $dirty;
    }

    /**
     * 删除/断开一对多关联
     * @param type $detached
     * @param type $relationQuery
     */
    protected function detachHasManyRelations($detached, $relationQuery)
    {
        $foreignKey = $relationQuery->getPlainForeignKey();
        if (is_null($detached)) {
            $relationModels = $relationQuery->get();
        } else {
            $relationModels = $relationQuery->find($detached);
        }
        $relationModels->each(function ($model) use ($foreignKey) {
            try {
                $model->setAttribute($foreignKey, null);
                $model->save();
            } catch (\Illuminate\Database\QueryException $err) {
                $model->setRawAttributes($model->getOriginal());
                $model->delete();
            }
        });
    }

    /**
     * 替换多对一归属
     * @param $model
     */
    protected function changeBelongsTo($model)
    {
        foreach ($this->relations['BelongsTo'] as $relation => $data) {
            $relationQuery = $model->$relation();
            $newBuilder = $relationQuery->getModel()->newQuery();
            $newRelationModel = $newBuilder->where($data)->get();
            if (count($newRelationModel) != 1 && !empty(array_filter($data))) {
                abort(500, '从属关联查询到' . count($newRelationModel) . '个结果，应该为1个');
            } elseif (!empty(array_filter($data))) {
                $relationQuery->associate($newRelationModel->first());
            }
        }
    }

    /**
     * 多对多关联同步
     * @param $model
     */
    protected function changeBelongsToMany($model)
    {
        foreach ($this->relations['BelongsToMany'] as $relation => $data) {
            $data = empty($data) ? [] : $data;
            $relationQuery = $model->$relation();
            $original = $relationQuery->get();
            $otherKey = str_replace($relationQuery->getTable() . '.', '', $relationQuery->getOtherKey());
            $input = [];
            foreach ($data as $v) {
                if (!is_array($v)) {
                    $input[$v] = [];
                } elseif (array_key_exists('pivot', $v)) {
                    $pivot = $v['pivot'];
                    $input[$v['pivot'][$otherKey]] = $pivot;
                } else {
                    $relationModel = $relationQuery->getModel()->newQuery()->where($v)->first();
                    !empty($relationModel) && $input[$relationModel->getKey()] = [];
                }
            }
            $dirty = $relationQuery->sync($input);
            $changed = $relationQuery->get();
            if (!empty(array_filter($dirty))) {
                $this->dirty[$relation] = $this->makeBelongsToManyDirty($dirty, $original, $changed);
            }
        }
    }

    /**
     * 生成多对多Dirty数据
     * @param type $response
     * @param type $original
     * @param type $changed
     * @return type
     */
    protected function makeBelongsToManyDirty($response, $original, $changed)
    {
        $newAttached = [];
        foreach ($response['attached'] as $v) {
            $pivot = $changed->find($v)->pivot;
            $order = $this->getPivotAttribute($pivot);
            $newAttached[$v] = $order;
        }
        $response['attached'] = $newAttached;
        $newDetached = [];
        foreach ($response['detached'] as $v) {
            $pivot = $original->find($v)->pivot;
            $order = $this->getPivotAttribute($pivot);
            $newDetached[$v] = $order;
        }
        $response['detached'] = $newDetached;
        $newUpdated = [];
        foreach ($response['updated'] as $v) {
            $current = $original->find($v)->pivot;
            $order = $changed->find($v)->pivot->toArray();
            $newUpdated[$v] = $this->getDirtyWithOriginal($current->fill($order));
        }
        $response['updated'] = $newUpdated;
        return $response;
    }

    /**
     * 获取中间表的额外字段
     * @param type $pivot
     * @return type
     */
    protected function getPivotAttribute($pivot)
    {
        return array_except($pivot->toArray(), [$pivot->getForeignKey(), $pivot->getOtherKey()]);
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

    /**
     * 删除
     */
    public function delete($data = null, $relations = [])
    {
        $newBuilder = $this->makeDeleteBuilder($data);

        $newBuilder->each(function ($model) use ($data, $relations) {
            DB::beginTransaction();
            try {
                $this->deleting($model, $data); //deleting 回调
                if ($this->hasLog()) {
                    $this->logService->model($model)->write(null, $data);
                }
                $this->deleteRelations($model, $relations);
                $model->delete();
                $this->deleted($model, $data); //deleted 回调
            } catch (\Illuminate\Database\QueryException $err) {
                DB::rollBack();
                throw $err;
            }
            DB::commit();
        });
        return ['status' => 1, 'message' => '删除成功'];
    }

    /**
     * 根据条件生成删除builder
     * @param type $data
     * @return type
     */
    protected function makeDeleteBuilder($data)
    {
        $newBuilder = $this->newBuilder();
        if (is_string($data)) {
            $newBuilder->where($this->primaryKey, $data);
        } elseif (array_has($data, $this->primaryKey)) {
            $newBuilder->where($this->primaryKey, $data[$this->primaryKey]);
        } elseif (is_array($data)) {
            $newBuilder->whereIn($this->primaryKey, $data);
        }
        return $newBuilder;
    }

    /**
     * 删除关联
     * @param type $model
     * @param type $relations
     */
    protected function deleteRelations($model, $relations)
    {
        foreach ($relations as $v) {
            $relationType = str_replace('Illuminate\Database\Eloquent\Relations\\', '', get_class($model->$v()));
            switch ($relationType) {
                case 'BelongsTo':
                    $model->$v->delete();
                    break;
                case 'HasOne':
                case 'HasMany':
                    $this->detachHasManyRelations(null, $model->$v());
                    break;
                case 'BelongsToMany':
                    $model->$v()->sync([]);
                    break;
            }
        }
    }

    /**
     * 返回一个新的Builder，与原Builder条件相同
     * @return type
     */
    protected function newBuilder()
    {
        return clone $this->builder;
    }

    /**
     * 检查模型及其关联是否有Dirty（变动）
     * @return type
     */
    protected function isDirty()
    {
        return !empty($this->dirty);
    }

    /* 继承回调 Start */
    protected function saving($model, $data)
    {

    }

    protected function saved($model, $data)
    {

    }

    protected function afterRelative($model, $data)
    {

    }

    protected function deleting($model, $data)
    {

    }

    protected function deleted($model, $data)
    {

    }
    /* 继承回调 End */
}
