<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\HR\Staff;
use Illuminate\Http\Exception\HttpResponseException;

class StaffRequest extends FormRequest
{

    protected $validator;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $staffSn = $this->staff_sn;
        $staff = Staff::visible()->find($staffSn);
        $authority = app('Authority');
        if (!empty($staffSn) && empty($staff)) {
            return false;
        }
        $departmentId = $this->department_id;
        if (!empty($departmentId) && !$authority->checkDepartment($departmentId)) {
            return false;
        }
        $brandId = $this->brand_id;
        if (!empty($brandId) && !$authority->checkBrand($brandId)) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->setValidator($this->operation_type);
        return $this->validator;
    }

    public function attributes()
    {
        return array_dot(trans('fields.staff'));
    }

    /**
     * 配置表单验证
     * @param string $operationType 操作类型
     */
    private function setValidator($operationType)
    {
        if (preg_match('/import_/', $operationType)) {
            $this->makeImportValidator($operationType);
        } else {
            $this->makeFormValidator($operationType);
        }
    }

    /**
     * 生成导入验证规则
     * @param string $operationType
     */
    private function makeImportValidator($operationType)
    {
        $this->makeBasicValidator($operationType);
        $importValidator = [];
        if (!$this->operationMatch($operationType, ['edit'])) {
            $importValidator['status'] = ['required', 'exists:staff_status,name'];
        }
        $importValidatorOrganic = [];
        $importValidatorPersonal = [];
        if ($this->operationMatch($operationType, ['entry', 'reinstate', 'transfer'])) {
            $importValidatorOrganic = [
                'brand.name' => ['required', 'exists:brands,name'],
                'cost_brands' => ['required', 'array'],
                'cost_brands.*.name' => ['exists:cost_brands,name'],
                'department.full_name' => ['required', 'exists:departments,full_name,deleted_at,NULL'],
                'position.name' => ['required', 'exists:positions,name,deleted_at,NULL'],
            ];
            if ($this->department['full_name'] != '未分配' || $this->position['name'] != '未分配') {
                $formValidatorOrganic['department.full_name'][] = 'not_in:未分配';
                $formValidatorOrganic['position.name'][] = 'not_in:未分配';
            }
        }
        if ($this->operationMatch($operationType, ['edit', 'entry', 'reinstate'])) {
            $importValidatorPersonal = [
                'gender.name' => ['required', 'exists:i_gender,name'],
                'household_province_id' => ['exists:i_district,name,level,1'],
                'household_city_id' => ['exists:i_district,name,level,2'],
                'household_county_id' => ['exists:i_district,name,level,3'],
                'living_province_id' => ['exists:i_district,name,level,1'],
                'living_city_id' => ['exists:i_district,name,level,2'],
                'living_county_id' => ['exists:i_district,name,level,3'],
            ];
        }
        $this->validator = array_collapse([$this->validator, $importValidator, $importValidatorOrganic, $importValidatorPersonal]);
    }

    /**
     * 生成表单提交验证规则
     * @param string $operationType
     */
    private function makeFormValidator($operationType)
    {
        $this->makeBasicValidator($operationType);
        $formValidator = [];
        $formValidatorOrganic = [];
        $formValidatorPersonal = [];
        if (!$this->operationMatch($operationType, ['edit', 'active'])) {
            $formValidator['status_id'] = ['required', 'exists:staff_status,id'];
        }
        if ($this->operationMatch($operationType, ['entry', 'reinstate', 'transfer'])) {
            $formValidator['status_id'][] = 'min:1';
            $formValidatorOrganic = [
                'brand_id' => ['required', 'integer', 'exists:brands,id'],
                'cost_brands' => ['array'],
                'cost_brands.*.id' => ['exists:cost_brands,id'],
                'department_id' => ['required', 'integer', 'exists:departments,id,deleted_at,NULL'],
                'position_id' => ['required', 'integer', 'exists:positions,id,deleted_at,NULL'],
            ];
            if ($this->department_id > 1 || $this->position_id > 1) {
                $formValidatorOrganic['department_id'][] = 'min:2';
                $formValidatorOrganic['position_id'][] = 'min:2';
            }
        }
        if ($this->operationMatch($operationType, ['edit', 'entry', 'reinstate'])) {
            $formValidatorPersonal = [
                'gender' => ['required', 'exists:i_gender,name'],
                'household_province_id' => empty($this->household_province_id) ? [] : ['exists:i_district,id,level,1'],
                'household_city_id' => empty($this->household_city_id) ? [] : ['exists:i_district,id,level,2'],
                'household_county_id' => empty($this->household_county_id) ? [] : ['exists:i_district,id,level,3'],
                'living_province_id' => empty($this->living_province_id) ? [] : ['exists:i_district,id,level,1'],
                'living_city_id' => empty($this->living_city_id) ? [] : ['exists:i_district,id,level,2'],
                'living_county_id' => empty($this->living_county_id) ? [] : ['exists:i_district,id,level,3'],
                'relative.*.pivot.relative_sn' => ['required_with:relative.*.pivot.relative_name'],
                'relative.*.pivot.relative_type' => ['required'],
                'relative.*.pivot.relative_name' => ['required'],
            ];
        }
        if ($this->operationMatch($operationType, ['edit'])) {
            $formValidatorPersonal['relative.*.pivot.relative_sn'][] = 'different:staff_sn';
        }
        $this->validator = array_collapse([$this->validator, $formValidator, $formValidatorOrganic, $formValidatorPersonal]);
    }

    /**
     * 生成基本验证规则
     * @param type $operationType
     */
    private function makeBasicValidator($operationType)
    {
        $this->validator = [
            'staff_sn' => ['exists:staff,staff_sn,deleted_at,NULL'],
            'shop_sn' => empty($this->shop_sn) ? [] : ['nullable', 'exists:shops,shop_sn,deleted_at,NULL'],
            'operate_at' => ['date', 'after:2000-1-1', 'before:2038-1-1'],
            'operation_remark' => ['max:100'],
        ];
        if ($this->operationMatch($operationType, ['edit', 'entry', 'reinstate'])) {
            $this->addPersonalValidator();
        } else {
            $this->confirmIdentityValidator();
        }
        //时间验证
        if ($this->operationMatch($operationType, ['entry', 'reinstate', 'employ', 'leave', 'transfer'])) {
            $this->validator['operate_at'] = array_prepend($this->validator['operate_at'], 'required');
        }
    }

    /**
     * 加入个人信息验证
     */
    private function addPersonalValidator()
    {
        $staffSn = empty($this->staff_sn) ? 'NULL' : $this->staff_sn;
        $validatorPersonal = [
            'realname' => ['required', 'between:2,10'],
            'mobile' => ['required', 'regex:/^1[3456789][0-9]{9}$/', 'unique:staff,mobile,' . $staffSn . ',staff_sn,deleted_at,NULL', 'unique:staff,username,' . $staffSn . ',staff_sn,deleted_at,NULL'],
            'username' => ['max:16', 'unique:staff,username,' . $staffSn . ',staff_sn,deleted_at,NULL', 'unique:staff,mobile,' . $staffSn . ',staff_sn,deleted_at,NULL'],
            'id_card_number' => ['required', 'regex:/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|10|11|12)(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', 'unique:staff,id_card_number,' . $staffSn . ',staff_sn,deleted_at,NULL'],
            'remark' => ['max:200'],
            'account_number' => ['between:16,19'],
            'account_name' => ['between:2,10'],
            'account_bank' => ['max:20'],
            'wechat_number' => ['between:6,20', 'unique:staff,NULL,' . $staffSn . ',staff_sn,deleted_at,NULL'],
            'national' => ['exists:i_national,name'],
            'politics' => ['exists:i_politics,name'],
            'marital_status' => ['exists:i_marital_status,name'],
            'height' => ['integer', 'between:140,220'],
            'weight' => ['integer', 'between:30,150'],
            'native_place' => ['max:30'],
            'education' => ['exists:i_education,name'],
            'dingtalk_number' => ['max:50', 'unique:staff,NULL,' . $staffSn . ',staff_sn,deleted_at,NULL'],
            'concat_name' => ['required', 'between:2,10'],
            'concat_tel' => ['required', 'regex:/^1[3456789]\d{9}$|^0\d{2,3}-\d{5,9}$/'],
            'concat_type' => ['required', 'max:5'],
            'household_address' => ['max:30'],
            'living_address' => ['max:30'],
        ];
        $this->validator = array_collapse([$this->validator, $validatorPersonal]);
    }

    private function confirmIdentityValidator()
    {
        $staffSn = empty($this->staff_sn) ? 'NULL' : $this->staff_sn;
        $this->validator['realname'][] = 'exists:staff,realname,staff_sn,' . $staffSn;
    }

    /**
     * 匹配操作类型
     * @param string $operationType 操作类型
     * @param string /array $match 匹配规则
     * @return boolean
     */
    private function operationMatch($operationType, $match)
    {
        if (empty($operationType)) {
            return true;
        } elseif (is_array($match)) {
            $response = false;
            foreach ($match as $v) {
                if (preg_match('/' . $v . '/', $operationType)) {
                    $response = true;
                }
            }
            return $response;
        } else {
            return preg_match('/' . $match . '/', $operationType);
        }
    }

}
