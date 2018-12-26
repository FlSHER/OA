<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->tinyInteger('cate_id')->default(0)->comment('关联部门分类');
            $table->mediumInteger('province_id')->default(0)->comment('部门省份');
            $table->char('minister_sn', 6)->default('')->comment('部长员工编号');
            $table->char('minister_name', 10)->default('')->comment('部长姓名');
            $table->char('area_manager_sn', 6)->default('')->comment('区域经理编号');
            $table->char('area_manager_name', 10)->default('')->comment('区域经理姓名');
            $table->char('regional_manager_sn', 6)->default('')->comment('大区经理编号');
            $table->char('regional_manager_name', 10)->default('')->comment('大区经理姓名');
            $table->char('personnel_manager_sn', 6)->default('')->comment('人事负责人编号');
            $table->char('personnel_manager_name', 10)->default('')->comment('人事负责人姓名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('cate_id');
            $table->dropColumn('province_id');
            $table->dropColumn('minister_sn');
            $table->dropColumn('minister_name');
            $table->dropColumn('area_manager_sn');
            $table->dropColumn('area_manager_name');
            $table->dropColumn('regional_manager_sn');
            $table->dropColumn('regional_manager_name');
            $table->dropColumn('personnel_manager_sn');
            $table->dropColumn('personnel_manager_name');
        });
    }
}
