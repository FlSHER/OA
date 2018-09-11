<?php

namespace App\Http\Requests\Reimburse;

use App\Repositories\Reimburse\AuditRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $auditRepository = new AuditRepository();
        if (empty($auditRepository->getReimDepartmentId())) {
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
        $auditRepository = new AuditRepository();
        $reimDepartmentIds = $auditRepository->getReimDepartmentId();
        return [
            'id' => [
                'required',
                'array'
            ],
            'id.*' => [
                Rule::exists('reimburse_mysql.reimbursements', 'id')
                    ->where('status_id', 4)
                    ->where('process_instance_id','')
                    ->whereIn('reim_department_id', $reimDepartmentIds)
                    ->whereNull('deleted_at')
            ],
            'remark'=>[
                'string',
                'max:300'
            ]
        ];
    }

    public function attributes()
    {
        return [
            'id' => '报销ID',
            'id.*' => '报销ID',
        ];
    }
}
