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
            $table->char('assistant_sn', 6)->default('')->comment('助店人编号');
            $table->char('assistant_name', 10)->default('')->comment('助店人姓名');
            $table->char('real_address', 50)->default('')->comment('真实定位地址');
            $table->date('opening_at')->nullable()->comment('开业时间');
            $table->date('end_at')->nullable()->comment('结束时间');

            $table->decimal('total_area', 5, 2)->nullable()->comment('店铺总面积');
            $table->enum('shop_type', ['A', 'B1', 'B2', 'B3', 'C'])->default('A')->comment('店铺类型');
            $table->enum('work_type', ['全班', '倒班'])->default('全班')->comment('上班类型');
            $table->tinyInteger('work_schedule_id')->default(0)->comment('关联工作排班表ID');

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
            $table->dropColumn('assistant_sn');
            $table->dropColumn('assistant_name');
            $table->dropColumn('real_address');
            $table->dropColumn('opening_at');
            $table->dropColumn('end_at');

            $table->dropColumn('total_area');
            $table->dropColumn('shop_type');
            $table->dropColumn('work_type');
            $table->dropColumn('work_schedule_id');
        });

        Schema::dropIfExists('shop_status');
    }
}
