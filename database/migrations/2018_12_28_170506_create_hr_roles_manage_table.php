<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrRolesManageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_roles', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->char('name', 10)->comment('角色名');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_staff_has_roles', function (Blueprint $table) {
            $table->mediumInteger('hr_role_id')->unsigned()->comment('角色ID');
            $table->mediumInteger('staff_sn')->unsigned()->comment('员工编号');
            $table->primary(['hr_role_id', 'staff_sn']);
        });

        Schema::create('hr_role_has_brands', function (Blueprint $table) {
            $table->mediumInteger('hr_role_id')->unsigned()->comment('角色ID');
            $table->smallInteger('brand_id')->unsigned()->comment('品牌编号');
            $table->primary(['hr_role_id', 'brand_id']);
        });

        Schema::create('hr_role_has_departments', function (Blueprint $table) {
            $table->mediumInteger('hr_role_id')->unsigned()->comment('角色ID');
            $table->smallInteger('department_id')->unsigned()->comment('部门ID');
            $table->primary(['hr_role_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_rules');
        Schema::dropIfExists('hr_staff_has_roles');
        Schema::dropIfExists('hr_role_has_brands');
        Schema::dropIfExists('hr_role_has_departments');
    }
}
