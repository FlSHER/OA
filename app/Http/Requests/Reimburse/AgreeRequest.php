<?php

namespace App\Http\Requests\Reimburse;

use App\Repositories\Reimburse\AuditRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgreeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $auditRepository = new AuditRepository();
        $reimDepartmentIds = $auditRepository->getReimDepartmentId();//资金归属ID
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
            'expenses' => [
                'required',
            ],
            'expenses.*.id' => [
                'required',
                Rule::exists('reimburse_mysql.expenses')
                    ->where('is_approved', 1)
                    ->where('reim_id', $this->id)
                    ->whereNull('deleted_at')
            ],
            'expenses.*.audited_cost' => [
                'required',
                'regex:/^[0-9]+(.[0-9]{1,2})?$/'
            ],
        ];
    }

    public function attributes()
    {
        return [
            'id' => "报销单ID",
            'expenses' => '明细',
            'expenses.*.id' => "消费明细ID",
            'expenses.*.audited_cost' => '审核金额',

        ];
    }
}
