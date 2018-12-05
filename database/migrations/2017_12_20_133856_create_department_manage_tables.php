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
            $table->tinyInteger('brand_id')->default(1)->comment('所属品牌ID');
            $table->smallInteger('parent_id')->default(0)->comment('上级部门ID');
            $table->tinyInteger('is_locked')->default(0)->comment('是否加锁');
            $table->tinyInteger('is_public')->default(0)->comment('是否对所有人开放');
            $table->tinyInteger('sort')->default(99)->comment('排序');
            $table->char('manager_sn', 6)->default('')->comment('部门管理者编号');
            $table->char('manager_name', 10)->default('')->comment('部门管理者姓名');

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
