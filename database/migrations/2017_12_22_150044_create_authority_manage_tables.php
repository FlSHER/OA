<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorityManageTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorities', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->char('auth_name', 10)->comment('权限名称');
            $table->char('access_url', 20)->comment('访问路由');
            $table->mediumInteger('parent_id')->unsigned()->default(0)->comment('上级权限ID');
            $table->tinyInteger('sort')->unsigned()->comment('排序');
            $table->tinyInteger('is_menu')->unsigned()->comment('是否展示为菜单');
            $table->char('menu_name', 10)->comment('菜单名称');
            $table->char('menu_logo', 50)->comment('菜单图标');
            $table->tinyInteger('is_active')->unsigned()->default(0)->comment('是否激活');
            $table->tinyInteger('is_public')->unsigned()->default(0)->comment('是否对所有人开放');
            $table->smallInteger('app_id')->unsigned()->comment('应用ID');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->char('name', 10)->comment('角色姓名');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('role_has_authorities', function (Blueprint $table) {
            $table->mediumInteger('role_id')->unsigned()->comment('角色ID');
            $table->mediumInteger('authority_id')->unsigned()->comment('权限ID');
            $table->primary(['role_id', 'authority_id']);
        });

        Schema::create('role_has_staff', function (Blueprint $table) {
            $table->mediumInteger('role_id')->unsigned()->comment('角色ID');
            $table->mediumInteger('staff_sn')->unsigned()->comment('员工编号');
            $table->primary(['role_id', 'staff_sn']);
        });

        Schema::create('role_has_department', function (Blueprint $table) {
            $table->mediumInteger('role_id')->unsigned()->comment('角色ID');
            $table->smallInteger('department_id')->unsigned()->comment('部门ID');
            $table->primary(['role_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authorities');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_has_authorities');
        Schema::dropIfExists('role_has_staff');
    }
}
