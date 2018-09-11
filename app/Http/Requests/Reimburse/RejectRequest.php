<?php

namespace App\Http\Requests\Reimburse;

use App\Repositories\Reimburse\AuditRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RejectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $auditRepository = new AuditRepository();
        $reimDepartmentIds = $auditRepository->getReimDepartmentId();
        if (empty($reimDepartmentIds)) {
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
        $reimDepartmentIds = $auditRepository->getReimDepartmentId();//资金归属ID
        return [
            'id' => [
                'required',
                Rule::exists('reimburse_mysql.reimbursements')
                    ->where('status_id', 3)
                    ->whereIn('reim_department_id', $reimDepartmentIds)
                    ->whereNull('deleted_at')
            ],
            'remarks' => [
                'required',
                'string'
            ]
        ];
    }

    public function attributes()
    {
        return [
            'id' => '报销单ID',
            'remarks' => '驳回原因'
        ];
    }
}
