<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTransfer extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('staff_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('staff_sn')->unsigned()->comment('员工编号');
            $table->char('staff_name', 10)->comment('员工姓名');
            $table->char('staff_gender', 10)->comment('性别');
            $table->char('staff_department', 10)->comment('部门名称');
            $table->char('current_shop_sn', 10)->comment('当前店铺代码');
            $table->char('leaving_shop_sn', 10)->comment('调离店铺代码');
            $table->date('left_at')->nullable()->comment('出发时间');
            $table->char('arriving_shop_sn', 10)->comment('到达店铺代码');
            $table->char('arriving_shop_duty', 5)->comment('到达店铺职务');
            $table->date('arrived_at')->nullable()->comment('到达时间');
            $table->tinyInteger('status')->default(0)->comment('调动状态');
            $table->mediumInteger('maker_sn')->unsigned()->comment('创建人编号');
            $table->char('maker_name', 10)->comment('创建人姓名');
            $table->char('remark', 200)->comment('备注');
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
        Schema::dropIfExists('staff_transfer');
    }

}
