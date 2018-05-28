<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffLeavingTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('staff_leaving', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('staff_sn')->unsigned();
            $table->tinyInteger('original_status_id')->comment('离职后的状态ID');

            $table->text('attendance')->nullable()->comment('考勤');
            $table->mediumInteger('attendance_operator_sn')->unsigned()->nullable()->comment('考勤交接员工编号');
            $table->char('attendance_operator_name', 10)->comment('考勤交接员工姓名')->default('');
            $table->integer('attendance_operate_at')->unsigned()->nullable()->comment('考勤交接时间');

            $table->text('goods')->nullable()->comment('物品回收');
            $table->mediumInteger('goods_operator_sn')->unsigned()->nullable()->comment('物品回收交接员工编号');
            $table->char('goods_operator_name', 10)->comment('物品回收交接员工姓名')->default('');
            $table->integer('goods_operate_at')->unsigned()->nullable()->comment('物品回收交接时间');

            $table->text('punishment')->nullable()->comment('费用扣减');
            $table->mediumInteger('punishment_operator_sn')->unsigned()->nullable()->comment('费用扣减交接员工编号');
            $table->char('punishment_operator_name', 10)->comment('费用扣减交接员工姓名')->default('');
            $table->integer('punishment_operate_at')->unsigned()->nullable()->comment('费用扣减交接时间');

            $table->text('inventory')->nullable()->comment('库存奖罚');
            $table->mediumInteger('inventory_operator_sn')->unsigned()->nullable()->comment('库存奖罚交接员工编号');
            $table->char('inventory_operator_name', 10)->comment('库存奖罚交接员工姓名')->default('');
            $table->integer('inventory_operate_at')->unsigned()->nullable()->comment('库存奖罚交接时间');

            $table->text('software')->nullable()->comment('系统停用');
            $table->mediumInteger('software_operator_sn')->unsigned()->nullable()->comment('系统停用交接员工编号');
            $table->char('software_operator_name', 10)->comment('系统停用交接员工姓名')->default('');
            $table->integer('software_operate_at')->unsigned()->nullable()->comment('系统停用交接时间');

            $table->text('finance')->nullable()->comment('财务清算');
            $table->mediumInteger('finance_operator_sn')->unsigned()->nullable()->comment('财务清算交接员工编号');
            $table->char('finance_operator_name', 10)->comment('财务清算交接员工姓名')->default('');
            $table->integer('finance_operate_at')->unsigned()->nullable()->comment('财务清算交接时间');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('staff_leaving');
    }

}
