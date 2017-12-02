<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_log', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('target_id')->comment('被操作店铺ID');
            $table->mediumInteger('admin_sn')->unsigned()->comment('管理员编号');
            $table->text('changes')->comment('变动明细');
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
        Schema::dropIfExists('shop_log');
    }
}
