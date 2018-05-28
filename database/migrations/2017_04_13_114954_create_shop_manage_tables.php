<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopManageTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('shop_sn', 10)->comment('店铺代码');
            $table->char('name', 50)->comment('店铺名称');
            $table->char('manager_sn', 6)->unsigned()->comment('店长员工编号');
            $table->char('manager_name', 10)->comment('店长姓名');
            $table->smallInteger('department_id')->unsigned()->comment('所属部门id');
            $table->tinyInteger('brand_id')->unsigned()->comment('品牌id');
            $table->decimal('lng', 9, 6)->nullable()->comment('经度');
            $table->decimal('lat', 9, 6)->nullable()->comment('纬度');
            $table->mediumInteger('province_id')->comment('省');
            $table->mediumInteger('city_id')->comment('市');
            $table->mediumInteger('county_id')->comment('区/县');
            $table->char('address', 50)->comment('详细地址');
            $table->time('clock_in')->comment('上班时间');
            $table->time('clock_out')->comment('下班时间');
            $table->timestamps();
            $table->softDeletes();
            $table->unique('shop_sn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }

}
