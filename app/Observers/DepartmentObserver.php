<?php
/**
 * Created by PhpStorm.
 * User: Fisher
 * Date: 2018/6/26 0026
 * Time: 11:30
 */

namespace App\Observers;


use App\Models\Department;

class DepartmentObserver
{
    public function saving(Department $department)
    {
        $department->changeFullName();
    }

    public function saved(Department $department)
    {
        $department->changeRoleAuthority();
    }
}