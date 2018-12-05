<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class StaffLog extends Model {

    protected $table = 'staff_log';
    protected $fillable = ['staff_sn', 'admin_sn', 'operation_type', 'operation_remark', 'operate_at', 'changes'];
    protected $casts = ['changes' => 'array'];

    /**
     * 操作名称本地化
     * @var array 
     */
    protected $typeLocalization;

    /**
     * 字段名本地化
     * @var array 
     */
    protected $columnLocalization;

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->columnLocalization = trans('fields.staff');
        $this->typeLocalization = trans('fields.staff.operation_type');
    }

    /* ----- 定义关联Start ----- */

    public function staff() { //员工
        return $this->belongsTo('App\Models\HR\Staff', 'staff_sn', 'staff_sn')->select('staff_sn', 'realname')->withTrashed();
    }

    public function admin() { //操作人员
        return $this->belongsTo('App\Models\HR\Staff', 'admin_sn', 'staff_sn')->select('staff_sn', 'realname')->withTrashed();
    }

    /* ----- 定义关联End ----- */

    /* ----- 访问器Start ----- */

    public function getOperationTypeAttribute($value) {
        return array_has($this->typeLocalization, $value) ? $this->typeLocalization[$value] : $value;
    }

    public function getChangesAttribute($value) {
        $value = json_decode($value, true);
        $data = [];
        foreach ($value as $k => $v) {
            $key = isset($this->columnLocalization[$k]) ? $this->columnLocalization[$k] : $k;
            $data[$key] = $v;
        }
        return $data;
    }

    /* ----- 访问器End ----- */

    /* ----- 修改器Start ----- */



    /* ----- 修改器End ----- */
}
