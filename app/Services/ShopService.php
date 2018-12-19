<?php 

namespace App\Services;

use App\Models\Tag;
use App\Models\HR\Staff;
use App\Models\HR\ShopLog;
use App\Models\HR\Shop as ShopModel;
use Illuminate\Support\Facades\Log;

class ShopService
{
    protected $dirty = [];

    protected $primaryKey = 'shop_sn';

    protected $relationMap = [
        'city_id' => 'city.name',
        'brand_id' => 'brand.name',
        'status_id' => 'status.name',
        'county_id' => 'county.name',
        'province_id' => 'province.name',
        'department_id' => 'department.name',
    ];

    /**
     * 数据总线.
     * 
     * @param  array $data
     * @return \App\Models\HR\Shop|error
     */
    public function index(array $data)
    {
        $model = ShopModel::find($data[$this->primaryKey]);
        if ($model) {

            return $this->fillDataAndSave($model, $data);
        } else {
            $shopModel = new ShopModel();

            return $this->fillDataAndSave($shopModel, $data);
        }
    }

    /**
     * 保存一条数据
     * 
     * @param \App\Models\HR\Shop $model
     * @param array   $data
     * @throws \Illuminate\Database\QueryException
     */
    protected function fillDataAndSave(ShopModel $shopModel, array $data)
    {
        $this->dirty = [];
        $block = function () use ($shopModel, $data) {
            $shopModel->fill($data);
            $this->addDirty($shopModel);
            $shopModel->save();
            $this->changeBelongsToMany($shopModel, $data);
            !empty($this->dirty) && $this->recordLog($shopModel);

            return $shopModel;
        };

        return $shopModel->getConnection()->transaction($block);
    }


    /**
     * 多对多关联同步
     * @param $model
     */
    protected function changeBelongsToMany($model, $data)
    {
        if (array_has($data, 'tags')) {
            $tags = $data['tags'] ? : [];
            $relationQuery = $model->tags();
            $original = $relationQuery->pluck('id');
            $changed = ['attached' => [], 'detached' => []];
            if (empty($tags)) {
                $changed['detached'] = $original->all();
            } else {
                $values = collect($tags);
                $diff = $original->diff($values)->values()->all();
                $diffOri = $values->diff($original)->values()->all();
                $changed['attached'] = $diffOri;
                $changed['detached'] = $diff;
            }

            // 同步标签🐎
            $relationQuery->sync($tags);

            if (!empty(array_filter($changed))) {
                $this->dirty['tags'] = $changed;
            }
        }
        if (array_has($data, 'staff')) {
            $staff = $data['staff'] ? : [];
            $relationQuery = $model->staff();
            $original = $relationQuery->pluck('staff_sn');
            $changed = ['attached' => [], 'detached' => []];

            if (empty($staff)) {
                $changed['detached'] = $relationQuery->pluck('staff_sn')->all();
                $relationQuery->update(['shop_sn' => '']);
            } else {
                $values = collect(array_column($staff, 'staff_sn'));
                $diff = $original->diff($values)->values()->all();
                $diffOri = $values->diff($original)->values()->all();
                $changed['attached'] = $diffOri;
                $changed['detached'] = $diff;

                // 更新员工所属店铺
                $relationQuery->update(['shop_sn' => '']);
                Staff::whereIn('staff_sn', $values)->update(['shop_sn' => $model->shop_sn]);
            }

            if (!empty(array_filter($changed))) {
                $this->dirty['staff'] = $changed;
            }
        }
    }


    /**
     * 加入Dirty
     * @param type $model
     * @param type $relation
     */
    protected function addDirty($model, $relation = null)
    {
        $dirty = $this->getDirtyWithOriginal($model);

        if (! empty($relation)) {
            $dirty = [$relation => $dirty];
        }

        $this->dirty = array_collapse([$this->dirty, $dirty]);
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
     * 记录店铺变更记录.
     * 
     * @param  \App\Models\HR\Shop $shop
     * 
     * @return void
     */
    protected function recordLog($shop)
    {
        $model = new ShopLog;
        $model->shop_sn = $shop->shop_sn;
        $model->admin_sn = app('CurrentUser')->getStaffSn();
        $model->changes = $this->trans();
        $model->save();
    }

    /**
     * 翻译店铺变更字段.
     * 
     * @return array
     */
    protected function trans(): array
    {
        $localization = trans('fields.shop');
        $relationMap = $this->relationMap;
        $changes = [];
        foreach ($this->dirty as $key => $change) {
            if (array_has($relationMap, $key)) {
                $relationKey = $relationMap[$key];
                $key = $localization[$relationKey];
                $value = $this->getRelationName($relationKey, $change);
            } elseif ($key === 'staff') {
                $key = $localization[$key];
                $value = array_filter([
                    '添加' => Staff::whereIn('staff_sn', $change['attached'])->pluck('realname')->all(),
                    '删除' => Staff::whereIn('staff_sn', $change['detached'])->pluck('realname')->all(),
                ]);
            } elseif ($key === 'tags') {
                $key = $localization[$key];
                $value = array_filter([
                    '添加' => Tag::whereIn('id', $change['attached'])->pluck('name')->all(),
                    '删除' => Tag::whereIn('id', $change['detached'])->pluck('name')->all(),
                ]);
            } elseif (is_array($change)) {
                $key = $localization[$key];
                $value = array_values($change);
            } else {
                $key = $localization[$key];
                $value = $change;
            }
            $changes[$key] = $value;
        }

        return $changes;
    }

    protected function getRelationName($relationKey, $change)
    {
        $model = new ShopModel;
        $relation = explode('.', $relationKey)[0];
        $values = array_values($change);
        $original = empty($values[0]) ? '' : $model->$relation()->getModel()->find($values[0])->name;
        $attribute = empty($values[1]) ? '' : $model->$relation()->getModel()->find($values[1])->name;

        return [$original, $attribute];
    }

}   