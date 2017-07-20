<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTmpTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
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
        Schema::dropIfExists('staff_tmp');
    }

}
