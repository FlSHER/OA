<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Position;
use App\Models\I\District;
use App\Services\ViolationService;
use DB;

/**
 * Description of HRMService
 *
 * @author Fisher
 */
class HRMService {

    use ViolationService;

    /**
     * 获取部门选择option标签
     * @param int $parentId
     * @return string
     */
    public function getDepartmentOptionsById($parentId = 0) {
        $departments = Department::where([['parent_id', '=', $parentId]])->orderBy('sort', 'asc')->get();
        return $this->changeDataIntoOptions($departments);
    }

    /**
     * 获取职位选择option标签
     * @param int/array $brandId
     * @return string
     */
    public function getPositionOptionsByBrandId($brandId = 0) {
        if (is_array($brandId)) {
            $positions = Position::whereHas('brand', function($query)use($brandId) {
                        $query->whereIn('id', $brandId);
                    })->orWhere('is_public', true);
        } else if ($brandId != 0) {
            $positions = Position::whereHas('brand', function($query)use($brandId) {
                        $query->where('id', '=', $brandId);
                    })->orWhere('is_public', true);
        } else {
            $positions = new Position;
        }
        $positions = $positions->orderBy('level', 'asc')->orderBy('sort', 'asc')->get();
        return $this->changeDataIntoOptions($positions);
    }

    public function getDistrictOptions($parentId = 'province') {
        if ($parentId == '0') {
            $districts = [];
        } else {
            $parentId = $parentId == 'province' ? 0 : $parentId;
            $districts = District::where("parent_id", $parentId)->get();
        }
        return '<option value="0">-- 无 --</option>' . $this->changeDataIntoOptions($districts);
    }

    /**
     * 获取附表option标签
     * @return sting
     */
    public function getOptions($model, $where = [], $order = 'sort') {
        $table = is_string($model) ? DB::table($model) : $model;
        $data = $table->where($where)->orderBy($order, 'asc')->get();
        return $this->changeDataIntoOptions($data);
    }

    /**
     * 获取属性的option标签
     * @param Eloquent $data
     * @return string
     */
    private function changeDataIntoOptions($data) {
        $html = '';
        foreach ($data as $v) {
            if (empty($v->option)) {
                $html .= '<option value="' . $v->id . '">' . $v->name . '</option>';
            } else {
                $html .= $v->option;
            }
        }
        return $html;
    }

    public function getCheckBox($name, $model, $where = [], $order = 'sort') {
        $table = is_string($model) ? DB::table($model) : $model;
        $data = $table->where($where)->orderBy($order, 'asc')->get();
        return $this->changeDataIntoCheckBox($data, $name);
    }

    private function changeDataIntoCheckBox($data, $name) {
        $html = '';
        foreach ($data as $v) {
            $html .= '<div class="col-sm-3 col-xs-6" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-left:0;">
            <label class="frame check frame-sm" unselectable="on" onselectstart="return false;">
                <input name="' . $name . '" type="checkbox" value="' . $v->id . '">
                <span class="checkbox-outer"><i class="fa fa-check"></i></span>&nbsp;
            </label>
            <span>' . $v->name . '</span>
        </div>';
        }
        return $html;
    }

}
