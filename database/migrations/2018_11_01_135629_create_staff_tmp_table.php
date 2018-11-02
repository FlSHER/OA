<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_tmp', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('staff_sn')->unsigned()->comment('被操作员工编号');
            $table->mediumInteger('admin_sn')->unsigned()->comment('管理员编号');
            $table->date('operate_at')->comment('执行日期')->nullable();
            $table->text('changes')->comment('变动明细');
            $table->smallInteger('status')->default(0)->comment('状态: 0-锁定 1-未锁定 2-已还原');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_tmp');
    }
}
