<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffLogTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('staff_log')) {
            Schema::create('staff_log', function (Blueprint $table) {
                $table->increments('id');
                $table->mediumInteger('staff_sn')->unsigned()->comment('被操作员工编号');
                $table->mediumInteger('admin_sn')->unsigned()->comment('管理员编号');
                $table->char('operation_type', 20)->comment('操作类型');
                $table->date('operate_at')->comment('执行日期')->nullable();
                $table->char('operation_remark', 100)->comment('操作备注');
                $table->text('changes')->comment('变动明细');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('staff_log');
    }

}
