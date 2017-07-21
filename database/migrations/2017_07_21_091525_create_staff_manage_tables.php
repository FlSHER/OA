<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffManageTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        /**
         * 员工主表
         */
        Schema::create('staff', function (Blueprint $table) {
            $table->mediumInteger('staff_sn', true)->unsigned()->primary();
            $table->timestamps();
            $table->softDeletes();
        });
        /**
         * 员工详细信息表
         */
        Schema::create('staff_info', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned()->primary();
            $table->timestamps();
            $table->softDeletes();
        });
        /**
         * 关系人表
         */
        Schema::create('staff_relatives', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned()->comment('员工编号');
            $table->mediumInteger('relative_sn')->unsigned()->comment('关联员工编号');
            $table->char('relative_name', 10)->comment('关联员工姓名');
            $table->tinyInteger('relative_type')->unsigned()->comment('关系类型');
            $table->timestamps();
            $table->primary(['staff_sn', 'relative_sn']);
        });
        /**
         * 关系类型表
         */
        Schema::create('staff_relative_type', function (Blueprint $table) {
            $table->tinyInteger('id', true)->unsigned()->primary();
            $table->char('name', 5);
            $table->tinyInteger('group_id')->unsigned()->comment('关系分组');
            $table->tinyInteger('opposite_group_id')->unsigned()->comment('对立分组');
            $table->tinyInteger('gender_id')->unsigned()->default(0)->comment('性别限制');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('排序');
        });
        /**
         * 预约调动临时表
         */
        Schema::create('staff_tmp', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned()->primary();
            $table->smallInteger('department_id')->unsigned()->nullable()->comment('所属部门');
            $table->smallInteger('position_id')->unsigned()->nullable()->comment('职位');
            $table->tinyInteger('brand_id')->unsigned()->nullable()->comment('品牌');
            $table->tinyInteger('status_id')->nullable()->comment('员工状态');
            $table->char('shop_sn', 10)->nullable()->comment('店铺代码');
            $table->date('employed_at')->nullable()->comment('转正时间');
            $table->date('left_at')->nullable()->comment('离职时间');
            $table->date('operate_at')->nullable()->comment('变动时间');
            $table->tinyInteger('is_active')->unsigned()->default(1)->comment('是否激活');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('staff');
        Schema::dropIfExists('staff_info');
        Schema::dropIfExists('staff_relatives');
        Schema::dropIfExists('staff_relative_type');
        Schema::dropIfExists('staff_tmp');
    }

}
