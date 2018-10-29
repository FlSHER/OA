<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->tinyInteger('status_id')->default(0)->comment('店铺状态');
            $table->char('manager1_sn', 6)->default('')->comment('一级负责人编号');
            $table->char('manager1_name', 10)->default('')->comment('一级负责人姓名');
            $table->char('manager2_sn', 6)->default('')->comment('二级负责人编号');
            $table->char('manager2_name', 10)->default('')->comment('二级负责人姓名');
            $table->char('manager3_sn', 6)->default('')->comment('三级负责人编号');
            $table->char('manager3_name', 10)->default('')->comment('三级负责人姓名');
            $table->time('opening_at')->comment('开业时间');
            $table->time('end_at')->comment('结束时间');

            $table->foreign('status_id')->references('id')->on('shop_status');
        });

        // 店铺状态
        Schema::create('shop_status', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->char('name', 10)->comment('状态名称');
            $table->unsignedTinyInteger('sort')->default(0)->comment('排序');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('status_id');
            $table->dropColumn('manager_sn');
            $table->dropColumn('manager_name');
            $table->dropColumn('manager1_sn');
            $table->dropColumn('manager1_name');
            $table->dropColumn('manager2_sn');
            $table->dropColumn('manager2_name');
            $table->dropColumn('manager3_sn');
            $table->dropColumn('manager3_name');
            $table->dropColumn('opening_at');
            $table->dropColumn('end_at');
        });

        Schema::dropIfExists('shop_status');
    }
}
