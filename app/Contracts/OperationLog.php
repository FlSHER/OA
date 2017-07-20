<?php

/**
 * 操作日志基类
 * create by Fisher 2017/6/16 <fisher9389@sina.com>
 */

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use DB;

class OperationLog {

    protected $table;
    protected $model;
    protected $localization = [];
    protected $relationMap = [];

    public function __construct($tableName = null, Model $model = null) {
        $tableName && $this->table($tableName);
        $model && $this->model($model);
    }

    /**
     * 配置关联映射
     * @param type $relationMap
     */
    public function map($relationMap) {
        $this->relationMap = $relationMap;
    }

    /**
     * 判断是否存在对应字段的关联映射
     * @param type $field
     * @return type
     */
    protected function relationHas($field) {
        return $this->hasMap() && array_has($this->relationMap, $field);
    }

    /**
     * 判断是否配置了关联映射
     * @return type
     */
    protected function hasMap() {
        return !empty($this->relationMap);
    }

    /**
     * 配置字段名称本地化
     * @param type $transPath
     * @return \App\Contracts\OperationLog
     */
    public function trans($transPath) {
        if (is_array($transPath)) {
            foreach ($transPath as $v) {
                $trans = array_dot(trans($v));
                $this->localization = array_collapse([$this->localization, $trans]);
            }
        } elseif (is_string($transPath)) {
            $this->localization = array_dot(trans($transPath));
        }
        return $this;
    }

    public function table($tableName) {
        $this->table = $tableName;
        return $this;
    }

    public function model(Model $model) {
        $this->model = $model;
        return $this;
    }

    public function write($dirty, $others = []) {
        $data = $this->makeBasicInfo($others);
        if (!empty($dirty)) {
            $changes = $this->makeChanges($dirty);
        } else {
            $changes = [];
        }
        $data['changes'] = json_encode($changes);
        $this->getTable()->insert($data);
    }

    protected function getTable() {
        if (class_exists($this->table)) {
            $model = new $this->table();
            return $model->newQuery();
        } else {
            return DB::table($this->table);
        }
    }

    protected function makeChanges($dirty, $parentsKey = '') {
        $changes = [];
        foreach ($dirty as $key => $value) {
            if ($this->relationHas($parentsKey . $key)) {
                list($key, $value) = $this->setRelationWithMap($parentsKey . $key, $value);
            }
            if (is_array($value) && array_keys($value) == ['original', 'dirty']) {
                $change = [$value['original'], $value['dirty']];
            } elseif (is_array($value) && array_keys($value) == ['attached', 'detached', 'updated']) {
                $newChanges = $this->makeBelongsToManyChanges($value, $key);
                $changes = array_collapse([$changes, $newChanges]);
                continue;
            } elseif (is_array($value)) {
                $newChanges = $this->makeChanges($value, $parentsKey . $key . '.');
                $changes = array_collapse([$changes, $newChanges]);
                continue;
            } else {
                $change = $value;
            }
            $realKey = $this->getRealKey($parentsKey . $key);
            $changes[$realKey] = $change;
        }
        return $changes;
    }

    protected function makeBasicInfo($others) {
        $data = [
            'admin_sn' => app('CurrentUser')->getStaffSn(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'operation_type' => $others['operation_type'],
            'operation_remark' => $others['operation_remark'],
            'operate_at' => $others['operate_at'],
        ];
        return $data;
    }

    protected function setRelationWithMap($originalKey, $originalValue) {
        $relations = explode('.', $originalKey);
        $originalField = array_pop($relations);
        $relationKey = $this->relationMap[$originalKey];
        $modelName = get_class($this->model);
        $model = new $this->model;
        foreach ($relations as $relation) {
            $model = $model->$relation()->getModel();
            $relationKey = str_replace($relation . '.', '', $relationKey);
        }
        $original = $originalValue['original'] === null ? '' : clone $model->fill([$originalField => $originalValue['original']]);
        $dirty = $originalValue['dirty'] === null ? '' : clone $model->fill([$originalField => $originalValue['dirty']]);
        foreach (explode('.', $relationKey) as $v) {
            $original = empty($original) ? '' : $original->$v;
            $dirty = empty($dirty) ? '' : $dirty->$v;
        }
        $relationValue = [
            'original' => $original,
            'dirty' => $dirty,
        ];
        return [$relationKey, $relationValue];
    }

    protected function makeBelongsToManyChanges($dirty, $relation) {
        $realKey = $this->getRealKey($relation);
        $changes = [];
        foreach ($dirty['attached'] as $key => $value) {
            foreach ($value as $k => $v) {
                $attributeName = $this->transPivotAttributeName($k, $relation);
                $changes["$realKey.$key"][$attributeName] = ['', $v];
            }
        }
        foreach ($dirty['detached'] as $key => $value) {
            foreach ($value as $k => $v) {
                $attributeName = $this->transPivotAttributeName($k, $relation);
                $changes["$realKey.$key"][$attributeName] = [$v, ''];
            }
        }
        foreach ($dirty['updated'] as $key => $value) {
            foreach ($value as $k => $v) {
                $attributeName = $this->transPivotAttributeName($k, $relation);
                $changes["$realKey.$key"][$attributeName] = [$v['original'], $v['dirty']];
            }
        }
        return $changes;
    }

    protected function transPivotAttributeName($k, $relation) {
        return $this->getRealKey($relation . '.*.pivot.' . $k);
    }

    protected function getRealKey($key) {
        $realKey = array_get($this->localization, $key, $key);
        if (is_array($realKey) && array_has($realKey, 'name')) {
            return $realKey['name'];
        } elseif (is_string($realKey)) {
            return $realKey;
        } else {
            return $key;
        }
    }

}
