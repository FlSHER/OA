<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentManageTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('name', 20)->comment('部门名称');
            $table->char('full_name', 100)->comment('部门全称');
            $table->tinyInteger('brand_id')->unsigned()->comment('所属品牌ID');
            $table->smallInteger('parent_id')->unsigned()->comment('上级部门ID');
            $table->tinyInteger('is_locked')->unsigned()->comment('是否加锁');
            $table->tinyInteger('is_public')->unsigned()->comment('是否对所有人开放');
            $table->tinyInteger('sort')->unsigned()->comment('排序');
            $table->mediumInteger('manager_sn')->unsigned()->comment('店长员工编号');
            $table->char('manager_name', 10)->comment('店长姓名');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
